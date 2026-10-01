<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateEtsRequest;
use App\Models\School;
use Illuminate\Support\Facades\Storage;

class SchoolController extends Controller
{
    public function edit()
    {
        $school = School::first() ?: new School;

        return view('pages.schools.edit', compact('school'));
    }

    public function update(UpdateEtsRequest $request)
    {
        // Si on arrive ici, c'est que la validation a déjà réussi !
        $school = School::first() ?: new School;

        $validatedData = $request->validated(); // On récupère uniquement les données validées

        // if ($request->hasFile('logo')) {
        //     if ($school->logo) {
        //         Storage::disk('public')->delete($school->logo);
        //     }
        //     $validatedData['logo'] = $request->file('logo')->store('uploads/ecole', 'public');
        // }

        if ($request->hasFile('logo')) {

            // Supprimer l'ancien logo
            if ($school->logo && file_exists(public_path($school->logo))) {
                unlink(public_path($school->logo));
            }

            // Dossier public/uploads/ecole
            $directory = public_path('uploads/ecole');

            if (! file_exists($directory)) {
                mkdir($directory, 0755, true);
            }

            // Nom unique du fichier
            $filename = uniqid().'.'.$request->file('logo')->getClientOriginalExtension();

            // Déplacer directement dans public/uploads/ecole
            $request->file('logo')->move($directory, $filename);

            // Enregistrer le chemin en base
            $validatedData['logo'] = 'uploads/ecole/'.$filename;
        }

        $school->fill($validatedData);
        // dd($validatedData);
        $school->save();

        return redirect()->route('settings.index')->with('success', 'Configuration enregistrée avec succès.');
    }
}
