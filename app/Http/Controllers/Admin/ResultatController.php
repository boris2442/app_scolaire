<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classe;
use App\Models\Sequence;
use App\Models\Trimestre;
use App\Services\MoyenneService;
use App\Services\ScolariteService;
use Illuminate\Http\Request;

class ResultatController extends Controller
{
    protected $moyenneService;
    protected $scolarite;

    public function __construct(MoyenneService $moyenneService, ScolariteService $scolarite)
    {
        $this->moyenneService = $moyenneService;
        $this->scolarite = $scolarite;
    }
    // app/Http/Controllers/Admin/ResultatController.php

    // public function index()
    // {
    //     $classes = Classe::with(['niveau'])->get();
    //     $sequences = Sequence::whereHas('trimestre', function ($q) {
    //         $q->where('annee_scolaire_id', $this->scolarite->getAnneeActive()->id);
    //     })->get();

    //     return view('pages.resultats.index', compact('classes', 'sequences'));
    // }
    public function index()
    {
        $classes = Classe::with(['niveau'])->get();
        $anneeActive = $this->scolarite->getAnneeActive();

        $sequences = Sequence::whereHas('trimestre', function ($q) use ($anneeActive) {
            $q->where('annee_scolaire_id', $anneeActive->id);
        })->get();

        // On récupère aussi les trimestres pour l'affichage
        $trimestres = Trimestre::where('annee_scolaire_id', $anneeActive->id)->get();

        return view('pages.resultats.index', compact('classes', 'sequences', 'trimestres'));
    }



    /**
     * Lance le calcul des résultats pour une classe et une séquence.
     */
    // public function calculer(Request $request)
    // {
    //     $request->validate([
    //         'classe_id' => 'required|exists:classes,id',
    //         'sequence_id' => 'required|exists:sequences,id',
    //     ]);

    //     $anneeActive = $this->scolarite->getAnneeActive();

    //     try {
    //         // Étape 1 : Calcul des moyennes par matière + Rangs par matière
    //         $this->moyenneService->calculerMoyennesSequentielles(
    //             $request->classe_id, 
    //             $request->sequence_id
    //         );

    //         // Étape 2 : Calcul du Bilan Général (Moyenne G + Rang Classe)
    //         $this->moyenneService->genererBilansSequentiels(
    //             $request->classe_id, 
    //             $request->sequence_id,
    //             $anneeActive->id
    //         );

    //         return back()->with('success', 'Les calculs de la séquence ont été générés avec succès !');
    //         // return view('pages.resultats.calculer', [
    //         //     'classeId' => $request->classe_id,
    //         //     'sequenceId' => $request->sequence_id,
    //         //     'anneeScolaireId' => $anneeActive->id,
    //         // ])->with('success', 'Les calculs de la séquence ont été générés avec succès !');a 

    //     } catch (\Exception $e) {
    //         return back()->with('error', 'Erreur lors du calcul : ' . $e->getMessage());
    //     }
    // }


    public function calculer(Request $request)
    {
        // Validation souple : soit sequence_id, soit trimestre_id
        $request->validate([
            'classe_id' => 'required|exists:classes,id',
            'sequence_id' => 'nullable|exists:sequences,id',
            'trimestre_id' => 'nullable|exists:trimestres,id',
        ]);

        $anneeActive = $this->scolarite->getAnneeActive();

        try {
            // CAS 1 : On a choisi une Séquence
            if ($request->filled('sequence_id')) {
                $this->moyenneService->calculerMoyennesSequentielles($request->classe_id, $request->sequence_id);
                $this->moyenneService->genererBilansSequentiels($request->classe_id, $request->sequence_id, $anneeActive->id);
                $message = 'Calculs de la séquence terminés !';
            }
            // CAS 2 : On a choisi un Trimestre
            elseif ($request->filled('trimestre_id')) {
                $this->moyenneService->calculerMoyennesTrimestrielles($request->classe_id, $request->trimestre_id);
                // Optionnel : $this->moyenneService->genererBilansTrimestriels(...) si tu as créé la fonction
                $message = 'Calculs du trimestre terminés !';
            } else {
                return back()->with('error', 'Veuillez sélectionner une séquence ou un trimestre.');
            }

            return back()->with('success', $message);
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur : ' . $e->getMessage());
        }
    }
}
