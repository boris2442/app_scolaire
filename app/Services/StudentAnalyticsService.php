<?php

namespace App\Services;

use App\Models\Inscription;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class StudentAnalyticsService
{
    // app/Services/StudentAnalyticsService.php

    public function getFullDashboardStats($anneeId)
    {
        return Cache::remember("student_stats_year_{$anneeId}", 1800, function () use ($anneeId) {
            // Optimisation : On ne fait qu'une seule lecture pour le global
            $global = Inscription::where('inscriptions.annee_scolaire_id', $anneeId)
                ->join('eleves', 'inscriptions.eleve_id', '=', 'eleves.id')
                ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN UPPER(eleves.sexe) LIKE 'M%' THEN 1 ELSE 0 END) as garcons,
                SUM(CASE WHEN UPPER(eleves.sexe) LIKE 'F%' THEN 1 ELSE 0 END) as filles
            ")
                ->first();

            $total = (int) ($global->total ?? 0);

            // Récupération des cycles
            $cycles = Inscription::where('inscriptions.annee_scolaire_id', $anneeId)
                ->join('classes', 'inscriptions.classe_id', '=', 'classes.id')
                ->join('niveaux', 'classes.niveau_id', '=', 'niveaux.id')
                ->select('niveaux.nom as cycle', DB::raw('count(*) as total'))
                ->groupBy('niveaux.nom')
                ->get()
                ->toArray();

            // Calcul croissance simplifié
            $anciensCount = Inscription::where('annee_scolaire_id', '<', $anneeId)->distinct('eleve_id')->count();
            $taux = $total > 0 ? round((($total - $anciensCount) / $total) * 100) : 0;

            return [
                'total'    => $total,
                'garcons'  => (int) ($global->garcons ?? 0),
                'filles'   => (int) ($global->filles ?? 0),
                'nouveaux' => $taux,
                'cycles'   => $cycles
            ];
        });
    }

    private function getGlobalStats($anneeId)
    {
        return Inscription::where('inscriptions.annee_scolaire_id', $anneeId) // Préfixe ajouté
            ->join('eleves', 'inscriptions.eleve_id', '=', 'eleves.id')
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN eleves.sexe = 'M' THEN 1 ELSE 0 END) as garcons,
                SUM(CASE WHEN eleves.sexe = 'F' THEN 1 ELSE 0 END) as filles
            ")
            ->first();
    }

    private function getStatsParCycle($anneeId)
    {
        return Inscription::where('inscriptions.annee_scolaire_id', $anneeId) // Préfixe ajouté
            ->join('classes', 'inscriptions.classe_id', '=', 'classes.id')
            ->join('niveaux', 'classes.niveau_id', '=', 'niveaux.id')
            ->select('niveaux.nom as cycle', DB::raw('count(*) as total'))
            ->groupBy('niveaux.nom')
            ->get();
    }

    private function getStatsParClasse($anneeId)
    {
        return Inscription::where('inscriptions.annee_scolaire_id', $anneeId) // Préfixe ajouté
            ->join('classes', 'inscriptions.classe_id', '=', 'classes.id')
            ->select('classes.nom as classe', DB::raw('count(*) as total'))
            ->groupBy('classes.nom')
            ->orderBy('total', 'desc')
            ->get();
    }

    private function getTauxNouveaux($anneeId)
    {
        $totalCetteAnnee = Inscription::where('annee_scolaire_id', $anneeId)->count();

        if ($totalCetteAnnee == 0) return 0;

        // On précise aussi ici pour éviter toute erreur future
        $anciensIds = Inscription::where('annee_scolaire_id', '<', $anneeId)
            ->distinct()
            ->pluck('eleve_id')
            ->toArray();

        $nouveauxCount = Inscription::where('annee_scolaire_id', $anneeId)
            ->whereNotIn('eleve_id', $anciensIds)
            ->count();

        return round(($nouveauxCount / $totalCetteAnnee) * 100, 1);
    }
}
