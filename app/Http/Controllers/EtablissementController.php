<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateEtablissementRequest;
use App\Models\Etablissement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EtablissementController extends Controller
{
    public function edit()
    {
        $etablissement = Etablissement::first() ?: new Etablissement();
        return view('pages.etablissements.edit', compact('etablissement'));
    }

    public function update(UpdateEtablissementRequest $request)
    {
        // Si on arrive ici, c'est que la validation a déjà réussi !
        $etablissement = Etablissement::first() ?: new Etablissement();

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
