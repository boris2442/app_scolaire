<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\BulkStoreRequest;
use App\Models\Affectation;

use App\Models\Classe;
use App\Models\Enseignant;
use App\Models\Teacher;
use App\Models\Year;
use App\Services\ScolariteService;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    protected $scolarite;

    public function __construct(ScolariteService $scolarite)
    {
        $this->scolarite = $scolarite;
    }


    public function index(Request $request)
    {
        // 1. Récupérer l'année passée en paramètre ou prendre celle en session par défaut
        $anneeId = $request->get('annee_scolaire_id');

        if ($anneeId) {
            // Logique si l'année est transmise explicitement
            $anneeActive = Year::findOrFail($anneeId);
        } else {
            $anneeActive = $this->scolarite->getAnneeActive();
        }

        $classes = Classe::orderBy('nom')->get();
        // $enseignants = Enseignant::with('user')
        //     ->orderBy('users.name')
        //     ->get();
        $enseignants = Teacher::query()
            ->join('users', 'enseignants.user_id', '=', 'users.id')
            ->orderBy('users.name', 'asc')
            ->select('enseignants.*') // Évite les conflits d'ID entre les deux tables
            ->with('user')
            ->get();

        $classeId = $request->get('classe_id');
        $matieresDuNiveau = [];
        $affectationsExistantes = [];

        if ($classeId) {
            $classe = Classe::with(['matieres'])->findOrFail($classeId);
            $matieresDuNiveau = $classe->matieres;

            // Filtrage précis par classe ET année active
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


    // public function bulkStore(BulkStoreRequest $request)
    // {
    //     // 1. Validation (Évite les crashs si les données sont corrompues)
    //     $request->validated();

    //     $anneeActive = $this->scolarite->getAnneeActive();
    //     $classeId = $request->classe_id;
    //     $donnees = $request->affectations ?? [];

    //     foreach ($donnees as $matiereId => $enseignantId) {
    //         if ($enseignantId) {
    //             // SI UN PROF EST CHOISI : On crée ou on met à jour
    //             Affectation::updateOrCreate(
    //                 [
    //                     'classe_id' => $classeId,
    //                     'matiere_id' => $matiereId,
    //                     'annee_scolaire_id' => $anneeActive->id,
    //                 ],
    //                 ['enseignant_id' => $enseignantId]
    //             );
    //         } else {
    //             // SI "NON AFFECTÉ" EST CHOISI : On supprime l'affectation existante
    //             Affectation::where([
    //                 'classe_id' => $classeId,
    //                 'matiere_id' => $matiereId,
    //                 'annee_scolaire_id' => $anneeActive->id,
    //             ])->delete();
    //         }
    //     }

    //     return redirect()->back()->with('success', 'Tableau de service mis à jour avec succès !');
    // }


    public function bulkStore(BulkStoreRequest $request)
    {
        $request->validated();

        // Utiliser l'année soumise dans le formulaire prioritairement
        $anneeId = $request->input('annee_scolaire_id', $this->scolarite->getAnneeActive()->id);
        $classeId = $request->classe_id;
        $donnees = $request->affectations ?? [];

        foreach ($donnees as $matiereId => $enseignantId) {
            if ($enseignantId) {
                Affectation::updateOrCreate(
                    [
                        'classe_id' => $classeId,
                        'matiere_id' => $matiereId,
                        'annee_scolaire_id' => $anneeId,
                    ],
                    ['enseignant_id' => $enseignantId]
                );
            } else {
                Affectation::where([
                    'classe_id' => $classeId,
                    'matiere_id' => $matiereId,
                    'annee_scolaire_id' => $anneeId,
                ])->delete();
            }
        }

        return redirect()->back()->with('success', 'Tableau de service mis à jour avec succès !');
    }
}
