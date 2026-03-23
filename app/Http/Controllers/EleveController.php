<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\EleveRequest;
use App\Models\AnneeScolaire;
use App\Models\Eleve;
use App\Models\Inscription;
use App\Models\Niveau;
use Illuminate\Http\Request;

class EleveController extends Controller
{

    // public function index(Request $request)
    // {
    //     $anneeActive = AnneeScolaire::where('est_active', true)->first();

    //     $query = Eleve::with(['inscriptions' => function ($q) use ($anneeActive) {
    //         $q->where('annee_scolaire_id', $anneeActive->id)->with('classe');
    //     }]);

    //     // Recherche
    //     if ($request->filled('search')) {
    //         $search = $request->search;
    //         $query->where(function ($q) use ($search) {
    //             $q->where('nom', 'LIKE', "%{$search}%")
    //                 ->orWhere('prenom', 'LIKE', "%{$search}%")
    //                 ->orWhere('matricule', 'LIKE', "%{$search}%");
    //         });
    //     }

    //     $eleves = $query->latest()->paginate(15)->withQueryString();

    //     return view('pages.eleves.index', compact('eleves', 'anneeActive'));
    // }

    // public function index(Request $request)
    // {
    //     // 1. On récupère l'année active (le contexte est roi)
    //     $anneeActive = AnneeScolaire::where('est_active', true)->first();

    //     // 2. On prépare la requête avec les relations pour éviter le "N/A"
    //     $query = Eleve::with(['inscriptions' => function ($q) use ($anneeActive) {
    //         $q->where('annee_scolaire_id', $anneeActive->id)->with('classe.niveau');
    //     }]);

    //     // 3. Système de filtre (si on choisit une classe dans l'URL)
    //     if ($request->has('classe_id')) {
    //         $query->whereHas('inscriptions', function ($q) use ($request, $anneeActive) {
    //             $q->where('classe_id', $request->classe_id)
    //                 ->where('annee_scolaire_id', $anneeActive->id);
    //         });
    //     }

    //     // 4. On pagine ! (Crucial pour les 10 000 élèves)
    //     $eleves = $query->latest()->paginate(15);

    //     $niveaux = Niveau::with('classes')->get();

