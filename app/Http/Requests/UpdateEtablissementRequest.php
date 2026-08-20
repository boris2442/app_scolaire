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
            'logo'      => 'nullable|image|mimes:jpg,jpeg,png|max:3072', // 3Mo max
            'english_name' => 'nullable|string|max:255',
            'english_slogan' => 'nullable|string|max:255',
            // Nouveaux champs de localisation
            'region'               => 'nullable|string|max:255',
            'department'           => 'nullable|string|max:255',
            'sub_division'         => 'nullable|string|max:255',
            'english_region'       => 'nullable|string|max:255',
            'english_department'   => 'nullable|string|max:255',
            'english_sub_division' => 'nullable|string|max:255',
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
            'email.email'  => 'L\'adresse email doit être valide.',
            'english_name.max' => 'Le nom en anglais ne doit pas dépasser 255 caractères.',
            'english_slogan.max' => 'Le slogan en anglais ne doit pas dépasser 255 caractères.',

            'nom.required'           => 'Le nom de l\'établissement est obligatoire pour les bulletins.',
            'logo.image'             => 'Le fichier doit être une image (jpg, jpeg ou png).',
            'logo.max'               => 'Le logo est trop lourd (maximum 3 Mo).',
            'email.email'            => 'L\'adresse email doit être valide.',
            'english_name.max'       => 'Le nom en anglais ne doit pas dépasser 255 caractères.',
            'english_slogan.max'     => 'Le slogan en anglais ne doit pas dépasser 255 caractères.',
            'region.max'             => 'La région ne doit pas dépasser 255 caractères.',
            'department.max'         => 'Le département ne doit pas dépasser 255 caractères.',
            'sub_division.max'       => 'L\'arrondissement ne doit pas dépasser 255 caractères.',
        ];
    }
}
