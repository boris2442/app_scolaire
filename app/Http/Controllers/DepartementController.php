<?php

namespace App\Http\Controllers;

use App\Http\Requests\DepartementRequest;
use App\Models\Departement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DepartementController extends Controller
{
    public function index()
    {

        // Dans DepartementController.php à la ligne 12
        $departements = DB::table('departements')->select('id', 'nom', 'code', 'description', 'created_at')->get();
        return view('pages.departements.index', compact('departements'));
    }

    public function edit($id)
    {
        $departement = DB::table('departements')->where('id', $id)->first();

        if (!$departement) {
            return redirect()->back()->with('error', 'Département introuvable.');
        }

        return view('pages.departements.edit', compact('departement'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:departements,code,' . $id,
            'description' => 'nullable|string'
        ]);

        DB::table('departements')->where('id', $id)->update([
            'nom' => $request->nom,
            'code' => strtoupper($request->code),
            'description' => $request->description,
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.departments.index')
            ->with('success', 'Département mis à jour avec succès !');
    }


    public function destroy($id)
    {
        try {
            DB::table('departements')->where('id', $id)->delete();
            return redirect()->route('admin.departments.index')
                ->with('success', 'Département supprimé.');
        } catch (\Exception $e) {
            // En cas de classes liées au département
            return redirect()->back()->with('error', 'Impossible de supprimer : ce département est lié à d\'autres données.');
        }
    }




    public function create()
    {
        return view('pages.departements.create');
    }

    public function store(DepartementRequest $request)
    {
        $request->validated();

        DB::table('departements')->insert([
            'nom' => $request->nom,
            'code' => strtoupper($request->code),
            'description' => $request->description,
            'created_at' => now(),
            'updated_at' => now(),
            // Si tu as une colonne pour l'auteur, décommente la ligne suivante :
            // 'createur_id' => auth()->id(), 
        ]);

        return redirect()->route('admin.departments.index')
            ->with('success', 'Nouveau département créé avec succès !');
    }


    public function show(Departement $department)
{
    return redirect()->route('admin.departments.index');
}
}
