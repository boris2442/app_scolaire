<?php

namespace App\Http\Controllers\Exports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DepartmentExport;

class DepartmentExportController extends Controller
{
    public function export()
    {
        return Excel::download(new DepartmentExport, 'departments.xlsx');
    }
}
