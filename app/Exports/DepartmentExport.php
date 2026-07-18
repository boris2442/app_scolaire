<?php

namespace App\Exports;

use App\Models\Departement;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DepartmentExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // return Departement::all();
        return Departement::Select('id', 'nom', 'code', 'description')
            ->get()

            ->map(function ($departement) {
                return [
                    $departement->id,
                    $departement->nom,
                    $departement->code,
                    $departement->description ?? '',
                ];
            });
    }
    public function headings(): array
    {
        return ['ID', 'Nom', 'Code', 'Description'];
    }
}
