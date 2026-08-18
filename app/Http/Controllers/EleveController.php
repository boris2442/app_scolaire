<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\EleveRequest;
use App\Imports\StudentImport;
use App\Models\AnneeScolaire;
use App\Models\Classe;
use App\Models\Eleve;
use App\Models\Etablissement;
use App\Models\Inscription;
use App\Models\Niveau;
use App\Services\ScolariteService;
use App\Services\StudentAnalyticsService;
use Barryvdh\DomPDF\PDF;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class EleveController extends Controller
{

    protected $scolarite;

    // On injecte le service via le constructeur pour l'avoir partout
    public function __construct(ScolariteService $scolarite)
    {
        $this->scolarite = $scolarite;
    }


    public function index(Request $request, StudentAnalyticsService $analytics)
    {
        $anneeId = $request->input('annee_id');
        $anneeActive = $anneeId
            ? AnneeScolaire::findOrFail($anneeId)
            : AnneeScolaire::where('est_active', true)->first();

        $stats = $analytics->getFullDashboardStats($anneeActive->id);

        // MODIFICATION ICI : On charge directement la classe (plus de relation niveau)
        $query = Eleve::whereHas('inscriptions', function ($q) use ($anneeActive) {
            $q->where('annee_scolaire_id', $anneeActive->id);
        })->with(['inscriptions' => function ($q) use ($anneeActive) {
            $q->where('annee_scolaire_id', $anneeActive->id)->with('classe');
        }]);

        // MODIFICATION DU FILTRE : Remplacement du filtre par niveau_id par un filtre par classe_id (ou adapté selon tes besoins)
        if ($request->filled('classe_id')) {
            $query->whereHas('inscriptions', function ($q) use ($request) {
                $q->where('classe_id', $request->classe_id);
            });
        }

        // $eleves = $query->latest()->paginate(5)->withQueryString();
        $eleves = $query
            ->join('inscriptions', 'eleves.id', '=', 'inscriptions.eleve_id')
            ->join('classes', 'inscriptions.classe_id', '=', 'classes.id')
            ->orderBy('classes.nom', 'asc')
            ->orderBy('eleves.nom', 'asc')
            ->orderBy('eleves.prenom', 'asc')
            ->select('eleves.*')
            ->paginate(25)
            ->withQueryString();

        // On récupère directement la liste des classes pour les filtres de la vue
        $classes = Classe::all();

        return view('pages.eleves.index', compact('eleves', 'classes', 'anneeActive', 'stats'));
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
    // public function create()

    // {
    //     //afficher le sexe

    //     $anneeActive = AnneeScolaire::where('est_active', true)->first();
    //     $niveaux = Niveau::with('classes')->get();
    //     // On récupère les sexes depuis la structure de la BD
    //     $sexes = Eleve::getSexeOptions();
    //     return view('pages.eleves.create', compact('anneeActive', 'niveaux', 'sexes'));
    // }

    public function create()
    {
        // Récupérer l'année active
        $anneeActive = AnneeScolaire::where('est_active', true)->first();

        // On récupère directement la liste des classes (plus de niveaux)
        $classes = Classe::all();

        // On récupère les sexes depuis la structure de la BD
        $sexes = Eleve::getSexeOptions();

        return view('pages.eleves.create', compact('anneeActive', 'classes', 'sexes'));
    }


    public function store(EleveRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('photos_eleves', 'public');
        }

        // On récupère le résultat de la transaction (qui sera notre objet $eleve)
        // $eleve = DB::transaction(function () use ($data, $request) {

        //     $nouveauEleve = Eleve::create([
        //         'nom' => strtoupper($data['nom']),
        //         'prenom' => $data['prenom'],
        //         'date_naissance' => $data['date_naissance'],
        //         'sexe' => $data['sexe'],
        //         'lieu_naissance' => $data['lieu_naissance'] ?? null,
        //         'telephone_parent' => $data['telephone_parent'] ?? null,
        //         'adresse' => $data['adresse'] ?? null,
        //         'photo' => $data['photo'] ?? null,
        //     ]);

        //     $anneeActive = $this->scolarite->getAnneeActive();

        //     $debut = Carbon::parse($anneeActive->date_debut)->format('y');
        //     $fin = Carbon::parse($anneeActive->date_fin)->format('y');

        //     $matricule = $debut . $fin . str_pad($nouveauEleve->id, 5, '0', STR_PAD_LEFT);

        //     $nouveauEleve->update(['matricule' => $matricule]);

        //     Inscription::create([
        //         'eleve_id' => $nouveauEleve->id,
        //         'classe_id' => $request->classe_id,
        //         'annee_scolaire_id' => $anneeActive->id,
        //         'date_inscription' => now(),
        //         'est_redoublant' => $request->has('est_redoublant'),
        //     ]);

        //     // On retourne l'objet élève ici
        //     return $nouveauEleve;
        // });





        $eleve = DB::transaction(function () use ($data, $request) {

            $nouveauEleve = Eleve::create([
                'nom'              => strtoupper($data['nom']),
                'prenom'           => $data['prenom'],
                'date_naissance'   => $data['date_naissance'],
                'sexe'             => $data['sexe'],
                'lieu_naissance'   => $data['lieu_naissance'] ?? null,
                'telephone_parent' => $data['telephone_parent'] ?? null,
                'adresse'          => $data['adresse'] ?? 'Non renseigné',
                'photo'            => $data['photo'] ?? null,
                'est_actif'        => true,
            ]);

            $anneeActive = $this->scolarite->getAnneeActive();

            // UTILISATION DE LA NOUVELLE FONCTION DE MATRICULE
            Eleve::genererEtAttribuerMatricule($nouveauEleve, $anneeActive->id);

            Inscription::create([
                'eleve_id'          => $nouveauEleve->id,
                'classe_id'         => $request->classe_id,
                'annee_scolaire_id' => $anneeActive->id,
                'date_inscription'  => now(),
                'est_redoublant'    => $request->has('est_redoublant'),
            ]);

            return $nouveauEleve;
        });




        // Maintenant, $eleve est parfaitement défini ici
        return redirect()->route('admin.students.index')
            ->with('success', "Inscription réussie ! Matricule : {$eleve->matricule}");
    }





    /**
     * Affiche le dossier complet d'un élève.
     */
    /**
     * Affiche le dossier complet d'un élève.
     * @param  \App\Models\Eleve  $eleve
     */


    public function show($id)
    {
        // On charge les inscriptions, l'année scolaire et la classe (sans la relation 'niveau')
        $eleve = Eleve::with(['inscriptions.classe', 'inscriptions.annee_scolaire'])
            ->findOrFail($id);

        return view('pages.eleves.show', compact('eleve'));
    }





    public function edit($id)
    {
        $eleve = Eleve::with('inscriptions')->findOrFail($id);

        // Utilisation du service
        $anneeActive = $this->scolarite->getAnneeActive();
        $inscriptionActuelle = $this->scolarite->getClasseActuelle($eleve->id);

        // On récupère directement les classes (plus de niveaux)
        $classes = Classe::all();
        $sexes = Eleve::getSexeOptions();

        return view('pages.eleves.edit', compact(
            'eleve',
            'anneeActive',
            'classes',
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

            return redirect()->route('admin.students.show', $eleve->id)
                ->with('success', "Le dossier de {$eleve->nom} a été mis à jour.");
        });
    }








    public function destroy($id)
    {
        $eleve = Eleve::findOrFail($id);

        // On archive l'élève
        $eleve->delete();

        return redirect()->route('admin.students.index')
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

        return redirect()->route('admin.students.index')
            ->with('success', "Le dossier de {$eleve->nom} a été restauré avec succès.");
    }

    // Suppression définitive (Optionnel)
    public function forceDelete($id)
    {
        $eleve = Eleve::withTrashed()->findOrFail($id);
        $eleve->forceDelete(); // Ici, la ligne est réellement effacée de la BD

        return redirect()->back()->with('success', "L'élève a été définitivement supprimé.");
    }














    public function imprimer(Request $request)
    {
        // 1. Validation : on s'assure qu'une classe est bien fournie
        $request->validate([
            'classe_id' => 'required|exists:classes,id'
        ]);

        $anneeActive = AnneeScolaire::where('est_active', true)->first();
        // Récupération des infos de l'école
        $etablissement = Etablissement::first();

        // 2. Récupération des données filtrées (plus de relation niveau)
        $eleves = Eleve::whereHas('inscriptions', function ($q) use ($request, $anneeActive) {
            $q->where('classe_id', $request->classe_id)
                ->where('annee_scolaire_id', $anneeActive->id);
        })->with(['inscriptions' => function ($q) use ($anneeActive) {
            $q->where('annee_scolaire_id', $anneeActive->id)->with('classe');
        }])
            ->orderBy('nom', 'asc')
            ->orderBy('prenom', 'asc')
            ->get();

        // Récupération de la classe pour le titre du document (sans with('niveau'))
        $classe = Classe::findOrFail($request->classe_id);

        // 3. Génération du PDF
        $pdf = \PDF::loadView('pages.eleves.pdf.liste', compact('eleves', 'classe', 'anneeActive', 'etablissement'));

        // 4. Téléchargement ou affichage
        return $pdf->download('liste_eleves_' . $classe->nom . '.pdf');
    }


    // Dans ton Contrôleur
    public function importer(Request $request)
    {
        $request->validate([
            'fichier_excel' => 'required|mimes:xlsx,xls,csv',
            'classe_id'     => 'required|exists:classes,id',
            'annee_id'      => 'required|exists:annee_scolaires,id', // Note bien le nom du champ du formulaire : 'annee_id'
        ]);

        try {
            // Ordre strict : Classe d'abord, Année ensuite
            Excel::import(
                new StudentImport($request->classe_id, $request->annee_id),
                $request->file('fichier_excel')
            );

            return redirect()->back()->with('success', 'Importation réussie !');
        } catch (\Exception $e) {
            dd("Erreur critique : " . $e->getMessage());
        }
    }
}
