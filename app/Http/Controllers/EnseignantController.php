<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
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
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'matricule' => 'required|unique:enseignants,matricule',
            'departement_id' => 'required|exists:departements,id', // Validation de l'existence
        ]);
        

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make('12345678'),
                'role' =>UserRole::ENSEIGNANT,
            ]);

            Enseignant::create([
                'user_id' => $user->id,
                'matricule' => $request->matricule,
                'departement_id' => $request->departement_id, // On enregistre l'ID
            ]);
        });

        return redirect()->route('admin.enseignants.index')->with('success', 'Enseignant créé avec succès !');
    }
}
