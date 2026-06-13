<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClassStoreRequest;
use App\Models\AnneeScolaire;
use App\Models\Classe;
use App\Models\Niveau;


class ClasseController extends Controller
{
    public function index()
    {
        // On récupère l'année active (très important !)
        $anneeActive = AnneeScolaire::where('est_active', true)->first();

        if (!$anneeActive) {
            return redirect()->route('settings.annees.index')
                ->with('error', 'Veuillez activer une année scolaire d\'abord.');
        }

        // On charge les classes de l'année active groupées par niveau
        $niveaux = Niveau::with(['classes' => function ($q) use ($anneeActive) {
            $q->where('annee_scolaire_id', $anneeActive->id)
                ->with('matieres'); // On charge aussi les matières pour chaque classe
        }])->get();

        return view('pages.classes.index', compact('niveaux', 'anneeActive'));
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
        return back()->with('success', 'Classe supprimée.');
    }
}
