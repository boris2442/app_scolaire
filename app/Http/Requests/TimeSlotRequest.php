<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class TimeSlotRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'heure_debut' => ['required', 'date_format:H:i'],
            'heure_fin' => ['required', 'date_format:H:i', 'after:heure_debut'],
            'libelle' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'heure_debut.required' => "L'heure de début est obligatoire.",
            'heure_debut.date_format' => "L'heure de début doit être au format HH:MM.",

            'heure_fin.required' => "L'heure de fin est obligatoire.",
            'heure_fin.date_format' => "L'heure de fin doit être au format HH:MM.",
            'heure_fin.after' => "L'heure de fin doit être après l'heure de début.",

            'libelle.string' => "Le libellé doit être une chaîne de caractères.",
            'libelle.max' => "Le libellé ne doit pas dépasser 255 caractères.",
        ];
    }
}
