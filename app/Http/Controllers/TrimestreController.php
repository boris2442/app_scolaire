<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AnneeScolaire;
use App\Models\Inscription;
use App\Models\Sequence;
use App\Models\Trimestre;
use App\Services\AcademicStatisticsService;
use Illuminate\Http\Request;

class TrimestreController extends Controller
{

protected $statisticsService;

    // On injecte le service dans le contrôleur
    public function __construct(AcademicStatisticsService $statisticsService)
    {
        $this->statisticsService = $statisticsService;
    }

    // La fonction qui se déclenche quand tu cliques sur "Calculer le trimestre"
    public function genererBilanTrimestre(Request $request, $trimestreId, $classeId)
    {
        // 1. Récupérer tous les élèves inscrits dans cette classe
        $inscriptions = Inscription::where('classe_id', $classeId)->get();
        $anneeScolaireId = 1; // À remplacer par ton ID d'année active

        // 2. Étape 1 : Pour chaque élève, on calcule sa moyenne trimestrielle
        foreach ($inscriptions as $inscription) {
            $this->statisticsService->calculerBilanGeneralTrimestre($trimestreId, $inscription->id, $anneeScolaireId);
        }

        // 3. Étape 2 : Une fois que tous les élèves ont leur moyenne, on les classe !
        $this->statisticsService->attribuerRangsClasseForTrimestre($trimestreId, $classeId);

        return redirect()->back()->with('success', 'Les bilans et rangs du trimestre ont été calculés avec succès !');
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
        // $anneesSansTrimestres = AnneeScolaire::withCount('trimestres')->get();
        //     dd($anneesSansTrimestres->toArray()); // Ceci va stopper la page et afficher le contenu

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
