<?php

namespace App\Http\Controllers;

use App\Models\Affectation;
use App\Models\Assessment;
use App\Models\Inscription;
use App\Models\Lesson;
use App\Models\Note;
use App\Models\Sequence;
use App\Services\ScolariteService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class AssessmentController extends Controller
{
    protected $scolarite;

    protected $actifYear;

    public function __construct(ScolariteService $scolarite)
    {
        $this->scolarite = $scolarite;
        $this->actifYear = $this->scolarite->getactifYear();
    }

    public function index()
    {
        $enseignant = auth()->user()->enseignant;

        if (! $enseignant) {
            return back()->with('error', 'Action impossible : profil enseignant non trouvé.');
        }

        // 1. Récupérer les séquences de l'année active (utilise $this->actifYear)
        $sequences = Sequence::whereHas('trimestre', function ($query) {
            $query->where('annee_scolaire_id', $this->actifYear->id);
        })->get();

        // 2. Affectations de l'enseignant
        // FILTRE AJOUTÉ ICI : annee_scolaire_id sur les affectations
        $affectations = $enseignant->affectations()
            ->where('annee_scolaire_id', $this->actifYear->id)
            ->with(['matiere', 'classe'])
            ->whereHas('classe.matieres', function ($query) {
                $query->whereColumn('matieres.id', 'affectations.matiere_id');
            })
            ->get();

        // 3. Évaluations filtrées STRICTEMENT sur l'année active
        $evaluations = Assessment::with(['classe', 'matiere', 'sequence'])
            ->where('enseignant_id', $enseignant->id)
            ->whereHas('sequence.trimestre', function ($query) {
                $query->where('annee_scolaire_id', $this->actifYear->id);
            })
            ->latest()
            ->get();

        return view('pages.assessments.index', [
            'evaluations' => $evaluations,
            'sequences' => $sequences,
            'actifYear' => $this->actifYear,
            'affectations' => $affectations,
        ]);
    }

    public function saisie($id, ScolariteService $scolariteService)
    {
        $evaluation = Assessment::with(['classe', 'matiere', 'sequence'])->findOrFail($id);

        // --- VERIFICATION DE CLOTURE ---
        $sequence = $evaluation->sequence;

        // 🔒 SECURITE : Vérifie que la séquence appartient à l'année active
        $scolariteService->validateSequence($sequence);

        if ($sequence->is_closed || ($sequence->submission_deadline && now()->greaterThan($sequence->submission_deadline))) {
            return redirect()->route('admin.evaluations.index')
                ->with('error', 'La saisie des notes pour cette évaluation est verrouillée (séquence clôturée ou date limite dépassée).');
        }
        //

        // Conservé : la table 'inscriptions' utilise bien annee_scolaire_id
        $inscriptions = Inscription::where('classe_id', $evaluation->classe_id)
            ->where('annee_scolaire_id', $this->actifYear->id)
            ->with('eleve')
            ->join('eleves', 'inscriptions.eleve_id', '=', 'eleves.id')
            ->orderBy('eleves.nom', 'asc')
            ->orderBy('eleves.prenom', 'asc')
            ->select('inscriptions.*')
            ->get();

        $notesExistantes = Note::where('evaluation_id', $id)
            ->get()
            ->mapWithKeys(function ($item) {
                return [(int) $item->inscription_id => $item];
            });

        // Retrait de 'annee_scolaire_id' qui n'existe pas dans la table 'lecons'
        $lecons = Lesson::where('enseignant_id', $evaluation->enseignant_id)
            ->where('matiere_id', $evaluation->matiere_id)
            ->where('classe_id', $evaluation->classe_id)
            ->orderBy('ordre')
            ->get();

        $leconsEvalueesIds = $evaluation->lecons()->pluck('lecons.id')->toArray();

        return view('pages.assessments.saisie', compact('evaluation', 'inscriptions', 'notesExistantes', 'lecons', 'leconsEvalueesIds'));
    }

    public function store(Request $request, ScolariteService $scolariteService)
    {

        $enseignant = auth()->user()->enseignant;

        if (! $enseignant) {
            return back()->with('error', 'Action impossible : profil enseignant non trouvé.');
        }

        // --- VERIFICATION DE CLOTURE ---
        $sequence = Sequence::findOrFail($request->sequence_id);
        // 🔒 SECURITE : Bloque si la séquence est hors de l'année scolaire active
        $scolariteService->validateSequence($sequence);

        if ($sequence->is_closed) {
            return back()->with('error', "Impossible de créer une évaluation : la période pour la {$sequence->nom} est clôturée par l'administration.");
        }

        if ($sequence->submission_deadline && now()->greaterThan($sequence->submission_deadline)) {
            return back()->with('error', "Impossible de créer une évaluation : la date limite de saisie pour la {$sequence->nom} est dépassée.");
        }

        $affectation = Affectation::findOrFail($request->affectation_id);

        // ICI : On cherche si cette évaluation existe déjà pour ne pas perdre les notes
        $evaluation = Assessment::firstOrCreate(
            [
                'sequence_id' => $request->sequence_id,
                'classe_id' => $affectation->classe_id,
                'matiere_id' => $affectation->matiere_id,
                'enseignant_id' => $enseignant->id,
                'annee_scolaire_id' => $this->actifYear->id, // <--- AJOUTE ÇA ICI
                // On ne met pas 'titre' ou 'date' ici car ils peuvent varier
            ],
            [
                'titre' => $request->titre,
                'date_evaluation' => now(),
            ]
        );

        return redirect()->route('admin.evaluations.saisie', ['id' => $evaluation->id])
            ->with('success', 'Session d\'évaluation prête !');
        // on finit on reste sur la meme page pour le telechargement des stats
        //   return redirect()->back()->with('success', 'Session d\'évaluation prête ! Vous pouvez maintenant saisir les notes ou télécharger les statistiques.');
    }

    public function bulkStoreNotes(Request $request, $id, ScolariteService $scolariteService)
    {
        $evaluation = Assessment::findOrFail($id);

        // --- VERIFICATION DE CLOTURE ---
        $sequence = $evaluation->sequence;
        // 🔒 SECURITE : Contrôle de cohérence de l'année active
        $scolariteService->validateSequence($sequence);
        if ($sequence->is_closed || ($sequence->submission_deadline && now()->greaterThan($sequence->submission_deadline))) {
            return redirect()->route('admin.evaluations.index')
                ->with('error', 'Enregistrement refusé : la période de saisie pour cette séquence est fermée.');
        }

        // 1. Synchroniser les leçons cochées (même si aucune n'est cochée, ça nettoie)
        $evaluation->lecons()->sync($request->input('lesson_ids', []));

        // 2. On vérifie qu'on a bien reçu le tableau 'notes'
        if (! $request->has('notes')) {
            return redirect()->back()->with('error', 'Aucune note n’a été envoyée, mais les leçons ont été mises à jour.');
        }

        foreach ($request->notes as $inscriptionId => $donnees) {
            if (isset($donnees['valeur']) && $donnees['valeur'] !== '') {
                if ($donnees['valeur'] > 20 || $donnees['valeur'] < 0) {
                    return back()->with('error', 'Attention : Une note doit être comprise entre 0 et 20.');
                }

                Note::updateOrCreate(
                    [
                        'evaluation_id' => $evaluation->id,
                        'inscription_id' => $inscriptionId,
                    ],
                    [
                        'valeur' => $donnees['valeur'],
                        'observation' => $donnees['observation'] ?? null,
                    ]
                );
            }
        }

        // return redirect()->route('admin.evaluations.index')
        //     ->with('success', 'Félicitations ! Les notes et les leçons évaluées ont été enregistrées.');
        return redirect()->back()->with('success', 'Félicitations ! Les notes et les leçons évaluées ont été enregistrées. Vous pouvez maintenant télécharger les statistiques de cette évaluation.');
    }

    // /////////////// les diffferents statistiques a gerer pour les impressions

    private function calculerStats($evaluation)
    {
        $notes = $evaluation->notes()->with('inscription.eleve')->get();

        $total = $notes->count();
        $reussites = $notes->filter(fn ($n) => $n->valeur >= 10);

        $garcons = $notes->filter(fn ($n) => $n->inscription->eleve->sexe === 'M');
        $filles = $notes->filter(fn ($n) => $n->inscription->eleve->sexe === 'F');

        $garconsCount = $garcons->count();
        $garconsReussite = $garcons->where('valeur', '>=', 10)->count();

        $fillesCount = $filles->count();
        $fillesReussite = $filles->where('valeur', '>=', 10)->count();

        // Progression des leçons
        $totalLeconsPrevues = Lesson::where('enseignant_id', $evaluation->enseignant_id)
            ->where('matiere_id', $evaluation->matiere_id)
            ->where('classe_id', $evaluation->classe_id)
            ->count();

        $leconsEvalueesIds = Assessment::where('enseignant_id', $evaluation->enseignant_id)
            ->where('matiere_id', $evaluation->matiere_id)
            ->where('classe_id', $evaluation->classe_id)
            ->where('annee_scolaire_id', $evaluation->annee_scolaire_id)
            ->with('lecons')
            ->get()
            ->flatMap(fn ($eval) => $eval->lecons->pluck('id'))
            ->unique()
            ->count();

        $tauxProgression = $totalLeconsPrevues > 0
            ? number_format(($leconsEvalueesIds / $totalLeconsPrevues) * 100, 2)
            : 0;

        return [
            'total' => $total,
            'moyenne' => $total > 0 ? number_format($notes->avg('valeur'), 2) : 0,
            'reussite_globale' => $reussites->count(),
            'taux_reussite' => $total > 0 ? number_format(($reussites->count() / $total) * 100, 2) : 0,

            'garcons_count' => $garconsCount,
            'garcons_reussite' => $garconsReussite,
            'garcons_taux' => $garconsCount > 0 ? number_format(($garconsReussite / $garconsCount) * 100, 1) : 0,

            'filles_count' => $fillesCount,
            'filles_reussite' => $fillesReussite,
            'filles_taux' => $fillesCount > 0 ? number_format(($fillesReussite / $fillesCount) * 100, 1) : 0,

            'lecons_faites' => $leconsEvalueesIds,
            'lecons_totales' => $totalLeconsPrevues,
            'taux_progression' => $tauxProgression,
        ];
    }

    public function telechargerStats($id)
    {
        // On charge les relations nécessaires (plus de .niveau sur la classe)
        $evaluation = Assessment::with(['classe', 'matiere', 'enseignant.user', 'anneeScolaire'])->findOrFail($id);

        $stats = $this->calculerStats($evaluation);

        $data = [
            'evaluation' => $evaluation,
            'stats' => $stats,
            'date_impression' => now()->format('d/m/Y à H:i'),
        ];

        $pdf = Pdf::loadView('pages.assessments.stats_assessment', $data)
            ->setPaper('a4', 'portrait');

        return $pdf->download(str('Statistiques '.$evaluation->matiere->nom)->slug('_').'.pdf');
    }
}
