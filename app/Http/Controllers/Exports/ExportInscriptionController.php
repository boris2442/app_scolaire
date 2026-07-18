<?php

namespace App\Http\Controllers\Exports;


use App\Exports\InscriptionExport;
use App\Http\Controllers\Controller;
use App\Services\ScolariteService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExportInscriptionController extends Controller
{
    public function export(ScolariteService $scolariteService)
    {
        return Excel::download(new InscriptionExport($scolariteService), 'inscriptions.xlsx');
    }
}