    //     return view('pages.eleves.index', compact('eleves', 'niveaux', 'anneeActive'));
    // }
    public function index(Request $request)
    {
        $anneeActive = AnneeScolaire::where('est_active', true)->first();

        // On commence la requête avec les relations nécessaires (Eager Loading)
        $query = Eleve::with(['inscriptions' => function ($q) use ($anneeActive) {
            $q->where('annee_scolaire_id', $anneeActive->id)->with('classe.niveau');
        }]);

        // FILTRE 1 : Par Niveau
        if ($request->filled('niveau_id')) {
            $query->whereHas('inscriptions.classe', function ($q) use ($request) {
                $q->where('niveau_id', $request->niveau_id);
            });
        }

        // FILTRE 2 : Par Classe spécifique
        if ($request->filled('classe_id')) {
            $query->whereHas('inscriptions', function ($q) use ($request) {
                $q->where('classe_id', $request->classe_id);
            });
        }

        // FILTRE 3 : Recherche par Nom ou Matricule
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nom', 'like', "%{$request->search}%")
                    ->orWhere('matricule', 'like', "%{$request->search}%");
            });
        }

        // PAGINATION : Crucial pour la performance (15 par page)
        $eleves = $query->latest()->paginate(15)->withQueryString();

        $niveaux = Niveau::with('classes')->get();

        return view('pages.eleves.index', compact('eleves', 'niveaux', 'anneeActive'));
    }



    public function create()

    {
        //afficher le sexe

        $anneeActive = AnneeScolaire::where('est_active', true)->first();
        $niveaux = Niveau::with('classes')->get();
        // On récupère les sexes depuis la structure de la BD
        $sexes = Eleve::getSexeOptions();
        return view('pages.eleves.create', compact('anneeActive', 'niveaux', 'sexes'));
    }

    // public function store(Request $request)
    // {
    //     // 1. Validation stricte
    //     $request->validate([
    //         'nom' => 'required|string|max:255',
    //         'date_naissance' => 'required|date',
    //         'sexe' => 'required|in:M,F',
    //         'salle_id' => 'required|exists:salles,id',
    //     ]);

    //     // 2. Génération du Matricule (Ex: 26-0001)
    //     $anneeSuffixe = date('y'); 
    //     $totalEleves = Eleve::count();
    //     $matricule = $anneeSuffixe . '-' . str_pad($totalEleves + 1, 4, '0', STR_PAD_LEFT);

    //     // 3. Création de l'élève
    //     $eleve = \App\Models\Eleve::create([
    //         'matricule' => $matricule,
    //         'nom' => strtoupper($request->nom), // Toujours en majuscules pour le nom
    //         'prenom' => $request->prenom,
    //         'date_naissance' => $request->date_naissance,
    //         'sexe' => $request->sexe,
    //         'lieu_naissance' => $request->lieu_naissance,
    //         'telephone_parent' => $request->telephone_parent,
    //         'adresse' => $request->adresse,
    //     ]);

    //     // 4. Inscription immédiate dans l'année active
    //     $anneeActive = AnneeScolaire::where('est_active', true)->first();

    //     Inscription::create([
    //         'eleve_id' => $eleve->id,
    //         'salle_id' => $request->salle_id,
    //         'annee_scolaire_id' => $anneeActive->id,
    //         'date_inscription' => now(),
    //     ]);

    //     return redirect()->route('admin.eleves.index')
    //         ->with('success', "L'élève {$eleve->nom} a été inscrit avec succès. Matricule : {$matricule}");
    // }


    // public function store(EleveRequest $request)
    // {

    //     // 1. Validation stricte
    //     $request->validated();

    //     // 2. Génération du Matricule (Ex: 26-0001)
    //     $anneeSuffixe = date('y');
    //     $totalEleves = Eleve::count();
    //     $matricule = $anneeSuffixe . '-' . str_pad($totalEleves + 1, 4, '0', STR_PAD_LEFT);



    //     // Récupère uniquement les données qui ont passé la validation
    //     $data = $request->validated();

    //     $eleve = Eleve::create([
    //         'matricule' => $matricule,
    //         'nom' => strtoupper($data['nom']),
    //         'prenom' => $data['prenom'],
    //         'date_naissance' => $data['date_naissance'],
    //         'sexe' => $data['sexe'],
    //         'lieu_naissance' => $data['lieu_naissance'] ?? null,
    //         'telephone_parent' => $data['telephone_parent'] ?? null,
    //         'adresse' => $data['adresse'] ?? null,

    //     ]);

    //     // 4. Inscription immédiate dans l'année active
    //     $anneeActive = AnneeScolaire::where('est_active', true)->first();

    //     Inscription::create([
    //         'eleve_id' => $eleve->id,
    //         'salle_id' => $request->salle_id,
    //         'annee_scolaire_id' => $anneeActive->id,
    //         'date_inscription' => now(),
    //     ]);
    //     return redirect()->route('admin.eleves.index')
    //         ->with('success', "L'élève {$eleve->nom} a été inscrit avec succès. Matricule : {$matricule}");



    // }




    public function store(EleveRequest $request)
    {
        // 1. Récupérer les données validées
        $data = $request->validated();

        // 2. Utiliser une transaction pour la sécurité des données
        return DB::transaction(function () use ($data, $request) {

            // Génération du Matricule
            $anneeSuffixe = date('y');
            $totalEleves = Eleve::count();
            $matricule = $anneeSuffixe . '-' . str_pad($totalEleves + 1, 4, '0', STR_PAD_LEFT);

            // 3. Création de l'élève
            $eleve = Eleve::create([
                'matricule' => $matricule,
                'nom' => strtoupper($data['nom']),
                'prenom' => $data['prenom'],
                'date_naissance' => $data['date_naissance'],
                'sexe' => $data['sexe'],
                'lieu_naissance' => $data['lieu_naissance'] ?? null,
                'telephone_parent' => $data['telephone_parent'] ?? null,
                'adresse' => $data['adresse'] ?? null,
            ]);

            // 4. Récupération de l'année scolaire active
            $anneeActive = AnneeScolaire::where('est_active', true)->first();

            if (!$anneeActive) {
                throw new \Exception("Aucune année scolaire n'est définie comme active.");
            }

            // 5. Inscription immédiate
            Inscription::create([
                'eleve_id' => $eleve->id,
                'classe_id' => $request->classe_id, // Utilise la colonne de ta table inscriptions
                'annee_scolaire_id' => $anneeActive->id,
                'date_inscription' => now(),
            ]);

            return redirect()->route('admin.eleves.index')
                ->with('success', "L'élève {$eleve->nom} a été inscrit avec succès. Matricule : {$matricule}");
        });
    }
}
