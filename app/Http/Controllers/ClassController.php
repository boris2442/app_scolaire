<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClassRequest;
use App\Http\Requests\ClassStoreRequest;
use App\Models\Classe;
use App\Models\Cycle;
use App\Models\Teacher;
use App\Models\Year;
use Illuminate\Support\Facades\DB;

class ClassController extends Controller
{
    public function index()
    {
        // On récupère l'année active (très important !)
        $actifYear = Year::where('est_active', true)->first();

        if (! $actifYear) {
            return redirect()->route('settings.years.index')
                ->with('error', 'Veuillez activer une année scolaire d\'abord.');
        }

        // On récupère TOUTES les classes de l'établissement (sans filtrer par année)
        $classes = Classe::with(['matieres', 'cycle'])->get();
        // recuperer les enseignant pour choisir le professeur principal
        $teachers = Teacher::with('user')->get();

        return view('pages.classes.index', compact('classes', 'actifYear', 'teachers'));
    }

    // public function store(ClassStoreRequest $request)
    // {
    //     $request->validated();

    //     Classe::create($request->all());

    //     return back()->with('success', 'La classe a été créée avec succès.');
    // }

    public function destroy(Classe $classe)
    {
        $classe->delete();

        return redirect()->route('settings.classes.index')->with('success', 'Classe supprimée.');
    }

    // public function edit($id)
    // {
    //     $classe = Classe::findOrFail($id);
    //     $cycles = Cycle::all(); // Pour alimenter le menu déroulant des cycles

    //     return view('pages.academics.classes-edit', compact('classe', 'cycles'));
    // }

    // public function update(ClassRequest $request, $id)
    // {
    //     $request->validated();

    //     $classe = Classe::findOrFail($id);
    //     $classe->update([
    //         'nom' => $request->nom,
    //         'cycle_id' => $request->cycle_id,
    //         'section' => $request->section,
    //     ]);

    //     return redirect()->route('settings.academique.index') // Ajuste selon la route de redirection de ta liste
    //         ->with('success', 'Classe mise à jour avec succès.');
    // }

    public function store(ClassStoreRequest $request)
    {
        $request->validated();

        // 1. Créer la classe
        $classe = Classe::create($request->only([
            'nom',
            'cycle_id',
            'section',
            'annee_scolaire_id',
        ]));

        // 2. Associer le professeur principal pour l'année scolaire transmise (ou l'année active)
        if ($request->filled('teacher_id')) {
            $yearId = $request->input('annee_scolaire_id')
                ?? Year::where('est_active', true)->value('id');

            DB::table('classe_enseignant_principal')->insert([
                'classe_id' => $classe->id,
                'enseignant_id' => $request->teacher_id,
                'annee_scolaire_id' => $yearId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return back()->with('success', 'La classe a été créée avec succès.');
    }

    public function edit($id)
    {
        $classe = Classe::with('professeursPrincipaux')->findOrFail($id);
        $cycles = Cycle::all();
        $teachers = Teacher::with('user')->get();

        // Récupérer le prof principal de l'année active pour pré-remplir le champ
        $currentProf = $classe->professeurPrincipalActif();

        return view('pages.academics.classes-edit', compact('classe', 'cycles', 'teachers', 'currentProf'));
    }

    public function update(ClassRequest $request, $id)
    {
        $request->validated();

        $classe = Classe::findOrFail($id);
        $classe->update([
            'nom' => $request->nom,
            'cycle_id' => $request->cycle_id,
            'section' => $request->section,
        ]);

        // Mettre à jour le prof principal pour l'année scolaire active
        $activeYearId = Year::where('est_active', true)->value('id');

        // Supprimer l'affectation existante pour cette année scolaire
        DB::table('classe_enseignant_principal')
            ->where('classe_id', $classe->id)
            ->where('annee_scolaire_id', $activeYearId)
            ->delete();

        // Insérer la nouvelle si un enseignant est sélectionné
        if ($request->filled('teacher_id')) {
            DB::table('classe_enseignant_principal')->insert([
                'classe_id' => $classe->id,
                'enseignant_id' => $request->teacher_id,
                'annee_scolaire_id' => $activeYearId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect()->route('settings.academique.index')
            ->with('success', 'Classe mise à jour avec succès.');
    }
}
