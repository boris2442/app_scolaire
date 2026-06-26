<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use App\Models\Inscription;
use App\Models\Niveau;
use App\Models\SuiviDisciplinaire;
use App\Models\Trimestre;
use App\Services\ScolariteService;
use DB;
use Illuminate\Http\Request;

class DisciplineController extends Controller
{
    protected $scolarite;

    public function __construct(ScolariteService $scolarite)
    {
        $this->scolarite = $scolarite;
    }

    // 1. Affiche le formulaire de sélection (Classe + Trimestre)
    public function index()
    {
        $annee = $this->scolarite->getAnneeActive();
        $niveaux = Niveau::with('classes')->get();
        $trimestres = $annee->trimestres;

        return view('pages.discipline.select', compact('niveaux', 'trimestres'));
    }

    public function saisie(Request $request)
    {
        $request->validate([
            'classe_id' => 'required',
            'trimestre_id' => 'required',
        ]);

        // 1. Récupération des objets nécessaires pour la vue
        $classe = Classe::findOrFail($request->classe_id);
        $trimestre = Trimestre::findOrFail($request->trimestre_id);

        // 2. Récupération des inscriptions
        // $inscriptions = Inscription::where('classe_id', $request->classe_id)
        //     ->where('annee_scolaire_id', $this->scolarite->getAnneeActive()->id)
        //     ->with(['eleve', 'suivi' => function ($query) use ($request) {
        //         $query->where('trimestre_id', $request->trimestre_id);
        //     }])

        //     ->get();

        // 2. Récupération des inscriptions triées par nom et prénom
        $inscriptions = Inscription::where('classe_id', $request->classe_id)
            ->where('annee_scolaire_id', $this->scolarite->getAnneeActive()->id)
            ->with(['eleve', 'suivi' => function ($query) use ($request) {
                $query->where('trimestre_id', $request->trimestre_id);
            }])
            // On joint la table 'eleves' pour accéder à ses colonnes
            ->join('eleves', 'inscriptions.eleve_id', '=', 'eleves.id')
            // On trie sur les colonnes de la table jointe
            ->orderBy('eleves.nom', 'asc')
            ->orderBy('eleves.prenom', 'asc')
            // On sélectionne uniquement les colonnes de la table inscriptions 
            // pour éviter d'écraser les attributs du modèle Inscription
            ->select('inscriptions.*')
            ->get();











        // 3. Passage des variables à la vue
        return view('pages.discipline.saisie', compact('inscriptions', 'classe', 'trimestre'));
    }
    // 3. Enregistrement en masse (Le cœur du système)
    public function store(Request $request)
    {
        // 1. Validation stricte
        $request->validate([
            'classe_id' => 'required|exists:classes,id',
            'trimestre_id' => 'required|exists:trimestres,id',
            'data' => 'required|array',
        ]);
        // dd($request->data);

        try {
            DB::transaction(function () use ($request) {
                foreach ($request->data as $inscriptionId => $values) {

                    // Vérifier si au moins une valeur est différente de 0 ou null
                    // Si tout est à zéro, on peut choisir de supprimer l'enregistrement existant 
                    // pour garder la base propre.
                    $hasData = collect($values)->filter(fn($v) => !empty($v))->isNotEmpty();

                    if ($hasData) {
                        SuiviDisciplinaire::updateOrCreate(
                            [
                                'inscription_id' => $inscriptionId,
                                'trimestre_id' => $request->trimestre_id,
                            ],
                            [
                                'retards' => $values['retards'] ?? 0,
                                'absences' => $values['absences'] ?? 0,
                                'suspensions' => $values['suspensions'] ?? 0,
                                'avertissements' => $values['avertissements'] ?? 0,
                                'blames' => $values['blames'] ?? 0,
                                'exclusions' => $values['exclusions'] ?? 0,
                            ]
                        );
                    } else {
                        // Optionnel : Si l'utilisateur efface tout, on supprime l'entrée
                        SuiviDisciplinaire::where('inscription_id', $inscriptionId)
                            ->where('trimestre_id', $request->trimestre_id)
                            ->delete();
                    }
                }
            });

            return back()->with('success', 'Données disciplinaires enregistrées avec succès.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Une erreur est survenue lors de l\'enregistrement : ' . $e->getMessage()]);
        }
    }
}
