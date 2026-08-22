<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Affectation;
use App\Models\Evaluation;
use App\Models\Inscription;
use App\Models\Lecon;
use App\Models\Note;
use App\Models\Sequence;
use App\Services\ScolariteService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EvaluationController extends Controller
{
    protected $scolarite;
    protected $anneeActive;

    public function __construct(ScolariteService $scolarite)
    {
        $this->scolarite = $scolarite;
        $this->anneeActive = $this->scolarite->getAnneeActive();
    }







    public function index()
    {
        $anneeActive = $this->scolarite->getAnneeActive();
        $enseignant = auth()->user()->enseignant;

        if (!$enseignant) {
            return back()->with('error', "Action impossible : profil enseignant non trouvé.");
        }

        // On récupère uniquement les séquences dont le trimestre appartient à l'année active
        $sequences = Sequence::whereHas('trimestre', function ($query) use ($anneeActive) {
            $query->where('annee_scolaire_id', $anneeActive->id);
        })->get();

        $affectations = $enseignant->affectations()
            ->with(['matiere', 'classe']) // MODIFICATION ICI : On enlève '.niveau'
            ->whereHas('classe.matieres', function ($query) {
                // Cette condition filtre uniquement les affectations dont la matière 
                // existe réellement dans la configuration de la classe
                $query->whereColumn('matieres.id', 'affectations.matiere_id');
            })
            ->get();

        // On récupère les évaluations déjà créées par ce prof
        $evaluations = Evaluation::with(['classe', 'matiere', 'sequence'])
            ->where('enseignant_id', $enseignant?->id)
            ->latest()
            ->get();

        return view('pages.evaluations.index', compact('evaluations', 'sequences', 'anneeActive', 'affectations'));
    }





    public function saisie($id)
    {
        // MODIFICATION ICI : On enlève '.niveau' de la relation 'classe'
        $evaluation = Evaluation::with(['classe', 'matiere', 'sequence'])->findOrFail($id);

        $inscriptions = Inscription::where('classe_id', $evaluation->classe_id)
            ->with('eleve')
            ->join('eleves', 'inscriptions.eleve_id', '=', 'eleves.id')
            ->orderBy('eleves.nom', 'asc')
            ->orderBy('eleves.prenom', 'asc')
            ->select('inscriptions.*') // Évite les conflits de colonnes si les tables partagent des noms identiques
            ->get();

        // On force l'indexation par l'ID d'inscription en tant qu'entier
        $notesExistantes = Note::where('evaluation_id', $id)
            ->get()
            ->mapWithKeys(function ($item) {
                return [(int)$item->inscription_id => $item];
            });

        // Récupérer les leçons de cette matière, de cette classe et de cet enseignant
        $lecons = Lecon::where('enseignant_id', $evaluation->enseignant_id)
            ->where('matiere_id', $evaluation->matiere_id)
            ->where('classe_id', $evaluation->classe_id)
            ->orderBy('ordre')
            ->get();

        // Récupérer les IDs des leçons déjà cochées/associées à cette évaluation (s'il y en a)
        $leconsEvalueesIds = $evaluation->lecons()->pluck('lecons.id')->toArray();

        return view('pages.evaluations.saisie', compact('evaluation', 'inscriptions', 'notesExistantes', 'lecons', 'leconsEvalueesIds'));
    }

    public function store(Request $request)
    {

        $enseignant = auth()->user()->enseignant;

        if (!$enseignant) {
            return back()->with('error', "Action impossible : profil enseignant non trouvé.");
        }

        $affectation = Affectation::findOrFail($request->affectation_id);

        // ICI : On cherche si cette évaluation existe déjà pour ne pas perdre les notes
        $evaluation = Evaluation::firstOrCreate(
            [
                'sequence_id'   => $request->sequence_id,
                'classe_id'     => $affectation->classe_id,
                'matiere_id'    => $affectation->matiere_id,
                'enseignant_id' => $enseignant->id,
                'annee_scolaire_id' => $this->anneeActive->id, // <--- AJOUTE ÇA ICI
                // On ne met pas 'titre' ou 'date' ici car ils peuvent varier
            ],
            [
                'titre'           => $request->titre,
                'date_evaluation' => now(),
            ]
        );

        return redirect()->route('admin.evaluations.saisie', ['id' => $evaluation->id])
            ->with('success', 'Session d\'évaluation prête !');
        //on finit on reste sur la meme page pour le telechargement des stats
        //   return redirect()->back()->with('success', 'Session d\'évaluation prête ! Vous pouvez maintenant saisir les notes ou télécharger les statistiques.');
    }





    public function bulkStoreNotes(Request $request, $id)
    {
        $evaluation = Evaluation::findOrFail($id);

        // 1. Synchroniser les leçons cochées (même si aucune n'est cochée, ça nettoie)
        $evaluation->lecons()->sync($request->input('lesson_ids', []));

        // 2. On vérifie qu'on a bien reçu le tableau 'notes'
        if (!$request->has('notes')) {
            return redirect()->back()->with('error', 'Aucune note n’a été envoyée, mais les leçons ont été mises à jour.');
        }

        foreach ($request->notes as $inscriptionId => $donnees) {
            if (isset($donnees['valeur']) && $donnees['valeur'] !== "") {
                if ($donnees['valeur'] > 20 || $donnees['valeur'] < 0) {
                    return back()->with('error', 'Attention : Une note doit être comprise entre 0 et 20.');
                }

                Note::updateOrCreate(
                    [
                        'evaluation_id'  => $evaluation->id,
                        'inscription_id' => $inscriptionId,
                    ],
                    [
                        'valeur'      => $donnees['valeur'],
                        'observation' => $donnees['observation'] ?? null,
                    ]
                );
            }
        }

        // return redirect()->route('admin.evaluations.index')
        //     ->with('success', 'Félicitations ! Les notes et les leçons évaluées ont été enregistrées.');
        return redirect()->back()->with('success', 'Félicitations ! Les notes et les leçons évaluées ont été enregistrées. Vous pouvez maintenant télécharger les statistiques de cette évaluation.');
    }










    ///////////////// les diffferents statistiques a gerer pour les impressions




    private function calculerStats($evaluation)
    {
        // On charge les notes, puis l'inscription, puis l'élève
        $notes = $evaluation->notes()->with('inscription.eleve')->get();

        $total = $notes->count();
        $reussites = $notes->filter(fn($n) => $n->valeur >= 10);

        $garcons = $notes->filter(fn($n) => $n->inscription->eleve->sexe === 'M');
        $filles = $notes->filter(fn($n) => $n->inscription->eleve->sexe === 'F');

        // --- CALCUL DE LA PROGRESSION DES LEÇONS ---
        // 1. Nombre total de leçons prévues pour cette matière, classe et enseignant
        $totalLeconsPrevues = Lecon::where('enseignant_id', $evaluation->enseignant_id)
            ->where('matiere_id', $evaluation->matiere_id)
            ->where('classe_id', $evaluation->classe_id)
            ->count();

        // 2. Nombre de leçons uniques déjà évaluées (via toutes les évaluations de cette matière/classe/prof)
        // On récupère les IDs de toutes les leçons liées aux évaluations de ce contexte pour éviter les doublons
        $leconsEvalueesIds = Evaluation::where('enseignant_id', $evaluation->enseignant_id)
            ->where('matiere_id', $evaluation->matiere_id)
            ->where('classe_id', $evaluation->classe_id)
            ->where('annee_scolaire_id', $evaluation->annee_scolaire_id)
            ->with('lecons')
            ->get()
            ->flatMap(function ($eval) {
                return $eval->lecons->pluck('id');
            })
            ->unique()
            ->count();

        // 3. Calcul du taux de progression
        $tauxProgression = $totalLeconsPrevues > 0
            ? number_format(($leconsEvalueesIds / $totalLeconsPrevues) * 100, 2)
            : 0;

        return [
            'total' => $total,
            'moyenne' => $total > 0 ? number_format($notes->avg('valeur'), 2) : 0,
            'reussite_globale' => $reussites->count(),
            'taux_reussite' => $total > 0 ? number_format(($reussites->count() / $total) * 100, 2) : 0,

            'garcons_count' => $garcons->count(),
            'garcons_reussite' => $garcons->where('valeur', '>=', 10)->count(),

            'filles_count' => $filles->count(),
            'filles_reussite' => $filles->where('valeur', '>=', 10)->count(),

            // --- ON AJOUTE LES DONNÉES DE PROGRESSION ICI ---
            'lecons_faites' => $leconsEvalueesIds,
            'lecons_totales' => $totalLeconsPrevues,
            'taux_progression' => $tauxProgression,
        ];
    }







    public function telechargerStats($id)
    {
        // On charge les relations nécessaires (plus de .niveau sur la classe)
        $evaluation = Evaluation::with(['classe', 'matiere', 'enseignant.user', 'anneeScolaire'])->findOrFail($id);

        $stats = $this->calculerStats($evaluation);

        $data = [
            'evaluation' => $evaluation,
            'stats' => $stats,
            'date_impression' => now()->format('d/m/Y à H:i')
        ];

        $pdf = Pdf::loadView('pages.evaluations.stats_evaluation', $data)
            ->setPaper('a4', 'portrait');

        return $pdf->download('Statistiques_' . $evaluation->matiere->nom . '.pdf');
    }
}
