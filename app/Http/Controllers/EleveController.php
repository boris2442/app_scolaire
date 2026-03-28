<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\EleveRequest;
use App\Models\AnneeScolaire;
use App\Models\Eleve;
use App\Models\Inscription;
use App\Models\Niveau;
use App\Services\ScolariteService;
use App\Services\StudentAnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EleveController extends Controller
{

    protected $scolarite;

    // On injecte le service via le constructeur pour l'avoir partout
    public function __construct(ScolariteService $scolarite)
    {
        $this->scolarite = $scolarite;
    }
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
        $eleves = $query->latest()->paginate(5)->withQueryString();

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







    public function edit($id)
    {
        $eleve = Eleve::with('inscriptions')->findOrFail($id);

        // Utilisation du service
        $anneeActive = $this->scolarite->getAnneeActive();
        $inscriptionActuelle = $this->scolarite->getClasseActuelle($eleve->id);

        $niveaux = Niveau::with('classes')->get();
        $sexes = Eleve::getSexeOptions();

        return view('pages.eleves.edit', compact(
            'eleve',
            'anneeActive',
            'niveaux',
            'sexes',
            'inscriptionActuelle'
        ));
    }

    public function update(EleveRequest $request, $id)
    {
        $eleve = Eleve::findOrFail($id);
        $data = $request->validated();

        return DB::transaction(function () use ($data, $request, $eleve) {

            // 1. Gestion de la Photo (Remplacement)
            if ($request->hasFile('photo')) {
                // Supprimer l'ancienne si elle existe
                if ($eleve->photo && Storage::disk('public')->exists($eleve->photo)) {
                    Storage::disk('public')->delete($eleve->photo);
                }
                $data['photo'] = $request->file('photo')->store('photos_eleves', 'public');
            }

            // 2. Mise à jour de l'élève
            $eleve->update([
                'nom' => strtoupper($data['nom']),
                'prenom' => $data['prenom'],
                'date_naissance' => $data['date_naissance'],
                'sexe' => $data['sexe'],
                'lieu_naissance' => $data['lieu_naissance'] ?? $eleve->lieu_naissance,
                'telephone_parent' => $data['telephone_parent'] ?? $eleve->telephone_parent,
                'adresse' => $data['adresse'] ?? $eleve->adresse,
                'photo' => $data['photo'] ?? $eleve->photo,
            ]);

            // 3. Mise à jour de la Classe (Inscription)
            // On cherche l'inscription de l'année active pour cet élève
            $anneeActive = AnneeScolaire::where('est_active', true)->first();

            if ($anneeActive) {
                $inscription = Inscription::where('eleve_id', $eleve->id)
                    ->where('annee_scolaire_id', $anneeActive->id)
                    ->first();

                if ($inscription) {
                    $inscription->update(['classe_id' => $request->classe_id]);
                }
            }

            return redirect()->route('admin.eleves.show', $eleve->id)
                ->with('success', "Le dossier de {$eleve->nom} a été mis à jour.");
        });
    }








    public function destroy($id)
    {
        $eleve = Eleve::findOrFail($id);

        // On archive l'élève
        $eleve->delete();

        return redirect()->route('admin.eleves.index')
            ->with('success', "L'élève {$eleve->nom} a été déplacé dans la corbeille.");
    }




    // Afficher uniquement les élèves supprimés
    public function trashed()
    {
        $elevesArchives = Eleve::onlyTrashed()
            ->orderByDesc('deleted_at')
            ->paginate(10);

        return view('pages.eleves.trashed', compact('elevesArchives'));
    }

    // Restaurer un élève
    public function restore($id)
    {
        // On utilise withTrashed() pour pouvoir trouver l'élève même s'il est "supprimé"
        $eleve = Eleve::withTrashed()->findOrFail($id);
        $eleve->restore();

        return redirect()->route('admin.eleves.index')
            ->with('success', "Le dossier de {$eleve->nom} a été restauré avec succès.");
    }

    // Suppression définitive (Optionnel)
    public function forceDelete($id)
    {
        $eleve = Eleve::withTrashed()->findOrFail($id);
        $eleve->forceDelete(); // Ici, la ligne est réellement effacée de la BD

        return redirect()->back()->with('success', "L'élève a été définitivement supprimé.");
    }
}
