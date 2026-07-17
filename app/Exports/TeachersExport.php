<?php

namespace App\Exports;

use App\Models\Enseignant;
use Maatwebsite\Excel\Concerns\FromCollection;

class TeachersExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Enseignant::all();
    }
}
