<?php

namespace App\Http\Controllers;

use App\Http\Requests\TimeSlotRequest;
use App\Models\Creneau;

class TimeSlotController extends Controller
{
    // Afficher la liste des créneaux horaires de l'établissement
    public function index()
    {
        $creneaux = Creneau::orderBy('heure_debut')->get();

        return view('pages.timeslots.index', compact('creneaux'));
    }

    // Enregistrer un nouveau créneau horaire
    public function store(TimeSlotRequest $request)
    {
        Creneau::create($request->validated());
        // Creneau::create($validated);

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
