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
            $stats['general'] = $this->getStatsGlobales($sequenceId);
            $stats['majors'] = $this->getTopEleves($sequenceId);
            $stats['par_classe'] = $this->getStatsParClasse($sequenceId);
        }

        $sequences = Sequence::whereHas('trimestre', fn($q) => $q->where('annee_scolaire_id', $anneeId))->get();

        return view('pages.admin.stats-palmares', compact('sequences', 'stats'));
    }

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

        // 2. Statistiques de réussite basées sur la table 'bilans' (Vraies moyennes générales)
        $perf = DB::table('bilans')
            ->join('inscriptions', 'bilans.inscription_id', '=', 'inscriptions.id')
            ->join('eleves', 'inscriptions.eleve_id', '=', 'eleves.id')
            ->where('bilans.sequence_id', $sequenceId)
            ->select(
                DB::raw('AVG(moyenne) as moyenne_generale'),
                DB::raw('MAX(moyenne) as meilleure_note'),
                DB::raw('SUM(CASE WHEN moyenne >= 10 THEN 1 ELSE 0 END) as total_admis'),
                DB::raw('SUM(CASE WHEN moyenne >= 10 AND eleves.sexe = "M" THEN 1 ELSE 0 END) as garcons_admis'),
                DB::raw('SUM(CASE WHEN moyenne >= 10 AND eleves.sexe = "F" THEN 1 ELSE 0 END) as filles_admis')
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
        // Extraction du Top 5 basé sur la table 'bilans' (Sans table niveaux)
        return DB::table('bilans')
            ->join('inscriptions', 'bilans.inscription_id', '=', 'inscriptions.id')
            ->join('eleves', 'inscriptions.eleve_id', '=', 'eleves.id')
            ->join('classes', 'inscriptions.classe_id', '=', 'classes.id')
            ->where('bilans.sequence_id', $sequenceId)
            ->select(
                'eleves.nom',
                'eleves.prenom',
                'classes.nom as classe_complete', // On prend directement le nom de la classe
                'bilans.moyenne as valeur'
            )
            ->orderByDesc('bilans.moyenne')
            ->limit(5)
            ->get();
    }

    private function getStatsParClasse($sequenceId)
    {
        // Statistiques par classe basées sur la table 'bilans' (Sans table niveaux)
        return DB::table('bilans')
            ->join('inscriptions', 'bilans.inscription_id', '=', 'inscriptions.id')
            ->join('classes', 'inscriptions.classe_id', '=', 'classes.id')
            ->where('bilans.sequence_id', $sequenceId)
            ->select(
                'classes.id',
                'classes.nom as nom_complet_classe', // On prend directement le nom de la classe
                DB::raw('COUNT(*) as total'),
                DB::raw('AVG(moyenne) as moyenne_classe'),
                DB::raw('SUM(CASE WHEN moyenne >= 10 THEN 1 ELSE 0 END) as reussite')
            )
            ->groupBy('classes.id', 'classes.nom')
            ->get();
    }

    public function detailClasse($classe_id, $sequence_id)
    {
        $anneeActive = $this->scolarite->getAnneeActive();

        $classeInfo = DB::table('classes')
            ->where('classes.id', $classe_id)
            ->select('classes.nom as classe_nom')
            ->first();

        $totalInscrits = DB::table('inscriptions')
            ->where('classe_id', $classe_id)
            ->where('annee_scolaire_id', $anneeActive->id)
            ->count();

        // Récupération des moyennes générales de la classe depuis la table 'bilans'
        $moyennes = DB::table('bilans')
            ->join('inscriptions', 'bilans.inscription_id', '=', 'inscriptions.id')
            ->join('eleves', 'inscriptions.eleve_id', '=', 'eleves.id')
            ->where('inscriptions.classe_id', $classe_id)
            ->where('inscriptions.annee_scolaire_id', $anneeActive->id)
            ->where('bilans.sequence_id', $sequence_id)
            ->where('bilans.moyenne', '>', 0)
            ->select(
                'eleves.nom',
                'eleves.prenom',
                'bilans.moyenne as valeur',
                'inscriptions.id as inscription_id'
            )
            ->distinct()
            ->orderBy('bilans.moyenne', 'desc')
            ->get();

        $appreciations = [
            'Excellent'  => $moyennes->where('valeur', '>=', 18)->count(),
            'Très Bien'  => $moyennes->where('valeur', '>=', 16)->where('valeur', '<', 18)->count(),
            'Bien'       => $moyennes->where('valeur', '>=', 14)->where('valeur', '<', 16)->count(),
            'Assez Bien' => $moyennes->where('valeur', '>=', 12)->where('valeur', '<', 14)->count(),
            'Passable'   => $moyennes->where('valeur', '>=', 10)->where('valeur', '<', 12)->count(),
            'Médiocre'   => $moyennes->where('valeur', '<', 10)->count(),
        ];

        // Plus de relation 'niveau' ici
        $classe = Classe::findOrFail($classe_id);

        return view('pages.admin.stats-classe-detail', compact('moyennes', 'appreciations', 'classe', 'totalInscrits'));
    }

    public function registreTrimestriel(Request $request)
    {
        $classeId = $request->get('classe_id');
        $trimestreId = $request->get('trimestre_id');
        $anneeActive = $this->scolarite->getAnneeActive();

        // 1. Récupérer les classes et trimestres pour les filtres (Sans table niveaux)
        $classes = DB::table('classes')
            ->select('classes.id', 'classes.nom as nom')
            ->get();
            
        $trimestres = DB::table('trimestres')
            ->where('annee_scolaire_id', $anneeActive->id)
            ->get();

        $registre = null;

        if ($classeId && $trimestreId) {
            // 2. Trouver les séquences liées à ce trimestre
            $sequences = DB::table('sequences')
                ->where('trimestre_id', $trimestreId)
                ->orderBy('id', 'asc')
                ->get();

            // 3. Extraction des matières de la classe avec leur prof
            $matieres = DB::table('classe_matiere') 
                ->join('matieres', 'classe_matiere.matiere_id', '=', 'matieres.id')
                ->leftJoin('affectations', function($join) use ($classeId) {
                    $join->on('affectations.matiere_id', '=', 'classe_matiere.matiere_id')
                         ->where('affectations.classe_id', '=', $classeId);
                })
                ->leftJoin('enseignants', 'affectations.enseignant_id', '=', 'enseignants.id')
                ->leftJoin('users', 'enseignants.user_id', '=', 'users.id')
                ->where('classe_matiere.classe_id', $classeId)
                ->select('matieres.id', 'matieres.nom as matiere_nom', DB::raw("COALESCE(users.name, 'Aucun prof') as prof_nom"))
                ->get();

            // 4. Récupérer les élèves de la classe
            $eleves = DB::table('inscriptions')
                ->join('eleves', 'inscriptions.eleve_id', '=', 'eleves.id')
                ->where('inscriptions.classe_id', $classeId)
                ->where('inscriptions.annee_scolaire_id', $anneeActive->id)
                ->select('inscriptions.id as inscription_id', 'eleves.nom', 'eleves.prenom')
                ->orderBy('eleves.nom', 'asc')
                ->get();

            // 5. REQUÊTE OPTIMISÉE : Prendre toutes les notes des séquences de ce trimestre
            $notesBrutes = DB::table('moyennes')
                ->whereIn('inscription_id', $eleves->pluck('inscription_id'))
                ->whereIn('sequence_id', $sequences->pluck('id'))
                ->get();

            $grilleNotes = [];
            $coefficientsMatieres = [];
            
            foreach ($notesBrutes as $note) {
                $grilleNotes[$note->inscription_id][$note->matiere_id][$note->sequence_id] = $note->valeur;
                $coefficientsMatieres[$note->matiere_id] = $note->coefficient;
            }

            $registre = [
                'sequences' => $sequences,
                'matieres' => $matieres,
                'eleves' => $eleves,
                'grille' => $grilleNotes,
                'coefficients' => $coefficientsMatieres
            ];
        }

        return view('pages.admin.statistiques.registre-trimestriel', compact('classes', 'trimestres', 'registre', 'classeId', 'trimestreId'));
    }
}
