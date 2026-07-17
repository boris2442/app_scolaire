<?php

namespace App\Http\Controllers\Exports;

use App\Exports\StudentExport;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class StudentControllerExport extends Controller
{
    public function export()
    {
        return Excel::download(new StudentExport, 'students.xlsx');
    }
}
