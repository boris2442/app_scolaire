<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AnneeScolaire;
use App\Models\Classe;
use App\Models\Niveau;
use Illuminate\Http\Request;

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

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:50',
            'niveau_id' => 'required|exists:niveaux,id',
            'annee_scolaire_id' => 'required|exists:annee_scolaires,id'
        ]);

        Classe::create($request->all());

        return back()->with('success', 'La classe a été créée avec succès.');
    }

    public function destroy(Classe $classe)
    {
        $classe->delete();
        return back()->with('success', 'Classe supprimée.');
    }
}
