<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Classe;
use App\Models\Matiere;
use Illuminate\Http\Request;

class ClasseMatiereController extends Controller
{
    // Afficher la page de config pour une classe spécifique
    public function edit(Classe $classe)
    {
        $allMatieres = Matiere::orderBy('nom')->get();
        
        // On récupère les IDs des matières déjà attribuées pour cocher les cases
        $matieresAttribuees = $classe->matieres->pluck('id')->toArray();

        return view('pages.classes.config-matieres', compact('classe', 'allMatieres', 'matieresAttribuees'));
    }

    // Sauvegarder les attributions
    public function update(Request $request, Classe $classe)
    {
        $matieres = $request->input('matieres', []); // Array des IDs cochés
        $coeffs = $request->input('coefficients', []); // Array des coeffs [id => valeur]

        $syncData = [];
        foreach ($matieres as $matiereId) {
            $syncData[$matiereId] = [
                'coefficient' => $coeffs[$matiereId] ?? 1,
                'ordre' => $request->input('ordre.' . $matiereId, 1)
            ];
        }

        // La méthode sync() supprime ce qui n'est plus coché et ajoute le nouveau
        $classe->matieres()->sync($syncData);

        return redirect()->route('settings.classes.index')
            ->with('success', 'Programme de la classe mis à jour !');
    }
}
