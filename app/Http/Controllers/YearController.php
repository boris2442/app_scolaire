<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreYearSchoolRequest;
use App\Models\Year;
use Illuminate\Support\Facades\DB;

class YearController extends Controller
{
    public function index()
    {
        $annees = Year::with(['trimestres.sequences'])

            ->orderBy('date_debut', 'desc')->get();
        $totalAnnees = $annees->count();
        // $anneeActive = Year::where('est_active', true)->first();
        // On récupère l'année active (petit bonus : on peut la chercher dans la collection déjà chargée)
        $anneeActive = $annees->where('est_active', true)->first();

        return view('pages.years.index', compact('annees', 'totalAnnees', 'anneeActive'));
    }

    public function store(StoreYearSchoolRequest $request)
    {
        DB::transaction(function () use ($request) {
            // 1. Créer l'année
            $annee = Year::create($request->validated());

            // 2. Créer les 3 trimestres et leurs séquences
            for ($i = 1; $i <= 3; $i++) {
                $trimestre = $annee->trimestres()->create([
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

    public function set_active(Year $annee_scolaire)
    {
        $annee_scolaire->activer(); // Utilise la méthode du modèle

        return back()->with('success', "L'année {$annee_scolaire->libelle} est désormais active.");
    }

    // public function destroy(AnneeScolaire $annee_scolaire)
    // {
    //     if ($annee_scolaire->est_active) return back()->with('error', 'Impossible de supprimer une année active.');
    //     // Vérifier si des bilans sont associés à cette année
    //     // Vérification directe dans la table 'bilans'
    //     if (DB::table('bilans')->where('annee_scolaire_id', $annee_scolaire->id)->exists()) {
    //         return back()->with(['error' => 'Impossible de supprimer car des bilans sont associés.']);
    //     }
    //     $annee_scolaire->delete();
    //     return back()->with('success', 'Année supprimée.');
    // }

    // public function destroy(AnneeScolaire $annee_scolaire)
    // {
    //     if ($annee_scolaire->est_active) {
    //         return back()->with('error', 'Impossible de supprimer une année active.');
    //     }

    //     // 1. Supprimer manuellement les relations associées dans 'bilans'
    //     // On utilise DB::table pour supprimer sans passer par le modèle s'il n'est pas prêt
    //     DB::table('bilans')->where('annee_scolaire_id', $annee_scolaire->id)->delete();

    //     // 2. Maintenant que les dépendances sont supprimées, on peut supprimer l'année
    //     $annee_scolaire->delete();

    //     return back()->with('success', 'Année et bilans associés supprimés avec succès.');
    // }

    public function destroy(Year $annee_scolaire)
    {
        if ($annee_scolaire->est_active) {
            return back()->with('error', 'Impossible de supprimer une année active.');
        }

        DB::transaction(function () use ($annee_scolaire) {
            // 1. Récupérer les IDs des trimestres de CETTE année uniquement
            $trimestreIds = $annee_scolaire->trimestres()->pluck('id');

            // 2. Supprimer les bilans liés à ces trimestres (pour éviter l'erreur 1451)
            DB::table('bilans')->whereIn('trimestre_id', $trimestreIds)->delete();

            // 3. Supprimer les séquences liées uniquement à ces trimestres
            DB::table('sequences')->whereIn('trimestre_id', $trimestreIds)->delete();

            // 4. Supprimer les trimestres de cette année
            $annee_scolaire->trimestres()->delete();

            // 5. Enfin, supprimer l'année
            $annee_scolaire->delete();
        });

        return back()->with('success', 'Année et toutes ses données associées supprimées avec succès.');
    }

    public function edit(Year $annee_scolaire)
    {
        // On envoie l'objet à la vue d'édition
        return view('pages.years.edit', compact('annee_scolaire'));
    }

    public function update(StoreYearSchoolRequest $request, Year $annee_scolaire)
    {
        $annee_scolaire->update($request->validated());

        return redirect()->route('settings.years.index')->with('success', 'Année mise à jour.');
    }
}
