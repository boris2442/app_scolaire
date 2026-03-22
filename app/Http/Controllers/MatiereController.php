<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Matiere;
use Illuminate\Http\Request;

class MatiereController extends Controller
{
public function index(Request $request)
    {
        $query = Matiere::query();

        // Petite recherche optionnelle
        if ($request->has('search')) {
            $query->where('nom', 'like', '%' . $request->search . '%')
                  ->orWhere('code', 'like', '%' . $request->search . '%');
        }

        $matieres = $query->orderBy('nom')->paginate(10);
        return view('pages.matieres.index', compact('matieres'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:100|unique:matieres,nom',
            'code' => 'required|string|max:10|unique:matieres,code',
        ]);

        Matiere::create($validated);

        return back()->with('success', 'Matière ajoutée au catalogue !');
    }

    public function update(Request $request, Matiere $matiere)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:100|unique:matieres,nom,' . $matiere->id,
            'code' => 'required|string|max:10|unique:matieres,code,' . $matiere->id,
        ]);

        $matiere->update($validated);
        return back()->with('success', 'Matière mise à jour.');
    }

    public function destroy(Matiere $matiere)
    {
        // On vérifie si la matière est liée à des classes avant de supprimer
        if ($matiere->classes()->count() > 0) {
            return back()->with('error', 'Impossible de supprimer : cette matière est utilisée dans des classes.');
        }

        $matiere->delete();
        return back()->with('success', 'Matière supprimée du catalogue.');
    }
}
