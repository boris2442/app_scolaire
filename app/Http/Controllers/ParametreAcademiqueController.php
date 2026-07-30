<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use App\Models\Niveau;
use App\Models\ParametreAcademique;
use App\Services\ScolariteService;
use Illuminate\Http\Request;

class ParametreAcademiqueController extends Controller
{
  // Fonction pour afficher la vue avec les classes
    public function index()
    {
        // On récupère directement les classes avec leur paramètre 'moyenne_min'
        $classes = Classe::with(['parametres' => function ($q) {
            $q->where('cle', 'moyenne_min');
        }])->get();

        return view('pages.parametres-classes.index', compact('classes'));
    }

  

   public function store(Request $request)
    {
        $annee = (new ScolariteService())->getAnneeActive();
        
        foreach ($request->moyennes as $classeId => $valeur) {
            ParametreAcademique::updateOrCreate(
                [
                    'classe_id'         => $classeId,
                    'annee_scolaire_id' => $annee->id,         // On injecte l'année
                    'cle'               => 'moyenne_min'
                ],
                [
                    'valeur' => $valeur
                ]
            );
        }

        return back()->with('success', 'Enregistrement réussi.');
    }
}
