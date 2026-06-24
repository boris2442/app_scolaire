<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CourseRequest;
use App\Models\GroupeMatiere;
use App\Models\Matiere;
use Illuminate\Http\Request;

class MatiereController extends Controller
{
    public function index(Request $request)
    {
        $groupes = GroupeMatiere::all();
        // $query = Matiere::query();
        // $query = Matiere::query();
        $query = Matiere::with(['groupeMatiere']); // On récupère également le groupe de chaque matière 


        // Petite recherche optionnelle
        if ($request->has('search')) {
            $query->where('nom', 'like', '%' . $request->search . '%')
                ->orWhere('code', 'like', '%' . $request->search . '%');
        }

        $matieres = $query->orderBy('nom')->paginate(10);
        return view('pages.matieres.index', compact('matieres', 'groupes'));
    }

    public function store(CourseRequest $request)
    {
        $validated = $request->validated();


        // Matiere::create($validated);
        Matiere::create([
            'nom' => $validated['nom'],
            'code' => $validated['code'],
            'groupe_matiere_id' => $validated['groupe_matiere_id'], // Assurez-vous que le champ existe dans la table matieres
        ]);


        return back()->with('success', 'Matière ajoutée au catalogue !');
    }

    public function edit(Matiere $matiere)
    {
        $groupes = GroupeMatiere::orderBy('ordre')->get();
        return view('pages.matieres.edit', compact('matiere', 'groupes'));
    }



    public function update(Request $request, Matiere $matiere)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:100|unique:matieres,nom,' . $matiere->id,
            'code' => 'required|string|max:10|unique:matieres,code,' . $matiere->id,
            'groupe_matiere_id' => 'required|exists:groupes_matieres,id', // Validation cruciale
        ]);

        $matiere->update($validated);
        // return back()->with('success', 'Matière mise à jour.');
        return redirect()->route('settings.matieres.index')->with('success', 'Matière mise à jour!');
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
