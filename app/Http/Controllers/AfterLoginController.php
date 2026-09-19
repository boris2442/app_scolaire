<?php

namespace App\Http\Controllers;

use App\Models\Year;

use App\Models\School;
use Illuminate\Support\Facades\Auth;

class AfterLoginController extends Controller
{
    public function index()
    {
        // Récupération sécurisée
        $anneeActive = Year::where('est_active', true)->first();
        $etablissement = School::first(); // Supposant qu'il n'y a qu'un seul paramétrage

        // $user=Auth::user()->name();
        // dd($user);
        return view('pages.after-login', compact('anneeActive', 'etablissement'));
    }
}
