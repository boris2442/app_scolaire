<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Requests\TeacherRequest;
use App\Models\Department;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TeacherController extends Controller
{
    public function index()
    {

        // afficher les enseignants avec pagination par ordre alphabetique
        // $enseignants=Teacher::with(['user', 'departement'])
        $teachers = Teacher::select('enseignants.*')
            ->join('users', 'users.id', '=', 'enseignants.user_id')
            ->with(['user', 'departement'])
            ->orderBy('users.name', 'asc') // Ordre alphabétique A -> Z
            ->paginate(15); // Nombre d'éléments par page

        return view('pages.teachers.index', compact('teachers'));
    }

    /**
     * Affiche le formulaire de création d'un enseignant
     */
    public function create()
    {
        $departments = Department::orderBy('nom')->get();

        return view('pages.teachers.create', compact('departments'));
    }

    public function store(TeacherRequest $request)
    {
        $validated = $request->validated();

        // Génération du mot de passe temporaire
        $temporaryPassword = Str::password(6);

        DB::transaction(function () use ($validated, $temporaryPassword) {

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => $temporaryPassword,
                'role' => UserRole::ENSEIGNANT,
                'phone' => $validated['phone'] ?? null,
                'must_change_password' => true,
            ]);

            Teacher::create([
                'user_id' => $user->id,
                'matricule' => Teacher::generateMatricule(),
                'departement_id' => $validated['departement_id'],
            ]);
        });

        return redirect()
            ->route('admin.enseignants.index')
            ->with('success', 'Enseignant créé avec succès !')
            ->with('credentials', [
                'name' => $validated['name'],
                'phone' => $validated['phone'],
                'password' => $temporaryPassword,
            ]);
    }

    public function edit(Teacher $teacher)
    {
        $teacher=Teacher::findOrFail($teacher);
        $departments = Department::orderBy('nom')->get();

        return view('pages.teachers.edit', compact('teacher', 'departments'));
    }

    public function update(Request $request, Teacher $teacher)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$teacher->user_id,
            // 'matricule' => 'unique:enseignants,matricule,' . $enseignant->id,
            'departement_id' => 'required|exists:departements,id',
        ]);

        DB::transaction(function () use ($request, $teacher) {
            // Mise à jour de l'utilisateur
            $teacher->user->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone, // Nouveau !
            ]);

            // Mise à jour de l'enseignant
            $teacher->update([
                // 'matricule' => $request->matricule,
                'departement_id' => $request->departement_id,
            ]);
        });

        return redirect()->route('admin.enseignants.index')->with('success', 'Enseignant mis à jour avec succès !');
    }

   public function destroy(Teacher $teacher)
{
    DB::transaction(function () use ($teacher) {
        $teacher->user->delete(); // Supprime l'utilisateur associé
        $teacher->delete();       // Supprime l'enseignant
    });

    return redirect()
        ->route('admin.enseignants.index')
        ->with('success', 'Enseignant supprimé avec succès !');
}

    public function show(Teacher $teacher)
    {
        $teacher->load('user', 'departement'); // Charge les relations nécessaires

        return view('pages.teachers.show', compact('teacher'));
    }
}
