<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Affectation;
use App\Models\Evaluation;
use App\Models\Sequence;
use App\Services\ScolariteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardTeacherController extends Controller
{



   

public function index(Request $request, ScolariteService $scolarite)
{
    $anneeActive = $scolarite->getAnneeActive();
    $enseignant = auth()->user()->enseignant;

    if (!$enseignant) {
        return back()->with('error', "Action impossible : profil enseignant non trouvé.");
    }

    // 1. Récupérer uniquement les séquences de l'année active
    $sequences = \App\Models\Sequence::whereHas('trimestre', function ($query) use ($anneeActive) {
        $query->where('annee_scolaire_id', $anneeActive->id);
    })->get();

    $sequenceId = $request->input('sequence_id', $sequences->first()?->id);

    // 2. Récupérer les affectations valides pour l'année active
    $affectations = $enseignant->affectations()
        ->where('annee_scolaire_id', $anneeActive->id)
        ->with(['matiere', 'classe.inscriptions'])
        ->whereHas('classe.matieres', function ($query) {
            $query->whereColumn('matieres.id', 'affectations.matiere_id');
        })
        ->get();

    // 3. Charger UNIQUEMENT les évaluations créées par cet enseignant pour la séquence choisie
    $evaluations = \App\Models\Evaluation::where('enseignant_id', $enseignant->id)
        ->where('sequence_id', $sequenceId)
        ->withCount('notes')
        ->get()
        ->keyBy(function ($item) {
            // Clé unique pour associer sans erreur : "classe_id-matiere_id"
            return $item->classe_id . '-' . $item->matiere_id;
        });

    // 4. Construction des statistiques pour le tableau de bord
    $statsSaisie = $affectations->map(function ($affectation) use ($evaluations) {
        $totalEleves = $affectation->classe->inscriptions->count();

        // Récupération instantanée depuis la mémoire
        $key = $affectation->classe_id . '-' . $affectation->matiere_id;
        $evaluation = $evaluations->get($key);

        return [
            'classe' => $affectation->classe->nom,
            'matiere' => $affectation->matiere->nom,
            'total' => $totalEleves,
            'saisies' => $evaluation ? $evaluation->notes_count : 0,
            'pourcentage' => $totalEleves > 0 ? round((($evaluation?->notes_count ?? 0) / $totalEleves) * 100) : 0,
            'evaluation_id' => $evaluation?->id,
            'derniere_date' => $evaluation ? $evaluation->updated_at->diffForHumans() : null,
        ];
    });

    return view('pages.enseignants.dashboard', compact(
        'statsSaisie',
        'sequences',
        'sequenceId',
        'enseignant',
        'anneeActive'
    ));
}
}
