<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CalculRequest;
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


    public function calculer(CalculRequest $request)
    {
        // Validation souple : soit sequence_id, soit trimestre_id
        $request->validated();

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
