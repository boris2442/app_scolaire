<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAnneeScolaireRequest;
use App\Models\AnneeScolaire;

use Illuminate\Support\Facades\DB;

class AnneeScolaireController extends Controller
{
    public function index()
    {
        $annees = AnneeScolaire::with(['trimestres.sequences'])

            ->orderBy('date_debut', 'desc')->get();
        $totalAnnees = $annees->count();
        //$anneeActive = AnneeScolaire::where('est_active', true)->first();
        // On récupère l'année active (petit bonus : on peut la chercher dans la collection déjà chargée)
        $anneeActive = $annees->where('est_active', true)->first();
        return view('pages.annees.index', compact('annees', 'totalAnnees', 'anneeActive'));
    }



    public function store(StoreAnneeScolaireRequest $request)
    {
        DB::transaction(function () use ($request) {
            // 1. Créer l'année
            $annee = AnneeScolaire::create($request->validated());

            // 2. Créer les 3 trimestres et leurs séquences
            for ($i = 1; $i <= 3; $i++) {
                $trimestre = $annee->trimestres()->create([
                    'nom' => "Trimestre $i"
                ]);

                // Pour chaque trimestre, on crée 2 séquences (1&2, 3&4, 5&6)
                for ($j = 1; $j <= 2; $j++) {
                    $numSeq = ($i - 1) * 2 + $j;
                    $trimestre->sequences()->create([
                        'nom' => "Séquence $numSeq"
                    ]);
                }
            }
        });

        return back()->with('success', 'Année scolaire et périodes pédagogiques générées avec succès !');
    }









    public function set_active(AnneeScolaire $annee_scolaire)
    {
        $annee_scolaire->activer(); // Utilise la méthode du modèle
        return back()->with('success', "L'année {$annee_scolaire->libelle} est désormais active.");
    }

    public function destroy(AnneeScolaire $annee_scolaire)
    {
        if ($annee_scolaire->est_active) return back()->with('error', 'Impossible de supprimer une année active.');
        $annee_scolaire->delete();
        return back()->with('success', 'Année supprimée.');
    }


    public function edit(AnneeScolaire $annee_scolaire)
    {
        // On envoie l'objet à la vue d'édition
        return view('pages.annees.edit', compact('annee_scolaire'));
    }

    public function update(StoreAnneeScolaireRequest $request, AnneeScolaire $annee_scolaire)
    {
        $annee_scolaire->update($request->validated());
        return redirect()->route('settings.years.index')->with('success', 'Année mise à jour.');
    }
}
