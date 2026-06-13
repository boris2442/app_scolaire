<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCycleRequest;
use App\Http\Requests\StoreLevelRequest;
use App\Http\Requests\UpdateLevelRequest;
use App\Models\Cycle;
use App\Models\Niveau;
use Illuminate\Http\Request;

class AcademiqueController extends Controller
{
    public function index()
    {
        $cycles = Cycle::with('niveaux')->get();
        return view('pages.academique.index', compact('cycles'));
    }

    public function storeCycle(StoreCycleRequest $request)
    {
        $request->validated();
        Cycle::create($request->all());
        return back()->with('success', 'Cycle ajouté !');
    }

    public function storeNiveau(StoreLevelRequest $request)
    {
        $request->validated();
        Niveau::create($request->all());
        return back()->with('success', 'Niveau ajouté !');
    }


    // Afficher le formulaire d'édition d'un Cycle
    public function editCycle(Cycle $cycle)
    {
        return view('pages.academique.edit-cycle', compact('cycle'));
    }

    // Afficher le formulaire d'édition d'un Niveau
    public function editNiveau(Niveau $niveau)
    {
        $cycles = Cycle::all();
        return view('pages.academique.edit-niveau', compact('niveau', 'cycles'));
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
        // Attention : onDelete('cascade') dans la migration supprimera aussi les niveaux liés
        $cycle->delete();
        return back()->with('success', 'Cycle et ses niveaux supprimés.');
    }

    // --- ACTIONS POUR LES NIVEAUX ---
    public function updateNiveau(UpdateLevelRequest $request, Niveau $niveau)
    {
        $request->validated();
        $niveau->update($request->all());
        return redirect()->route('settings.academique.index')->with('success', 'Niveau mis à jour !');
    }

    public function destroyNiveau(Niveau $niveau)
    {
        $niveau->delete();
        return redirect()->route('settings.academique.index')->with('success', 'Niveau supprimé.');
    }
}
