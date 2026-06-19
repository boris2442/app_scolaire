<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AcademicStatisticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatisticController extends Controller
{
    protected $statsService;

    // Injection automatique du Service par Laravel
    public function __construct(AcademicStatisticsService $statsService)
    {
        $this->statsService = $statsService;
    }

    /**
     * Calcule toute la classe d'un coup via un bouton
     */
    public function générerSequence(Request $request)
    {
        $sequenceId = $request->input('sequence_id');
        $classeId = $request->input('classe_id');
        
        // Récupérer l'ID de l'année scolaire active (ici fixé à 1 pour l'exemple)
        $anneeScolaireId = 1; 

        // 1. Récupérer tous les élèves inscrits dans cette classe spécifique
        $elevesInscrits = DB::table('inscriptions')
            ->where('classe_id', $classeId)
            ->get();

        if ($elevesInscrits->isEmpty()) {
            return redirect()->back()->with('error', 'Aucun élève trouvé dans cette classe.');
        }

        // 2. Lancer la boucle de calcul pour chaque élève
        foreach ($elevesInscrits as $eleve) {
            // Étape A : Calcule les moyennes de chaque matière pour cet élève
            $this->statsService->calculerMoyennesMatieresPourSequence($sequenceId, $eleve->id);

            // Étape B : Combine ces matières pour générer son bilan général
            $this->statsService->calculerBilanGeneralSequence($sequenceId, $eleve->id, $anneeScolaireId);
        }

        // Étape C : Une fois que tout le monde est évalué, on calcule les rangs globaux de la classe
        $this->statsService->attribuerRangsClasseForSequence($sequenceId, $classeId);

        return redirect()->back()->with('success', 'Les statistiques de la classe et les rangs ont été générés avec succès !');
    }
}
