<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Affectation;
use App\Models\Evaluation;
use App\Models\Inscription;
use App\Models\Note;
use App\Models\Sequence;
use App\Services\ScolariteService;
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

        // OPTION A : Si tu as une relation directe ou via les trimestres
        // On récupère uniquement les séquences dont le trimestre appartient à l'année active
        $sequences = Sequence::whereHas('trimestre', function ($query) use ($anneeActive) {
            $query->where('annee_scolaire_id', $anneeActive->id);
        })->get();

        $affectations = $enseignant
            ? $enseignant->affectations()->with(['matiere', 'classe.niveau'])->get()
            : collect();

        // On récupère les évaluations déjà créées par ce prof
        $evaluations = Evaluation::with(['classe', 'matiere', 'sequence'])
            ->where('enseignant_id', $enseignant?->id)
            ->latest()
            ->get();

        // $test = $affectations->first();
        // dd($test->niveau_id, $test->niveau);

        return view('pages.evaluations.index', compact('evaluations', 'sequences', 'anneeActive', 'affectations'));
    }




    // public function saisie($id)
    // {
    //     $evaluation = Evaluation::findOrFail($id);
    //     $user = auth()->user();

    //     // VERIFICATION : Est-ce que ce prof est bien affecté à cette matière dans cette classe ?
    //     $estAffecte = Affectation::where([
    //         'enseignant_id' => $user->enseignant->id,
    //         'classe_id' => $evaluation->classe_id,
    //         'matiere_id' => $evaluation->matiere_id,
    //         'annee_scolaire_id' => $this->scolarite->getAnneeActive()->id
    //     ])->exists();

    //     if (!$estAffecte && !$user->hasRole('admin')) {
    //         return redirect()->route('dashboard')->with('error', 'Accès refusé : Vous n\'enseignez pas cette matière dans cette classe.');
    //     }

    //     // Si c'est bon, on continue...
    //     // Dans EvaluationController.php
    //     // $inscriptions = Inscription::where('classe_id', $evaluation->classe_id)
    //     //     ->with('eleve')
    //     //     ->get();

    //     // $notesExistantes = Note::where('evaluation_id', $evaluation->id)
    //     //     ->get()
    //     //     ->keyBy('inscription_id');
    //     $inscriptions = Inscription::where('classe_id', $evaluation->classe_id)
    //         ->with('eleve')
    //         ->get();

    //     // ON FORCE LE TYPE : get() puis mapWithKeys pour être sûr que l'ID est un entier
    //     $notesExistantes = Note::where('evaluation_id', $id)
    //         ->get()
    //         ->mapWithKeys(function ($note) {
    //             return [(int)$note->inscription_id => $note];
    //         });


    //     return view('pages.evaluations.saisie', compact('evaluation', 'inscriptions', 'notesExistantes'));
    // }
    // public function saisie($id)
    // {
    //     $evaluation = Evaluation::findOrFail($id);

    //     // 1. On récupère les inscriptions de la classe
    //     $inscriptions = Inscription::where('classe_id', $evaluation->classe_id)->get();
    //     $idsInscriptions = $inscriptions->pluck('id')->toArray();

    //     // 2. On récupère TOUTES les notes qui existent pour cette évaluation
    //     $notesBrutes = Note::where('evaluation_id', $id)->get();

    //     // 3. On indexe par inscription_id
    //     $notesExistantes = $notesBrutes->keyBy('inscription_id');

    //     // DEBUG : Si tu ne vois toujours rien, décommente la ligne suivante pour voir le verdict :
    //     // dd($idsInscriptions, $notesBrutes->toArray());

    //     return view('pages.evaluations.saisie', compact('evaluation', 'inscriptions', 'notesExistantes'));
    // }
    public function saisie($id)
    {
        $evaluation = Evaluation::with('classe.niveau', 'matiere', 'sequence')->findOrFail($id);

        $inscriptions = Inscription::where('classe_id', $evaluation->classe_id)
            ->with('eleve')
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
}
