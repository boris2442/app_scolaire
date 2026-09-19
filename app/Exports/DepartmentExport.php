<?php

namespace App\Exports;


use App\Models\Department;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DepartmentExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // return Department::all();
        return Department::Select('id', 'nom', 'code', 'description')
            ->get()

            ->map(function ($Department) {
                return [
                    $Department->id,
                    $Department->nom,
                    $Department->code,
                    $Department->description ?? '',
                ];
            });
    }
    public function headings(): array
    {
        return ['ID', 'Nom', 'Code', 'Description'];
    }
}
