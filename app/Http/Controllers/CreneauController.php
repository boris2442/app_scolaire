<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use App\Models\Creneau;
use Illuminate\Http\Request;

class CreneauController extends Controller
{

    // Afficher la liste des créneaux horaires de l'établissement
    public function index()
    {
        $creneaux = Creneau::orderBy('heure_debut')->get();
        return view('pages.creneaux.index', compact('creneaux'));
    }

    // Enregistrer un nouveau créneau horaire
    public function store(Request $request)
    {
        $validated = $request->validate([
            'heure_debut' => 'required|date_format:H:i',
            'heure_fin' => 'required|date_format:H:i|after:heure_debut',
            'libelle' => 'nullable|string|max:255',
        ]);

        Creneau::create($validated);

        return redirect()->back()->with('success', 'Créneau horaire ajouté avec succès.');
    }
    // Supprimer un créneau
    public function destroy($id)
    {
        $creneau = Creneau::findOrFail($id);
        $creneau->delete();

        return redirect()->route('admin.creneaux.index')->with('success', 'Créneau supprimé avec succès.');
    }
}
