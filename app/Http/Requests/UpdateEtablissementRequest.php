<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateEtablissementRequest extends FormRequest
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
            'nom'       => 'required|string|max:255',
            'slogan'    => 'nullable|string|max:255',
            'adresse'   => 'nullable|string',
            'telephone' => 'nullable|string|max:20',
            'code_ecole' => 'nullable|string|max:50',
            'email'     => 'nullable|email',
            'logo'      => 'nullable|image|mimes:jpg,jpeg,png|max:1024', // 1Mo max
        ];
    }

    /**
     * Messages d'erreur personnalisés (Optionnel mais plus pro).
     */
    public function messages(): array
    {
        return [
            'nom.required' => 'Le nom de l\'établissement est obligatoire pour les bulletins.',
            'logo.image'   => 'Le fichier doit être une image (jpg, jpeg ou png).',
            'logo.max'     => 'Le logo est trop lourd (maximum 1 Mo).',
        ];
    }
}
