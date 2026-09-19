<?php

namespace App\Http\Controllers;


use App\Http\Requests\UpdateEtsRequest;
use App\Models\School;

use Illuminate\Support\Facades\Storage;

class SchoolController extends Controller
{
    public function edit()
    {
        $etablissement = School::first() ?: new School();
        return view('pages.etablissements.edit', compact('etablissement'));
    }

    public function update(UpdateEtsRequest $request)
    {
        // Si on arrive ici, c'est que la validation a déjà réussi !
        $etablissement = School::first() ?: new School();

        $validatedData = $request->validated(); // On récupère uniquement les données validées

        if ($request->hasFile('logo')) {
            if ($etablissement->logo) {
                Storage::disk('public')->delete($etablissement->logo);
            }
            $validatedData['logo'] = $request->file('logo')->store('uploads/ecole', 'public');
        }

        $etablissement->fill($validatedData);
        // dd($validatedData);
        $etablissement->save();

        return redirect()->route('settings.index')->with('success', 'Configuration enregistrée avec succès.');
    }
}
