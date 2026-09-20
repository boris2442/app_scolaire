<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreYearSchoolRequest;
use App\Models\Year;
use Illuminate\Support\Facades\DB;

class YearController extends Controller
{
    public function index()
    {
        $years = Year::with(['trimestres.sequences'])

            ->orderBy('date_debut', 'desc')->get();
        $totalYears = $years->count();
       
        $actifYear = $years->where('est_active', true)->first();

        return view('pages.years.index', compact('years', 'totalYears', 'actifYear'));
    }

    public function store(StoreYearSchoolRequest $request)
    {
        DB::transaction(function () use ($request) {
            // 1. Créer l'année
            $year = Year::create($request->validated());

            // 2. Créer les 3 trimestres et leurs séquences
            for ($i = 1; $i <= 3; $i++) {
                $trimestre = $year->trimestres()->create([
                    'nom' => "Trimestre $i",
                ]);

                // Pour chaque trimestre, on crée 2 séquences (1&2, 3&4, 5&6)
                for ($j = 1; $j <= 2; $j++) {
                    $numSeq = ($i - 1) * 2 + $j;
                    $trimestre->sequences()->create([
                        'nom' => "Eval $numSeq",
                    ]);
                }
            }
        });

        return back()->with('success', 'Année scolaire et périodes pédagogiques générées avec succès !');
    }

    public function set_active(Year $year)
    {
        $year->activer(); // Utilise la méthode du modèle

        return back()->with('success', "L'année {$year->libelle} est désormais active.");
    }

    public function destroy(Year $year)
    {
        if ($year->est_active) {
            return back()->with('error', 'Impossible de supprimer une année active.');
        }

        DB::transaction(function () use ($year) {
            // 1. Récupérer les IDs des trimestres de CETTE année uniquement
            $trimestreIds = $year->trimestres()->pluck('id');

            // 2. Supprimer les bilans liés à ces trimestres (pour éviter l'erreur 1451)
            DB::table('bilans')->whereIn('trimestre_id', $trimestreIds)->delete();

            // 3. Supprimer les séquences liées uniquement à ces trimestres
            DB::table('sequences')->whereIn('trimestre_id', $trimestreIds)->delete();

            // 4. Supprimer les trimestres de cette année
            $year->trimestres()->delete();

            // 5. Enfin, supprimer l'année
            $year->delete();
        });

        return back()->with('success', 'Année et toutes ses données associées supprimées avec succès.');
    }

    public function edit(Year $year)
    {
        // On envoie l'objet à la vue d'édition
        return view('pages.years.edit', compact('year'));
    }

    public function update(StoreYearSchoolRequest $request, Year $year)
    {
        $year->update($request->validated());

        return redirect()->route('settings.years.index')->with('success', 'Année mise à jour.');
    }
}
