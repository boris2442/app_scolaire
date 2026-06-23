<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class EleveRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nom' => 'required|string|max:255',
            'prenom' => 'nullable|string|max:255',
            'date_naissance' => 'required|date',
            'sexe' => 'required|in:M,F',
            'classe_id' => 'required|exists:classes,id', // On cible la table 'classes'
            'lieu_naissance' => 'nullable|string',
            'telephone_parent' => 'nullable|string',
            'adresse' => 'nullable|string',
            'est_redoublant' => 'nullable|boolean',
        ];
    }
}
