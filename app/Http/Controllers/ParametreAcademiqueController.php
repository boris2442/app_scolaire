<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use App\Models\Niveau;
use App\Models\ParametreAcademique;
use App\Services\ScolariteService;
use Illuminate\Http\Request;

class ParametreAcademiqueController extends Controller
{
    // Fonction pour afficher la vue avec les niveaux et leurs classes
    public function index()
    {
        // On récupère les niveaux et on charge les classes associées
        // On charge aussi les paramètres existants pour afficher la valeur actuelle
        $niveaux = Niveau::with(['classes' => function ($query) {
            $query->with(['parametres' => function ($q) {
                $q->where('cle', 'moyenne_min');
            }]);
        }])->get();

        return view('pages.parametres-classes.index', compact('niveaux'));
    }

    // Fonction pour enregistrer les moyennes saisies
    // public function store(Request $request)
    // {
    //     // $request->moyennes est un tableau : [classe_id => valeur]
    //     foreach ($request->moyennes as $classeId => $valeur) {
    //         // On cherche s'il existe déjà une règle, sinon on la crée
    //         ParametreAcademique::updateOrCreate(
    //             [
    //                 'classe_id' => $classeId, 
    //                 'cle' => 'moyenne_min'
    //             ],
    //             [
    //                 'valeur' => $valeur
    //             ]
    //         );
    //     }

    //     return redirect()->back()->with('success', 'Les moyennes minimales ont été mises à jour.');
    // }

    public function store(Request $request)
    {
        $annee = (new ScolariteService())->getAnneeActive();
        // DEBUG RAPIDE
       // dd($request->all(), $annee->id);
        foreach ($request->moyennes as $classeId => $valeur) {
            // Il faut récupérer le niveau_id de la classe pour le stocker
            $classe = Classe::find($classeId);

            ParametreAcademique::updateOrCreate(
                [
                    'classe_id'         => $classeId,
                    'niveau_id'         => $classe->niveau_id, // On injecte le niveau
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
