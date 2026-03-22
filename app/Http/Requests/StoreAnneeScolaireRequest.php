<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreAnneeScolaireRequest extends FormRequest
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
        // On récupère le paramètre de la route
        $anneeScolaire = $this->route('annee_scolaire');

        // On extrait l'ID (que ce soit un objet ou juste un chiffre)
        $id = is_object($anneeScolaire) ? $anneeScolaire->id : $anneeScolaire;

        return [
            'libelle' => [
                'required',
                'string',
                'max:20',
                // On ignore l'ID actuel pour permettre de modifier sans erreur "déjà pris"
                'unique:annee_scolaires,libelle,' . $id,
            ],
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after:date_debut',
        ];
    }
}
