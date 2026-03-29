<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Affectation;
use App\Models\Evaluation;
use App\Models\Inscription;
use App\Services\ScolariteService;
use Illuminate\Http\Request;
use App\Models\Note;

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
        // On récupère le profil enseignant de l'utilisateur connecté
        $enseignant = auth()->user()->enseignant;

        // On récupère uniquement les évaluations créées par lui
        // OU les évaluations des classes/matières qui lui sont affectées
        $evaluations = Evaluation::whereHas('matiere', function ($query) use ($enseignant) {
            $query->whereIn('id', $enseignant->affectations->pluck('matiere_id'));
        })
            ->with(['classe', 'matiere', 'sequence'])
            ->latest()
            ->get();

        return view('pages.evaluations.index', compact('evaluations'));
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
        $request->validate([
            'titre' => 'required|string',
            'sequence_id' => 'required|exists:sequences,id',
            'matiere_id' => 'required|exists:matieres,id',
            'classe_id' => 'required|exists:classes,id',
        ]);

        // On crée l'évaluation (le conteneur des notes)
        $evaluation = Evaluation::create([
            'titre' => $request->titre,
            'sequence_id' => $request->sequence_id,
            'matiere_id' => $request->matiere_id,
            'classe_id' => $request->classe_id,
        ]);

        // On redirige vers la page de saisie des notes pour cette évaluation
        return redirect()->route('admin.evaluations.saisie', $evaluation->id);
    }
}
