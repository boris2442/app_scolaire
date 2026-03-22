<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAnneeScolaireRequest;
use App\Models\AnneeScolaire;
use Illuminate\Http\Request;

class AnneeScolaireController extends Controller
{
    public function index()
    {
        $annees = AnneeScolaire::orderBy('date_debut', 'desc')->get();
        $totalAnnees = $annees->count();
        $anneeActive = AnneeScolaire::where('est_active', true)->first();

        return view('pages.annees.index', compact('annees', 'totalAnnees', 'anneeActive'));
    }

    public function store(StoreAnneeScolaireRequest $request)
    {
        AnneeScolaire::create($request->validated());

        return back()->with('success', 'Année scolaire ajoutée.');
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
        return redirect()->route('settings.annees.index')->with('success', 'Année mise à jour.');
    }
}
