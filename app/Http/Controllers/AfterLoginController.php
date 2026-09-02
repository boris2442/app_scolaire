<?php
namespace App\Http\Controllers;

use App\Models\AnneeScolaire;
use App\Models\Etablissement;
use Illuminate\Support\Facades\Auth;

class AfterLoginController extends Controller
{
    public function index()
    {
        // Récupération sécurisée
        $anneeActive = AnneeScolaire::where('est_active', true)->first();
        $etablissement = Etablissement::first(); // Supposant qu'il n'y a qu'un seul paramétrage
// $user=Auth::user()->name();
// dd($user);
        return view('pages.after-login', compact('anneeActive', 'etablissement'));
    }
}
