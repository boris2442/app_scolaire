<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classe;
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
                'classes.id', // <--- IL MANQUAIT ÇA !
                // On fusionne Niveau + Classe (ex: "6ème" + " " + "A")
                DB::raw("CONCAT(niveaux.nom, ' ', classes.nom) as nom_complet_classe"),
                DB::raw('COUNT(*) as total'),
                DB::raw('AVG(valeur) as moyenne_classe'),
                DB::raw('SUM(CASE WHEN valeur >= 10 THEN 1 ELSE 0 END) as reussite')
            )
            ->groupBy('classes.id', 'niveaux.nom', 'classes.nom')
            ->get();
    }





    public function detailClasse($classe_id, $sequence_id)
    {
        // 1. Récupérer l'année active pour éviter de mélanger avec les archives
        $anneeActive = DB::table('annee_scolaires')->where('est_active', 1)->first();
        // 1. Récupérer les infos de la classe AVEC son niveau
        $classe = DB::table('classes')
            ->join('niveaux', 'classes.niveau_id', '=', 'niveaux.id')
            ->where('classes.id', $classe_id)
            ->select('classes.nom as classe_nom', 'niveaux.nom as niveau_nom')
            ->first();
        $totalInscrits = DB::table('inscriptions')
            ->where('classe_id', $classe_id)
            ->where('annee_scolaire_id', $anneeActive->id)
            ->count();


        // 2. La requête avec filtrage strict
        $moyennes = DB::table('moyennes')
            ->join('inscriptions', 'moyennes.inscription_id', '=', 'inscriptions.id')
            ->join('eleves', 'inscriptions.eleve_id', '=', 'eleves.id')
            ->where('inscriptions.classe_id', $classe_id)
            ->where('inscriptions.annee_scolaire_id', $anneeActive->id) // Filtre l'année
            ->where('moyennes.valeur', '>', 0) // <--- AJOUTE CECI pour masquer les notes non saisies
            ->where('moyennes.sequence_id', $sequence_id) // Filtre la séquence
            ->select(
                'eleves.nom',
                'eleves.prenom',
                'moyennes.valeur',
                'inscriptions.id as inscription_id' // Utiliser l'ID d'inscription unique
            )
            ->distinct() // <--- FORCE LA SUPPRESSION DES DOUBLONS
            ->orderBy('moyennes.valeur', 'desc')
            ->get();

        // On prépare les compteurs par mention
        $appreciations = [
            'Excellent' => $moyennes->where('valeur', '>=', 18)->count(),
            'Très Bien' => $moyennes->whereBetween('valeur', [16, 17.99])->count(),
            'Bien' => $moyennes->whereBetween('valeur', [14, 15.99])->count(),
            'Assez Bien' => $moyennes->whereBetween('valeur', [12, 13.99])->count(),
            'Passable' => $moyennes->whereBetween('valeur', [10, 11.99])->count(),
            'Médiocre' => $moyennes->where('valeur', '<', 10)->count(),
        ];

        // On récupère les infos de la classe pour le titre
        // $classe = DB::table('classes')->find($classe_id);
        $classe = Classe::with('niveau')->findOrFail($classe_id);

        return view('pages.admin.stats-classe-detail', compact('moyennes', 'appreciations', 'classe', 'totalInscrits'));
    }
}
