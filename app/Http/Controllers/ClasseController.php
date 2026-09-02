<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClassStoreRequest;
use App\Models\AnneeScolaire;
use App\Models\Classe;
use App\Models\Cycle;
use App\Models\Niveau;
use Illuminate\Http\Request;


class ClasseController extends Controller
{

    public function index()
    {
        // On récupère l'année active (très important !)
        $anneeActive = AnneeScolaire::where('est_active', true)->first();

        if (!$anneeActive) {
            return redirect()->route('settings.years.index')
                ->with('error', 'Veuillez activer une année scolaire d\'abord.');
        }

       
        // On récupère TOUTES les classes de l'établissement (sans filtrer par année)
        $classes = Classe::with(['matieres', 'cycle'])->get();


        return view('pages.classes.index', compact('classes', 'anneeActive'));
    }
    public function store(ClassStoreRequest $request)
    {
        $request->validated();

        Classe::create($request->all());

        return back()->with('success', 'La classe a été créée avec succès.');
    }

    public function destroy(Classe $classe)
    {
        $classe->delete();
        return redirect()->route('settings.classes.index')->with('success', 'Classe supprimée.');
    }

    public function edit($id)
    {
        $classe = Classe::findOrFail($id);
        $cycles = Cycle::all(); // Pour alimenter le menu déroulant des cycles

        return view('pages.academique.classes-edit', compact('classe', 'cycles'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'cycle_id' => 'required|exists:cycles,id',
            'section' => 'required|in:francophone,anglophone',
        ]);

        $classe = Classe::findOrFail($id);
        $classe->update([
            'nom' => $request->nom,
            'cycle_id' => $request->cycle_id,
            'section' => $request->section,
        ]);

        return redirect()->route('settings.academique.index') // Ajuste selon la route de redirection de ta liste
            ->with('success', 'Classe mise à jour avec succès.');
    }
}
