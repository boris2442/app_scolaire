<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sequence;
use App\Services\ScolariteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatistiqueController extends Controller
{
    protected $scolarite;

    public function __construct(ScolariteService $scolarite)
    {
        $this->scolarite = $scolarite;
    }

    public function index(Request $request)
    {
        $anneeId = $this->scolarite->getAnneeActive()->id;
        $sequenceId = $request->get('sequence_id');

        $stats = [
            'general' => null,
            'par_classe' => [],
            'majors' => []
        ];

        if ($sequenceId) {
            // On lance les calculs optimisés via des fonctions privées
            $stats['general'] = $this->getStatsGlobales($sequenceId);
            $stats['majors'] = $this->getTopEleves($sequenceId);
            $stats['par_classe'] = $this->getStatsParClasse($sequenceId);
        }

        $sequences = Sequence::whereHas('trimestre', fn($q) => $q->where('annee_scolaire_id', $anneeId))->get();

        return view('pages.admin.stats-palmares', compact('sequences', 'stats'));
    }

    // --- FONCTIONS PRIVÉES D'OPTIMISATION ---


    // private function getStatsGlobales($sequenceId)
    // {
    //     $anneeId = $this->scolarite->getAnneeActive()->id;

    //     // 1. On récupère d'abord l'effectif réel (Tous les inscrits de l'année)
    //     $effectifReel = DB::table('inscriptions')
    //         ->join('eleves', 'inscriptions.eleve_id', '=', 'eleves.id')
    //         ->where('inscriptions.annee_scolaire_id', $anneeId)
    //         ->select(
    //             DB::raw('COUNT(*) as total'),
    //             DB::raw('SUM(CASE WHEN eleves.sexe = "M" THEN 1 ELSE 0 END) as garcons'),
    //             DB::raw('SUM(CASE WHEN eleves.sexe = "F" THEN 1 ELSE 0 END) as filles')
    //         )->first();

    //     // 2. On récupère les performances (Uniquement ceux qui ont des notes pour la séquence)
    //     $performances = DB::table('moyennes')
    //         ->where('sequence_id', $sequenceId)
    //         ->select(
    //             DB::raw('AVG(valeur) as moyenne_generale'),
    //             DB::raw('SUM(CASE WHEN valeur >= 10 THEN 1 ELSE 0 END) as admis'),
    //             DB::raw('MAX(valeur) as meilleure_note')
    //         )->first();

    //     // On fusionne les deux pour la vue
    //     return (object) [
    //         'effectif_total' => $effectifReel->total,
    //         'garcons' => $effectifReel->garcons,
    //         'filles' => $effectifReel->filles,
    //         'moyenne_generale' => $performances->moyenne_generale ?? 0,
    //         'admis' => $performances->admis ?? 0,
    //         'meilleure_note' => $performances->meilleure_note ?? 0
    //     ];
    // }

    private function getStatsGlobales($sequenceId)
    {
        $anneeId = $this->scolarite->getAnneeActive()->id;

        // 1. Effectif fixe des inscrits (La référence)
        $effectifRef = DB::table('inscriptions')
            ->join('eleves', 'inscriptions.eleve_id', '=', 'eleves.id')
            ->where('inscriptions.annee_scolaire_id', $anneeId)
            ->select(
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN eleves.sexe = "M" THEN 1 ELSE 0 END) as total_garcons'),
                DB::raw('SUM(CASE WHEN eleves.sexe = "F" THEN 1 ELSE 0 END) as total_filles')
            )->first();

        // 2. Statistiques de réussite (Basées sur les moyennes >= 10)
        $perf = DB::table('moyennes')
            ->join('inscriptions', 'moyennes.inscription_id', '=', 'inscriptions.id')
            ->join('eleves', 'inscriptions.eleve_id', '=', 'eleves.id')
            ->where('moyennes.sequence_id', $sequenceId)
            ->select(
                DB::raw('AVG(valeur) as moyenne_generale'),
                DB::raw('MAX(valeur) as meilleure_note'),
                // Les Admis (Total, G et F)
                DB::raw('SUM(CASE WHEN valeur >= 10 THEN 1 ELSE 0 END) as total_admis'),
                DB::raw('SUM(CASE WHEN valeur >= 10 AND eleves.sexe = "M" THEN 1 ELSE 0 END) as garcons_admis'),
                DB::raw('SUM(CASE WHEN valeur >= 10 AND eleves.sexe = "F" THEN 1 ELSE 0 END) as filles_admis')
            )->first();

        return (object) [
            'effectif_total'   => $effectifRef->total,
            'total_garcons'    => $effectifRef->total_garcons,
            'total_filles'     => $effectifRef->total_filles,
            'moyenne_generale' => $perf->moyenne_generale ?? 0,
            'meilleure_note'   => $perf->meilleure_note ?? 0,
            'total_admis'      => $perf->total_admis ?? 0,
            'total_echoues'    => $effectifRef->total - ($perf->total_admis ?? 0),
            'garcons_admis'    => $perf->garcons_admis ?? 0,
            'filles_admis'     => $perf->filles_admis ?? 0,
        ];
    }

















    private function getTopEleves($sequenceId)
    {
        return DB::table('moyennes')
            ->join('inscriptions', 'moyennes.inscription_id', '=', 'inscriptions.id')
            ->join('eleves', 'inscriptions.eleve_id', '=', 'eleves.id')
            ->join('classes', 'inscriptions.classe_id', '=', 'classes.id')
            ->join('niveaux', 'classes.niveau_id', '=', 'niveaux.id') // <-- AJOUT DU NIVEAU
            ->where('moyennes.sequence_id', $sequenceId)
            ->select(
                'eleves.nom',
                'eleves.prenom',
                // On crée "classe_complete" qui sera par exemple "6ème A"
                DB::raw("CONCAT(niveaux.nom, ' ', classes.nom) as classe_complete"),
                'moyennes.valeur'
            )
            ->orderByDesc('moyennes.valeur')
            ->limit(5)
            ->get();
    }

    private function getStatsParClasse($sequenceId)
    {
        return DB::table('moyennes')
            ->join('inscriptions', 'moyennes.inscription_id', '=', 'inscriptions.id')
            ->join('classes', 'inscriptions.classe_id', '=', 'classes.id')
            ->join('niveaux', 'classes.niveau_id', '=', 'niveaux.id') // <-- ON JOINT LE NIVEAU ICI
            ->where('moyennes.sequence_id', $sequenceId)
            ->select(
                // On fusionne Niveau + Classe (ex: "6ème" + " " + "A")
                DB::raw("CONCAT(niveaux.nom, ' ', classes.nom) as nom_complet_classe"),
                DB::raw('COUNT(*) as total'),
                DB::raw('AVG(valeur) as moyenne_classe'),
                DB::raw('SUM(CASE WHEN valeur >= 10 THEN 1 ELSE 0 END) as reussite')
            )
            ->groupBy('classes.id', 'niveaux.nom', 'classes.nom')
            ->get();
    }
}
