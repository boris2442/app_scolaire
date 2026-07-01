<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AnneeScolaire;
use App\Models\Inscription;
use App\Models\Sequence;
use App\Models\Trimestre;
use App\Services\AcademicStatisticsService;
use App\Services\MoyenneService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrimestreController extends Controller
{

    protected $statisticsService;
    protected $moyenneService;

    // On injecte le service dans le contrôleur
    public function __construct(AcademicStatisticsService $statisticsService, MoyenneService $moyenneService)
    {
        $this->statisticsService = $statisticsService;
        $this->moyenneService = $moyenneService;
    }




    // La fonction qui se déclenche quand tu cliques sur "Calculer le trimestre"
    public function genererBilanTrimestre(Request $request, $trimestreId, $classeId)
    {
        // 1. Récupération dynamique de l'année scolaire active (Fini le "1" en dur !)
        $anneeActive = DB::table('annee_scolaires')->where('est_active', 1)->first();

        if (!$anneeActive) {
            return redirect()->back()->with('error', "Aucune année scolaire active configurée.");
        }

        $anneeScolaireId = $anneeActive->id;

        // 2. PREMIÈRE ÉTAPE CRUCIALE : Calculer les moyennes et rangs par MATIÈRE
        // C'est cette ligne qui va enfin remplir ta table 'moyennes' avec le trimestre_id !
        $this->moyenneService->calculerMoyennesTrimestrielles($classeId, $trimestreId);

        // 3. Récupérer tous les élèves inscrits dans cette classe
        $inscriptions = Inscription::where('classe_id', $classeId)->get();

        // 4. DEUXIÈME ÉTAPE : Pour chaque élève, on calcule sa moyenne générale trimestrielle
        // (Cette méthode va lire les données qu'on vient de générer ou les bilans séquentiels)
        foreach ($inscriptions as $inscription) {
            $this->statisticsService->calculerBilanGeneralTrimestre($trimestreId, $inscription->id, $anneeScolaireId);
        }

        // 5. TROISIÈME ÉTAPE : Une fois que tout le monde a sa moyenne générale, on distribue les rangs globaux !
        $this->statisticsService->attribuerRangsClasseForTrimestre($trimestreId, $classeId);

        return redirect()->back()->with('success', 'Les bilans, moyennes par matière et rangs du trimestre ont été calculés avec succès !');
    }















    public function index()
    {
        // On ne propose que les années qui n'ont pas encore leurs 3 trimestres
        $anneesSansTrimestres = AnneeScolaire::withCount('trimestres')
            ->having('trimestres_count', '<', 3)
            ->get();

        $anneeActive = AnneeScolaire::where('est_active', 1)
            ->with('trimestres.sequences')
            ->first();


        return view('pages.trimestres.index', compact('anneesSansTrimestres', 'anneeActive'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required',
            'annee_scolaire_id' => 'required|exists:annee_scolaires,id',
        ]);

        // 1. Créer le trimestre
        $trimestre = Trimestre::create($request->all());

        // 2. Logique automatique pour les séquences (Le secret de Boris Tech)
        $sequencesMap = [
            '1er Trimestre' => ['Séquence 1', 'Séquence 2'],
            '2e Trimestre'  => ['Séquence 3', 'Séquence 4'],
            '3e Trimestre'  => ['Séquence 5', 'Séquence 6'],
        ];

        if (isset($sequencesMap[$request->nom])) {
            foreach ($sequencesMap[$request->nom] as $nomSeq) {
                Sequence::create([
                    'nom' => $nomSeq,
                    'trimestre_id' => $trimestre->id
                ]);
            }
        }

        return redirect()->back()->with('success', "Le {$trimestre->nom} et ses séquences ont été initialisés.");
    }
}
