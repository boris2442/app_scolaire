<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\EleveRequest;
use App\Models\AnneeScolaire;
use App\Models\Eleve;
use App\Models\Inscription;
use App\Models\Niveau;
use App\Services\StudentAnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

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

    private function getKpis($anneeActiveId)
    {
        // On récupère les IDs des élèves inscrits cette année pour filtrer nos stats
        $stats = Inscription::where('annee_scolaire_id', $anneeActiveId)
            ->join('eleves', 'inscriptions.eleve_id', '=', 'eleves.id')
            ->selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN eleves.sexe = 'M' THEN 1 ELSE 0 END) as garcons,
            SUM(CASE WHEN eleves.sexe = 'F' THEN 1 ELSE 0 END) as filles,
            SUM(CASE WHEN inscriptions.statut = 'actif' THEN 1 ELSE 0 END) as confirmes
        ")
            ->first();

        return [
            'total' => $stats->total ?? 0,
            'garcons' => $stats->garcons ?? 0,
            'filles' => $stats->filles ?? 0,
            'confirmes' => $stats->confirmes ?? 0,
            'pourcentage_filles' => $stats->total > 0 ? round(($stats->filles / $stats->total) * 100) : 0,
        ];
    }
    public function index(Request $request, StudentAnalyticsService $analytics)
    {
        // AJOUTE CETTE LIGNE JUSTE ICI (une seule fois pour nettoyer)
        Cache::flush();
        $anneeActive = AnneeScolaire::where('est_active', true)->first();

        // --- APPEL DES KPIs ---
        // $kpis = $this->getKpis($anneeActive->id);
        // Récupération des KPIs via le service
        // $kpis = $analytics->getYearlyKpis($anneeActive->id);
        // On récupère tout d'un coup
        $stats = $analytics->getFullDashboardStats($anneeActive->id);


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

        return view('pages.eleves.index', compact('eleves', 'niveaux', 'anneeActive', 'stats'));
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



    public function store(EleveRequest $request)
    {
        // 1. Récupérer les données validées
        $data = $request->validated();
        // Gestion de l'image
        if ($request->hasFile('photo')) {
            // On stocke l'image dans storage/app/public/photos_eleves
            $path = $request->file('photo')->store('photos_eleves', 'public');
            $data['photo'] = $path;
        }
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
                'photo' => $data['photo'] ?? null,
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

    /**
     * Affiche le dossier complet d'un élève.
     */
    /**
     * Affiche le dossier complet d'un élève.
     * @param  \App\Models\Eleve  $eleve
     */
    //     public function show(Eleve $eleve)
    //     {
    //         // On charge les relations 'inscriptions' avec leurs classes et années scolaires
    //         // pour que la vue puisse afficher l'historique sans refaire de requêtes SQL.

    //         $eleve->load(['inscriptions.classe', 'inscriptions.anneeScolaire']);
    //  @dump($eleve->toArray());
    //         return view('pages.eleves.show', compact('eleve'));
    //     }


    public function show($id)
    {
        $eleve = Eleve::with(['inscriptions.classe', 'inscriptions.annee_scolaire', 'inscriptions.classe.niveau'])
            ->findOrFail($id);

        return view('pages.eleves.show', compact('eleve'));
    }
}
