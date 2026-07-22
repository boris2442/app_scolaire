<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Affectation;
use App\Models\Evaluation;
use App\Models\Inscription;
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
        // dd($enseignant);
        if (!$enseignant) {
            return back()->with('error', "Action impossible : profil enseignant non trouvé.");
        }

        // OPTION A : Si tu as une relation directe ou via les trimestres
        // On récupère uniquement les séquences dont le trimestre appartient à l'année active
        $sequences = Sequence::whereHas('trimestre', function ($query) use ($anneeActive) {
            $query->where('annee_scolaire_id', $anneeActive->id);
        })->get();

        $affectations = $enseignant->affectations()
            ->with(['matiere', 'classe.niveau'])
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
        // dd($evaluations);
        // $test = $affectations->first();
        // dd($test->niveau_id, $test->niveau);

        return view('pages.evaluations.index', compact('evaluations', 'sequences', 'anneeActive', 'affectations'));
    }



    public function saisie($id)
    {
        $evaluation = Evaluation::with('classe.niveau', 'matiere', 'sequence')->findOrFail($id);

        // $inscriptions = Inscription::where('classe_id', $evaluation->classe_id)
        //     ->with('eleve')
        //     ->get();

        $inscriptions = Inscription::where('classe_id', $evaluation->classe_id)
            ->with('eleve')
            ->join('eleves', 'inscriptions.eleve_id', '=', 'eleves.id')
            ->orderBy('eleves.nom', 'asc')
            ->orderBy('eleves.prenom', 'asc')
            ->select('inscriptions.*') // Évite les conflits de colonnes si les tables partagent des noms identiques (comme 'id' ou 'created_at')
            ->get();



        // On force l'indexation par l'ID d'inscription en tant qu'entier
        $notesExistantes = Note::where('evaluation_id', $id)
            ->get()
            ->mapWithKeys(function ($item) {
                return [(int)$item->inscription_id => $item];
            });

        return view('pages.evaluations.saisie', compact('evaluation', 'inscriptions', 'notesExistantes'));
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
    }


    //function pour enregistrer les notes en masse
    public function bulkStoreNotes(Request $request, $id)
    {

        $evaluation = Evaluation::findOrFail($id);

        // 1. On vérifie qu'on a bien reçu le tableau 'notes'
        if (!$request->has('notes')) {
            return redirect()->back()->with('error', 'Aucune donnée n’a été envoyée.');
        }

        foreach ($request->notes as $inscriptionId => $donnees) {

            // 2. On vérifie si 'valeur' existe ET n'est pas vide dans ce sous-tableau
            if (isset($donnees['valeur']) && $donnees['valeur'] !== "") {


                // Dans ton contrôleur, avant d'enregistrer
                if ($donnees['valeur'] > 20 || $donnees['valeur'] < 0) {
                    return back()->with('error', 'Attention : Une note doit être comprise entre 0 et 20.');
                }

                Note::updateOrCreate(
                    [
                        'evaluation_id'  => $evaluation->id,
                        'inscription_id' => $inscriptionId,
                    ],
                    [
                        // TRÈS IMPORTANT : On pointe précisément vers 'valeur' 
                        // et non vers tout le tableau $donnees
                        'valeur'      => $donnees['valeur'],
                        'observation' => $donnees['observation'] ?? null,
                    ]
                );
            }
        }

        return redirect()->route('admin.evaluations.index')
            ->with('success', 'Félicitations ! Les notes ont été enregistrées.');
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

        return [
            'total' => $total,
            'moyenne' => $total > 0 ? number_format($notes->avg('valeur'), 2) : 0,
            'reussite_globale' => $reussites->count(),
            'taux_reussite' => $total > 0 ? number_format(($reussites->count() / $total) * 100, 2) : 0,

            'garcons_count' => $garcons->count(),
            'garcons_reussite' => $garcons->where('valeur', '>=', 10)->count(),

            'filles_count' => $filles->count(),
            'filles_reussite' => $filles->where('valeur', '>=', 10)->count(),
        ];
    }




    public function telechargerStats($id)
    {
        // On appelle les relations qu'on vient de définir
        $evaluation = Evaluation::with(['classe.niveau', 'matiere', 'enseignant.user', 'anneeScolaire'])->findOrFail($id);

        $stats = $this->calculerStats($evaluation);

        $data = [
            'evaluation' => $evaluation,
            'stats' => $stats,
            'date_impression' => now()->format('d/m/Y à H:i')
        ];

        $pdf = Pdf::loadView('pages.evaluations.stats_evaluation', $data)
            ->setPaper('a4', 'portrait');;

        return $pdf->download('Statistiques_' . $evaluation->matiere->nom . '.pdf');
    }
}
