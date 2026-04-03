<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Affectation;
use App\Models\Evaluation;
use App\Services\ScolariteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardTeacherController extends Controller
{
    public function index(ScolariteService $scolarite)
    {
        $enseignant = Auth::user()->enseignant;
        $anneeActive = $scolarite->getAnneeActive();

        // 1. Récupérer les affectations avec les relations nécessaires
        $affectations = Affectation::where('enseignant_id', $enseignant->id)
            ->where('annee_scolaire_id', $anneeActive->id)
            ->with(['classe.inscriptions', 'matiere'])
            ->get();

        // DashboardTeacherController.php

        $statsSaisie = $affectations->map(function ($affectation) {
            $totalEleves = $affectation->classe->inscriptions->count();

            $evaluation = Evaluation::where([
                'classe_id' => $affectation->classe_id,
                'matiere_id' => $affectation->matiere_id,
            ])->withCount('notes')->first();

            return [
                'classe' => $affectation->classe->nom,
                'niveau' => $affectation->classe->niveau->nom ?? 'N/A', // Vérifie si c'est 'nom' ou 'libelle'
                'matiere' => $affectation->matiere->nom,
                'total' => $totalEleves,
                'saisies' => $evaluation ? $evaluation->notes_count : 0,
                'pourcentage' => $totalEleves > 0 ? round((($evaluation?->notes_count ?? 0) / $totalEleves) * 100) : 0,
                'evaluation_id' => $evaluation?->id,
                'derniere_date' => $evaluation ? $evaluation->updated_at->diffForHumans() : null,
            ];
        });
        return view('pages.enseignants.dashboard', compact('statsSaisie', 'enseignant', 'anneeActive'));
    }
}
