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


    // public function index()
    // {
    //     // On récupère le profil enseignant de l'utilisateur connecté
    //     $enseignant = auth()->user()->enseignant;
    //     // Dans EvaluationController index()

    //     // On récupère uniquement les évaluations créées par lui
    //     // OU les évaluations des classes/matières qui lui sont affectées
    //     $evaluations = Evaluation::whereHas('matiere', function ($query) use ($enseignant) {
    //         $query->whereIn('id', $enseignant->affectations->pluck('matiere_id'));
    //     })
    //         ->with(['classe', 'matiere', 'sequence'])
    //         ->latest()
    //         ->get();

    //     return view('pages.evaluations.index', compact('evaluations'));
    // }


    // public function index()
    // {
    //     $anneeActive = $this->scolarite->getAnneeActive();
    //     $enseignant = auth()->user()->enseignant;

    //     $sequences = Sequence::all();

    //     $affectations = $enseignant
    //         ? $enseignant->affectations()->with(['matiere', 'niveau', 'classe'])->get()
    //         : collect();

    //     // --- DEBUT DU CODE DE TEST A AJOUTER ---
    //     if ($enseignant && $affectations->count() === 0) {
    //         // On récupère les premiers éléments existants en base pour créer un test
    //         $matiere = \App\Models\Matiere::first();
    //         $classe = \App\Models\Classe::first();
    //         $niveau = \App\Models\Niveau::first();

    //         if ($matiere && $classe && $niveau) {
    //             \App\Models\Affectation::create([
    //                 'enseignant_id'     => $enseignant->id,
    //                 'matiere_id'        => $matiere->id,
    //                 'classe_id'         => $classe->id,
    //                 'niveau_id'         => $niveau->id,
    //                 'annee_scolaire_id' => $anneeActive->id,
    //             ]);
    //             // On rafraîchit la page pour que $affectations ne soit plus vide
    //             return redirect()->refresh();
    //         }
    //     }
    //     // --- FIN DU CODE DE TEST ---

    //     dd([
    //         'Utilisateur_ID'      => auth()->id(),
    //         'role'                => auth()->user()->role,
    //         'Est_Enseignant'      => $enseignant ? 'OUI' : 'NON',
    //         'Nombre_Affectations' => $affectations->count(),
    //         'Donnees'             => $affectations->toArray()
    //     ]);

    //     $evaluations = Evaluation::with(['classe', 'matiere', 'sequence', 'niveau'])
    //         ->latest()
    //         ->get();

    //     return view('pages.evaluations.index', compact('evaluations', 'sequences', 'anneeActive', 'affectations'));
    // }






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





    public function saisie($id)
    {
        $evaluation = Evaluation::findOrFail($id);
        $user = auth()->user();

        // VERIFICATION : Est-ce que ce prof est bien affecté à cette matière dans cette classe ?
        $estAffecte = Affectation::where([
            'enseignant_id' => $user->enseignant->id,
            'classe_id' => $evaluation->classe_id,
            'matiere_id' => $evaluation->matiere_id,
            'annee_scolaire_id' => $this->scolarite->getAnneeActive()->id
        ])->exists();

        if (!$estAffecte && !$user->hasRole('admin')) {
            return redirect()->route('dashboard')->with('error', 'Accès refusé : Vous n\'enseignez pas cette matière dans cette classe.');
        }

        // Si c'est bon, on continue...
        $inscriptions = Inscription::where('classe_id', $evaluation->classe_id)->with('eleve')->get();
        $notesExistantes = Note::where('evaluation_id', $evaluation->id)->get()->keyBy('inscription_id');

        return view('pages.evaluations.saisie', compact('evaluation', 'inscriptions', 'notesExistantes'));
    }



    public function bulkStoreNotes(Request $request, $evaluationId)
    {
        // Plus besoin de chercher l'année ici, elle est déjà dans $this->anneeActive
        foreach ($request->notes as $inscriptionId => $valeur) {
            if ($valeur !== null) {
                Note::updateOrCreate(
                    [
                        'evaluation_id' => $evaluationId,
                        'inscription_id' => $inscriptionId,
                        // On peut même imaginer une sécurité ici si besoin
                    ],
                    ['valeur' => $valeur]
                );
            }
        }
        return back()->with('success', "Notes enregistrées pour l'année {$this->anneeActive->libelle}");
    }



    public function store(Request $request)
    {
        // 1. On récupère l'enseignant lié à l'utilisateur connecté
        $enseignant = auth()->user()->enseignant;

        // SECURITÉ : Si l'utilisateur n'est pas un enseignant, on bloque
        if (!$enseignant) {
            return back()->with('error', "Action impossible : profil enseignant non trouvé.");
        }

        $affectation = Affectation::findOrFail($request->affectation_id);

        // 2. Création avec TOUTES les colonnes obligatoires
        Evaluation::create([
            'titre'         => $request->titre,
            'sequence_id'   => $request->sequence_id,
            'classe_id'     => $affectation->classe_id, // Récupéré via l'affectation
            'matiere_id'    => $affectation->matiere_id, // Récupéré via l'affectation
            'enseignant_id' => $enseignant->id,         // C'EST CETTE LIGNE QUI MANQUAIT !
            'date_evaluation' => now(),
        ]);

        return redirect()->route('admin.evaluations.index')->with('success', 'Évaluation initialisée !');
    }
}
