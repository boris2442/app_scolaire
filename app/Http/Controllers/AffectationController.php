<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\BulkStoreRequest;
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
        $classes = Classe::with('niveau')->orderBy('niveau_id')->get();
        $enseignants = Enseignant::with('user')->get();

        $classeId = $request->get('classe_id'); // On change salle_id par classe_id
        $matieresDuNiveau = [];
        $affectationsExistantes = [];



        if ($classeId) {
            // On charge la relation 'matieres' directement depuis la classe
            $classe = Classe::with(['matieres', 'niveau'])->findOrFail($classeId);

            // On récupère les matières de la classe
            $matieresDuNiveau = $classe->matieres;

            $affectationsExistantes = Affectation::where('classe_id', $classeId)
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





    public function bulkStore(BulkStoreRequest $request)
    {
        // 1. Validation (Évite les crashs si les données sont corrompues)
        $request->validated();

        $anneeActive = $this->scolarite->getAnneeActive();
        $classeId = $request->classe_id;
        $donnees = $request->affectations ?? [];

        foreach ($donnees as $matiereId => $enseignantId) {
            if ($enseignantId) {
                // SI UN PROF EST CHOISI : On crée ou on met à jour
                Affectation::updateOrCreate(
                    [
                        'classe_id' => $classeId,
                        'matiere_id' => $matiereId,
                        'annee_scolaire_id' => $anneeActive->id,
                    ],
                    ['enseignant_id' => $enseignantId]
                );
            } else {
                // SI "NON AFFECTÉ" EST CHOISI : On supprime l'affectation existante
                Affectation::where([
                    'classe_id' => $classeId,
                    'matiere_id' => $matiereId,
                    'annee_scolaire_id' => $anneeActive->id,
                ])->delete();
            }
        }

        return redirect()->back()->with('success', 'Tableau de service mis à jour avec succès !');
    }
}
