<?php

namespace App\Http\Controllers\Exports;

use App\Exports\TeachersExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class TeacherExportController extends Controller
{
public function export()
    {
        return Excel::download(new TeachersExport, 'teachers.xlsx');
    }
}
