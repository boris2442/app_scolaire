<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Affectation;
use App\Models\Classe;
use App\Models\Enseignant;
use App\Services\ScolariteService;
use Illuminate\Http\Request;

class AffectationController extends Controller
{
    protected $scolarite;

    public function __construct(ScolariteService $scolarite)
    {
        $this->scolarite = $scolarite;
    }

    /**
     * Affiche la matrice d'affectation pour une salle spécifique
     */
    public function index(Request $request)
    {
        $anneeActive = $this->scolarite->getAnneeActive();

        // On récupère tes classes (A, B, C...) avec leur niveau (6e, 5e...)
        $classes = \App\Models\Classe::with('niveau')->orderBy('niveau_id')->get();
        $enseignants = \App\Models\Enseignant::with('user')->get();

        $classeId = $request->get('classe_id'); // On change salle_id par classe_id
        $matieresDuNiveau = [];
        $affectationsExistantes = [];

        if ($classeId) {
            $classe = \App\Models\Classe::with('niveau.matieres')->findOrFail($classeId);

            // On récupère les matières liées au niveau (ex: 6e) de cette classe (ex: 6e A)
            $matieresDuNiveau = $classe->niveau->matieres;

            $affectationsExistantes = \App\Models\Affectation::where('classe_id', $classeId) // Adapté ici aussi
                ->where('annee_scolaire_id', $anneeActive->id)
                ->get()
                ->keyBy('matiere_id');
        }

        return view('pages.affectations.index', compact(
            'classes',
            'enseignants',
            'anneeActive',
            'classeId',
            'matieresDuNiveau',
            'affectationsExistantes'
        ));
    }

    /**
     * Enregistre ou met à jour une affectation
     */
    public function store(Request $request)
    {
        $request->validate([
            'salle_id' => 'required|exists:salles,id',
            'matiere_id' => 'required|exists:matieres,id',
            'enseignant_id' => 'required|exists:enseignants,id',
        ]);

        $anneeActive = $this->scolarite->getAnneeActive();

        // On utilise updateOrCreate pour éviter les doublons
        Affectation::updateOrCreate(
            [
                'salle_id' => $request->salle_id,
                'matiere_id' => $request->matiere_id,
                'annee_scolaire_id' => $anneeActive->id,
            ],
            [
                'enseignant_id' => $request->enseignant_id,
            ]
        );

        return redirect()->back()->with('success', 'Affectation mise à jour avec succès.');
    }
}
