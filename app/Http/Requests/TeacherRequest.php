<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class TeacherRequest extends FormRequest
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
    // public function rules(): array
    // {
    //     return [
    //       'name' => 'required|string|max:255',
    //         'email' => 'required|email|unique:users,email',
    //        // 'matricule' => 'required|unique:enseignants,matricule',
    //         'departement_id' => 'required|exists:departements,id', // Validation de l'existence
    //     ];
    // }
    public function rules(): array
{
    return [
        'name' => ['required', 'string', 'max:255'],

        'email' => [
            // 'required',
            'email',
            'unique:users,email',
        ],

        'phone' => [
            'required',
            'string',
            'max:30',
        ],

        'departement_id' => [
            'required',
            'exists:departements,id',
        ],
    ];
}
}
