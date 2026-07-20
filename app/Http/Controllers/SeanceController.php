<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AnneeScolaire;
use App\Models\Classe;
use App\Models\Creneau;
use App\Models\Enseignant;
use App\Models\Jour;
use App\Models\Matiere;
use App\Models\Seance;
use App\Models\User;
use App\Services\ScolariteService;
use Illuminate\Http\Request;

class SeanceController extends Controller
{
    // Afficher l'emploi du temps d'une classe spécifique
    // Afficher l'emploi du temps d'une classe spécifique
    public function showByClasse($classeId)
    {
        $classe = Classe::findOrFail($classeId);
        $anneeActive = AnneeScolaire::where('est_active', true)->first();

        // Récupérer toutes les séances de cette classe pour l'année en cours
        $seances = Seance::with(['matiere', 'enseignant', 'jour', 'creneau'])
            ->where('classe_id', $classeId)
            ->where('annee_scolaire_id', $anneeActive?->id)
            ->get();

        $jours = Jour::orderBy('ordre')->get();
        $creneaux = Creneau::orderBy('heure_debut')->get();

        // 1. Récupérer uniquement les matières de cette classe via la table pivot classe_matiere
        // (Adapte le nom de la table pivot si elle s'appelle différemment, ex: classe_matiere ou matieres_classes)
        $matieresIds = \Illuminate\Support\Facades\DB::table('classe_matiere')
            ->where('classe_id', $classeId)
            ->pluck('matiere_id');

        $matieres = Matiere::whereIn('id', $matieresIds)
            ->select('id', 'nom')
            ->orderBy('nom')
            ->get();

        // 2. Récupérer uniquement les enseignants affectés à cette classe (via la table affectations)
        $enseignantsIds = \Illuminate\Support\Facades\DB::table('affectations')
            ->where('classe_id', $classeId)
            ->when($anneeActive, function ($q) use ($anneeActive) {
                return $q->where('annee_scolaire_id', $anneeActive->id);
            })
            ->pluck('enseignant_id');

        $enseignants = User::where('role', 'enseignant')
            ->whereIn('id', $enseignantsIds)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return view('pages.emplois.classe', compact('classe', 'seances', 'jours', 'creneaux', 'matieres', 'enseignants'));
    }


    // Enregistrer une nouvelle séance de cours
    public function store(Request $request)
    {
        $validated = $request->validate([
            'classe_id' => 'required|exists:classes,id',
            'matiere_id' => 'required|exists:matieres,id',
            'enseignant_id' => 'required|exists:enseignants,id',
            'jour_id' => 'required|exists:jours,id',
            'creneau_id' => 'required|exists:creneaus,id',
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
