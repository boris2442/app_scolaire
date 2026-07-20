<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AnneeScolaire;
use App\Models\Classe;
use App\Models\Creneau;
use App\Models\Jour;
use App\Models\Seance;
use App\Services\ScolariteService;
use Illuminate\Http\Request;

class SeanceController extends Controller
{
    // Afficher l'emploi du temps d'une classe spécifique
    public function showByClasse($classeId)
    {
        $classe = Classe::findOrFail($classeId);
        $anneeActive = AnneeScolaire::where('est_active', true)->first(); // Adapte selon ton champ d'année active

        // Récupérer toutes les séances de cette classe pour l'année en cours
        $seances = Seance::with(['matiere', 'enseignant', 'jour', 'creneau'])
            ->where('classe_id', $classeId)
            ->where('annee_scolaire_id', $anneeActive?->id)
            ->get();

        $jours = Jour::orderBy('ordre')->get();
        $creneaux = Creneau::orderBy('heure_debut')->get();

        return view('pages.emplois.classe', compact('classe', 'seances', 'jours', 'creneaux'));
    }

    // Enregistrer une nouvelle séance de cours
    public function store(Request $request)
    {
        $validated = $request->validate([
            'classe_id' => 'required|exists:classes,id',
            'matiere_id' => 'required|exists:matieres,id',
            'enseignant_id' => 'required|exists:enseignants,id',
            'jour_id' => 'required|exists:jours,id',
            'creneau_id' => 'required|exists:creneaux,id',
        ]);

        $anneeActive = AnneeScolaire::where('est_active', true)->first();

        if (!$anneeActive) {
            return redirect()->back()->withErrors(['msg' => 'Aucune année scolaire active trouvée.']);
        }

        $validated['annee_scolaire_id'] = $anneeActive->id;

        // Optionnel : Vérifier si l'enseignant est déjà occupé sur ce créneau ce jour-là
        $conflitEnseignant = Seance::where('annee_scolaire_id', $anneeActive->id)
            ->where('enseignant_id', $validated['enseignant_id'])
            ->where('jour_id', $validated['jour_id'])
            ->where('creneau_id', $validated['creneau_id'])
            ->exists();

        if ($conflitEnseignant) {
            return redirect()->back()->withErrors(['conflit' => 'Cet enseignant a déjà un cours prévu à ce créneau !']);
        }

        Seance::create($validated);

        return redirect()->back()->with('success', 'Séance planifiée avec succès.');
    }

    public function indexClasses()
    {
        $classes = Classe::with('niveau')->orderBy('nom')->get();
        return view('pages.emplois.choix-classe', compact('classes'));
    }
}
