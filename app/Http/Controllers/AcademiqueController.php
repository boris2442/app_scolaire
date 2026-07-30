<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCycleRequest;
use App\Models\AnneeScolaire;
use App\Models\Cycle;
use Illuminate\Http\Request;

class AcademiqueController extends Controller
{
    public function index()
    {
        // On récupère l'année active (très important !)
        $anneeActive = AnneeScolaire::where('est_active', true)->first();

        if (!$anneeActive) {
            return redirect()->route('settings.years.index')
                ->with('error', 'Veuillez activer une année scolaire d\'abord.');
        }
        // On récupère uniquement les cycles (la relation 'niveaux' est supprimée)
        $cycles = Cycle::with('classes')->get(); // avec 'with' pour charger la relation
        return view('pages.academique.index', compact('cycles', 'anneeActive'));
    }

    public function storeCycle(StoreCycleRequest $request)
    {
        $request->validated();
        Cycle::create($request->all());
        return back()->with('success', 'Cycle ajouté !');
    }

    // Afficher le formulaire d'édition d'un Cycle
    public function editCycle(Cycle $cycle)
    {
        return view('pages.academique.edit-cycle', compact('cycle'));
    }

    // --- ACTIONS POUR LES CYCLES ---
    public function updateCycle(Request $request, Cycle $cycle)
    {
        $request->validate(['nom' => 'required|unique:cycles,nom,' . $cycle->id]);
        $cycle->update($request->all());
        return back()->with('success', 'Cycle mis à jour !');
    }

    public function destroyCycle(Cycle $cycle)
    {
        $cycle->delete();
        return back()->with('success', 'Cycle supprimé.');
    }
}
