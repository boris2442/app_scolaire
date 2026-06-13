<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\TeacherRequest;
use App\Models\Departement;
use App\Models\Enseignant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


class EnseignantController extends Controller
{

    public function index()
    {
        // On ajoute 'departement' dans le with()
        $enseignants = Enseignant::with(['user', 'departement'])->latest()->get();

        return view('pages.enseignants.index', compact('enseignants'));
    }

    /**
     * Affiche le formulaire de création d'un enseignant
     */
    public function create()
    {
        $departements = Departement::orderBy('nom')->get();
        return view('pages.enseignants.create', compact('departements'));
    }
    public function store(TeacherRequest $request)
    {
        $request->validated();


        DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make('12345678'),
                'role' => UserRole::ENSEIGNANT,
                'phone' => $request->phone, // Nouveau !
            ]);

            Enseignant::create([
                'user_id' => $user->id,
                'matricule' => $request->matricule,
                'departement_id' => $request->departement_id, // On enregistre l'ID
            ]);
        });

        return redirect()->route('admin.enseignants.index')->with('success', 'Enseignant créé avec succès !');
    }

    public function edit(Enseignant $enseignant)
    {
        $departements = Departement::orderBy('nom')->get();
        return view('pages.enseignants.edit', compact('enseignant', 'departements'));
    }
    public function update(Request $request, Enseignant $enseignant)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $enseignant->user_id,
            'matricule' => 'required|unique:enseignants,matricule,' . $enseignant->id,
            'departement_id' => 'required|exists:departements,id',
        ]);

        DB::transaction(function () use ($request, $enseignant) {
            // Mise à jour de l'utilisateur
            $enseignant->user->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone, // Nouveau !
            ]);

            // Mise à jour de l'enseignant
            $enseignant->update([
                'matricule' => $request->matricule,
                'departement_id' => $request->departement_id,
            ]);
        });

        return redirect()->route('admin.enseignants.index')->with('success', 'Enseignant mis à jour avec succès !');
    }
    public function destroy(Enseignant $enseignant)
    {
        DB::transaction(function () use ($enseignant) {
            $enseignant->user->delete(); // Supprime l'utilisateur associé
            $enseignant->delete(); // Supprime l'enseignant
        });

        return redirect()->route('admin.enseignants.index')->with('success', 'Enseignant supprimé avec succès !');
    }

    public function show(Enseignant $enseignant)
    {
        $enseignant->load('user', 'departement'); // Charge les relations nécessaires
        return view('pages.enseignants.show', compact('enseignant'));
    }
}
