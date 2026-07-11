<?php

namespace App\Http\Controllers;

use App\Models\GroupeMatiere;
use Illuminate\Http\Request;

class GroupeMatiereController extends Controller
{
    public function index()
    {
        // On récupère les groupes triés par ordre d'affichage
        // et on compte le nombre de matières par groupe
        $groupes = GroupeMatiere::withCount('matieres')
            ->orderBy('ordre', 'asc')
            ->get();

        return view('pages.groupesmatieres.index', compact('groupes'));
    }

    public function create()
    {
        return view('pages.groupesmatieres.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'ordre' => 'required|integer',
        ]);

        GroupeMatiere::create($request->all());

        return redirect()->route('admin.groupes.index')
            ->with('success', 'Groupe ajouté avec succès.');
    }
    public function edit(GroupeMatiere $groupe)
    {
        return view('pages.groupesmatieres.edit', compact('groupe'));
    }
    public function update(Request $request, GroupeMatiere $groupe)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'ordre' => 'required|integer',
        ]);

        $groupe->update($request->all());

        return redirect()->route('admin.groupes.index')
            ->with('success', 'Groupe mis à jour avec succès.');
    }
    public function destroy(GroupeMatiere $groupe)
    {
        $groupe->delete();

        return redirect()->route('admin.groupes.index')
            ->with('success', 'Groupe supprimé avec succès.');
    }
}
