<?php

namespace App\Exports;

use App\Models\Enseignant;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;


class TeachersExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Enseignant::with(['user', 'departement'])
            ->get()
            ->map(function ($enseignant) {
                return [
                    $enseignant->id,
                    $enseignant->matricule,
                    $enseignant->user->name,
                    $enseignant->user->phone,

                    $enseignant->user->email,
                    $enseignant->departement ? $enseignant->departement->nom : '',
                ];
            });
    }
    public function headings(): array
    {
        return ['ID', 'Matricule', 'Nom complet', 'Téléphone', 'Email', 'Département'];
    }
}
