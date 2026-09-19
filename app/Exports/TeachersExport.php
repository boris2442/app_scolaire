<?php

namespace App\Exports;


use App\Models\Teacher;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;


class TeachersExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Teacher::with(['user', 'departement'])
            ->get()
            ->map(function ($Teacher) {
                return [
                    $Teacher->id,
                    $Teacher->matricule,
                    $Teacher->user->name,
                    $Teacher->user->phone,
                    $Teacher->user->email,
                    $Teacher->departement ? $Teacher->departement->nom : '',
                ];
            });
    }
    
    public function headings(): array
    {
        return ['ID', 'Matricule', 'Nom complet', 'Téléphone', 'Email', 'Département'];
    }
}
