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

        if ($request->hasFile('logo')) {
            if ($school->logo) {
                Storage::disk('public')->delete($school->logo);
            }
            $validatedData['logo'] = $request->file('logo')->store('uploads/ecole', 'public');
        }

        $school->fill($validatedData);
        // dd($validatedData);
        $school->save();

        return redirect()->route('settings.index')->with('success', 'Configuration enregistrée avec succès.');
    }
}
