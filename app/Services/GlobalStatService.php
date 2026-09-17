<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class GlobalStatService
{
    /**
     * Calcule les statistiques globales pour un trimestre donné de manière ultra-fluide.
     */
    public function obtenirStatsGlobales(int $trimestreId): array
    {
        $anneeActive = DB::table('annee_scolaires')->where('est_active', 1)->first();

        if (! $anneeActive) {
            return [];
        }

        $sequences = DB::table('sequences')
            ->where('trimestre_id', $trimestreId)
            ->pluck('id');

        if ($sequences->isEmpty()) {
            return [];
        }

        // 1. Sous-requête SQL optimisée : calcule la moyenne exacte par élève évalué
        $subQuery = DB::table('moyennes')
            ->join('inscriptions', 'moyennes.inscription_id', '=', 'inscriptions.id')
            ->join('eleves', 'inscriptions.eleve_id', '=', 'eleves.id')
            ->join('classes', 'inscriptions.classe_id', '=', 'classes.id')
            ->leftJoin('cycles', 'classes.cycle_id', '=', 'cycles.id')
            ->where('inscriptions.annee_scolaire_id', $anneeActive->id)
            ->whereIn('moyennes.sequence_id', $sequences)
            ->select(
                'inscriptions.id as inscription_id',
                'eleves.nom',
                'eleves.prenom',
                'classes.nom as classe_nom',
                'classes.section',
                DB::raw("COALESCE(cycles.nom, 'Non Défini') as cycle_nom"),
                // Extraction SQL native du niveau (ex: "4e" depuis "4e All")
                DB::raw("SUBSTRING_INDEX(classes.nom, ' ', 1) as niveau"),
                DB::raw('ROUND(SUM(moyennes.total_points) / NULLIF(SUM(moyennes.coefficient), 0), 2) as moyenne_trimestre')
            )
            ->groupBy(
                'inscriptions.id',
                'eleves.nom',
                'eleves.prenom',
                'classes.nom',
                'classes.section',
                'cycles.nom'
            )
            ->havingRaw('SUM(moyennes.coefficient) > 0');

        // 2. Requête source sur la sous-requête
        $baseQuery = DB::table(DB::raw("({$subQuery->toSql()}) as stats_eleves"))
            ->mergeBindings($subQuery);

        // 3. Extraction groupée via SQL direct
        return [
            'etablissement' => $this->genererAgregatSQL($baseQuery),
            'sections' => $this->genererAgregatsParColonne($baseQuery, 'section'),
            'cycles' => $this->genererAgregatsParColonne($baseQuery, 'cycle_nom'),
            'niveaux' => $this->genererAgregatsParColonne($baseQuery, 'niveau'),
        ];
    }

    /**
     * Génère les totaux, admis, taux et moyennes en 1 seule requête SQL.
     */
    /**
     * Génère les totaux, admis, taux et moyennes en 1 seule requête SQL.
     */
    private function genererAgregatSQL($query): array
    {
        $stats = (clone $query)
            ->select(
                DB::raw('COUNT(*) as evalues'),
                DB::raw('SUM(CASE WHEN moyenne_trimestre >= 10 THEN 1 ELSE 0 END) as admis'),
                DB::raw('ROUND(AVG(moyenne_trimestre), 2) as moyenne_generale')
            )
            ->first();

        if (! $stats || $stats->evalues == 0) {
            return [
                'total_eleves' => 0,
                'evalues' => 0,
                'admis' => 0,
                'echecs' => 0,
                'taux_reussite' => 0,
                'moyenne_generale' => 0,
                'major' => null,
                'dernier' => null,
            ];
        }

        $evalues = (int) $stats->evalues;
        $admis = (int) $stats->admis;

        return [
            'total_eleves' => $evalues, // Alignement avec le template Blade
            'evalues' => $evalues,
            'admis' => $admis,
            'echecs' => $evalues - $admis,
            'taux_reussite' => round(($admis / $evalues) * 100, 2),
            'moyenne_generale' => (float) $stats->moyenne_generale,
            'major' => (clone $query)->orderByDesc('moyenne_trimestre')->first(),
            'dernier' => (clone $query)->orderBy('moyenne_trimestre')->first(),
        ];
    }

    /**
     * Groupe par colonne (section, cycle_nom, niveau) directement en SQL.
     */
    private function genererAgregatsParColonne($query, string $colonne): array
    {
        $groupes = (clone $query)->distinct()->pluck($colonne);
        $resultats = [];

        foreach ($groupes as $groupe) {
            if ($groupe) {
                $queryGroupe = (clone $query)->where($colonne, $groupe);
                $resultats[$groupe] = $this->genererAgregatSQL($queryGroupe);
            }
        }

        return $resultats;
    }
}
