<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class TeacherProfileController extends Controller
{
    public function update(Request $request)
    {
        $user = Auth::user();

        // Validation des champs (ajuste selon tes impératifs)
        $validated = $request->validate([
            'matricule' => ['required', 'string', 'max:255'],
            'grade' => ['nullable', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date'],
            'birth_place' => ['nullable', 'string', 'max:255'],
            'marital_status' => ['nullable', 'string', 'max:50'],
            'previous_position' => ['nullable', 'string', 'max:255'],
            'previous_school' => ['nullable', 'string', 'max:255'],
            'appointment_document_number' => ['nullable', 'string', 'max:255'],
            'appointment_date' => ['nullable', 'date'],
            'service_assumption_date' => ['nullable', 'date'],
            'quality' => ['nullable', 'string', 'max:255'],
            'diploma' => ['nullable', 'string', 'max:255'],
            'matiere_id' => ['nullable', 'exists:matieres,id'],
            'public_service_first_date' => ['nullable', 'date'],
            'school_first_date' => ['nullable', 'date'],
            'interruption_reason' => ['nullable', 'string', 'max:255'],
            'interruption_start_date' => ['nullable', 'date'],
            'interruption_end_date' => ['nullable', 'date'],
            'secondary_phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:255'],
            'number_of_children' => ['nullable', 'integer', 'min:0', 'max:30'],
        ]);
        // dd($validated);
        // Enregistrement ou mise à jour relié à l'utilisateur connecté
        $user->enseignant()->updateOrCreate(
            ['user_id' => $user->id],
            $validated
        );

        return Redirect::route('after.login.page')->with('success', 'enseignant-profile-updated');
    }
}
