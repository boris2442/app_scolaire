<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use App\Models\Lecon;
use Illuminate\Http\Request;

class CheckProgramController extends Controller
{
    public function index(Request $request)
    {
        $classes = Classe::orderBy('nom')->get();
        $selectedClasseId = $request->input('classe_id');

        $progressionData = [];

        if ($selectedClasseId) {
            // Récupère toutes les leçons de la classe avec l'état 'evaluations_exists' (true/false)
            $leconsGrouped = Lecon::where('classe_id', $selectedClasseId)
                ->with(['matiere', 'enseignant'])
                ->withExists('evaluations') // Vérifie automatiquement si la leçon est liée à au moins une évaluation
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
