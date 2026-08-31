<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use App\Models\Lecon;
use App\Services\ScolariteService;
use Illuminate\Http\Request;

class CheckProgramController extends Controller
{
    protected $scolarite;

    public function __construct(ScolariteService $scolarite)
    {
        $this->scolarite = $scolarite;
    }
    public function index(Request $request)
    {
        $anneeActive = $this->scolarite->getAnneeActive();
        $classes = Classe::orderBy('nom')->get();
        $selectedClasseId = $request->input('classe_id');

        $progressionData = [];

        if ($selectedClasseId) {
            // Filtrage des évaluations uniquement sur l'année scolaire active
            $leconsGrouped = Lecon::where('classe_id', $selectedClasseId)
                ->with(['matiere', 'enseignant'])
                ->withExists(['evaluations' => function ($query) use ($anneeActive) {
                    $query->whereHas('sequence.trimestre', function ($q) use ($anneeActive) {
                        $q->where('annee_scolaire_id', $anneeActive->id);
                    });
                }])
                ->orderBy('ordre')
                ->get()
                ->groupBy('matiere_id');

            foreach ($leconsGrouped as $matiereId => $lecons) {
                $totalLecons = $lecons->count();
                $leconsFaites = $lecons->where('evaluations_exists', true)->count();
                $pourcentage = $totalLecons > 0 ? round(($leconsFaites / $totalLecons) * 100) : 0;

                $firstLecon = $lecons->first();

                $progressionData[] = [
                    'matiere'       => $firstLecon->matiere,
                    'enseignant'    => $firstLecon->enseignant,
                    'total_lecons'  => $totalLecons,
                    'lecons_faites' => $leconsFaites,
                    'pourcentage'   => $pourcentage,
                    'lecons'        => $lecons,
                ];
            }
        }

        return view('pages.avancement-program.index', compact('classes', 'selectedClasseId', 'progressionData'));
    }
}
