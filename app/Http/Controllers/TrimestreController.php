<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AnneeScolaire;
use App\Models\Sequence;
use App\Models\Trimestre;
use Illuminate\Http\Request;

class TrimestreController extends Controller
{
    public function index()
    {
        // On ne propose que les années qui n'ont pas encore leurs 3 trimestres
        $anneesSansTrimestres = AnneeScolaire::withCount('trimestres')
            ->having('trimestres_count', '<', 3)
            ->get();

        $anneeActive = AnneeScolaire::where('est_active', 1)
            ->with('trimestres.sequences')
            ->first();
       // $anneesSansTrimestres = AnneeScolaire::withCount('trimestres')->get();
        //     dd($anneesSansTrimestres->toArray()); // Ceci va stopper la page et afficher le contenu

        return view('pages.trimestres.index', compact('anneesSansTrimestres', 'anneeActive'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required',
            'annee_scolaire_id' => 'required|exists:annee_scolaires,id',
        ]);

        // 1. Créer le trimestre
        $trimestre = Trimestre::create($request->all());

        // 2. Logique automatique pour les séquences (Le secret de Boris Tech)
        $sequencesMap = [
            '1er Trimestre' => ['Séquence 1', 'Séquence 2'],
            '2e Trimestre'  => ['Séquence 3', 'Séquence 4'],
            '3e Trimestre'  => ['Séquence 5', 'Séquence 6'],
        ];

        if (isset($sequencesMap[$request->nom])) {
            foreach ($sequencesMap[$request->nom] as $nomSeq) {
                Sequence::create([
                    'nom' => $nomSeq,
                    'trimestre_id' => $trimestre->id
                ]);
            }
        }

        return redirect()->back()->with('success', "Le {$trimestre->nom} et ses séquences ont été initialisés.");
    }
}
