<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ClassStoreRequest extends FormRequest
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
            'nom' => 'required|string|max:100',
            'cycle_id' => 'required|exists:cycles,id',
            'section' => 'required|string|in:francophone,anglophone',
            'annee_scolaire_id' => 'nullable|exists:annee_scolaires,id',
            'teacher_id' => 'nullable|exists:enseignants,id',
        ];
    }
}
