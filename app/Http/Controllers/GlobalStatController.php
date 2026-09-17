<?php

namespace App\Http\Controllers;

use App\Services\GlobalStatService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class GlobalStatController extends Controller
{
    // ...

    public function imprimerStatsGlobales($trimestreId, GlobalStatService $globalStatService)
    {
        // 1. Vérification de la clôture des séquences du trimestre
        $sequencesNonCloses = DB::table('sequences')
            ->where('trimestre_id', $trimestreId)
            ->where('is_closed', 0)
            ->exists();

        if ($sequencesNonCloses) {
            return redirect()
                ->back()
                ->with('error', 'Toutes les évaluations de ce trimestre doivent être closes pour générer les statistiques globales.');
        }

        // 2. Chargement des données de base
        $etablissement = DB::table('etablissements')->first();
        $trimestre = DB::table('trimestres')->where('id', $trimestreId)->first();
        $anneeActive = DB::table('annee_scolaires')->where('est_active', 1)->first();

        if (! $trimestre || ! $anneeActive) {
            abort(404, 'Trimestre ou Année scolaire active introuvable.');
        }

        // 3. Calcul des statistiques via le service
        $stats = $globalStatService->obtenirStatsGlobales($trimestreId);

        if (empty($stats)) {
            return redirect()->back()->with('error', 'Aucune donnée trouvée pour calculer les statistiques.');
        }

        // 4. Génération du PDF au format A4 Landscape (Paysage)
        $pdf = Pdf::loadView('pages.admin.pdf.stats-globales', compact(
            'etablissement',
            'trimestre',
            'anneeActive',
            'stats'
        ))->setPaper('a4', 'landscape');

        $fileName = str("Statistiques_Globales_{$trimestre->nom}")->slug('_').'.pdf';

        return $pdf->download($fileName);
    }
}
