<?php

namespace App\Exports;

use App\Models\Departement;
use Maatwebsite\Excel\Concerns\FromCollection;

class DepartmentExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Departement::all();
    }
}
