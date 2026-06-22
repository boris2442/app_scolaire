<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Trimestre;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BulletinPrintController extends Controller
{
    // 1. Affichage de la Grille des Classes
    public function index(Request $request)
    {
        // On récupère l'année active (via ta logique existante)
        $anneeActive = DB::table('annee_scolaires')->where('est_active', 1)->first();

        if (!$anneeActive) {
            abort(500, "Aucune année scolaire active configurée.");
        }

        // Récupérer tous les trimestres pour le menu déroulant
        $trimestres = DB::table('trimestres')
            ->where('annee_scolaire_id', $anneeActive->id)
            ->get();

        // Récupérer le trimestre sélectionné ou prendre le premier par défaut
        $trimestreId = $request->get('trimestre_id') ?? ($trimestres->first()->id ?? null);

        // Récupérer toutes les classes avec le nombre d'élèves inscrits pour cette année
        $classes = DB::table('classes')
            ->join('niveaux', 'classes.niveau_id', '=', 'niveaux.id')
            ->leftJoin('inscriptions', function ($join) use ($anneeActive) {
                $join->on('inscriptions.classe_id', '=', 'classes.id')
                    ->where('inscriptions.annee_scolaire_id', '=', $anneeActive->id);
            })
            ->select(
                'classes.id',
                'niveaux.nom as niveau_nom',
                'classes.nom as classe_nom',
                DB::raw('COUNT(inscriptions.id) as total_eleves')
            )
            ->groupBy('classes.id', 'niveaux.nom', 'classes.nom')
            ->orderBy('niveaux.nom', 'desc')
            ->get();

        return view('pages.admin.bulletins.index', compact('classes', 'trimestres', 'trimestreId'));
    }

    // 2. Affichage du Hub d'une classe (La liste des élèves)
    public function classeHub($classeId, Request $request)
    {
        $trimestreId = $request->get('trimestre_id');


        // 2. SÉCURITÉ : Si aucun trimestre n'est sélectionné, on prend le trimestre actif par défaut
        // if (!$trimestreId) {
        //     $trimestreActive = Trimestre::where('est_actif', true)->first();
        //     $trimestreId = $trimestreActive ? $trimestreActive->id : \App\Models\Trimestre::first()?->id;
        // }



        $anneeActive = DB::table('annee_scolaires')->where('est_active', 1)->first();

        // Informations de la classe
        $classe = DB::table('classes')
            ->join('niveaux', 'classes.niveau_id', '=', 'niveaux.id')
            ->where('classes.id', $classeId)
            ->select('classes.id', DB::raw("CONCAT(niveaux.nom, ' ', classes.nom) as nom"))
            ->first();

        // Liste des élèves inscrits dans cette classe
        $eleves = DB::table('inscriptions')
            ->join('eleves', 'inscriptions.eleve_id', '=', 'eleves.id')
            ->where('inscriptions.classe_id', $classeId)
            ->where('inscriptions.annee_scolaire_id', $anneeActive->id)
            ->select('inscriptions.id as inscription_id', 'eleves.nom', 'eleves.prenom', 'eleves.matricule')
            ->orderBy('eleves.nom', 'asc')
            ->get();

        return view('pages.admin.bulletins.classe-hub', compact('classe', 'eleves', 'trimestreId'));
    }




    // 1. Impression d'un SEUL élève
    public function imprimerEleve($inscriptionId, $trimestreId)
    {
        $etablissement = DB::table('etablissements')->first();
        $trimestre = DB::table('trimestres')->where('id', $trimestreId)->first();
        $sequences = DB::table('sequences')->where('trimestre_id', $trimestreId)->orderBy('id', 'asc')->take(2)->get();

        // On récupère les données de cet élève et on les met dans un tableau []
        $bulletins = [
            $this->chargerDonneesBulletin($inscriptionId, $trimestreId, $sequences)
        ];

        $pdf = Pdf::loadView('pages.admin.pdf.bulletin-single', compact('bulletins', 'trimestre', 'sequences', 'etablissement'))->setPaper('a4', 'portrait');

        return $pdf->download("Bulletin_Eleve.pdf");
    }

    // 2. NOUVEAU : Impression de TOUTE la classe d'un coup
    public function imprimerClasse($classeId, $trimestreId)
    {
        $etablissement = DB::table('etablissements')->first();
        $trimestre = DB::table('trimestres')->where('id', $trimestreId)->first();
        $sequences = DB::table('sequences')->where('trimestre_id', $trimestreId)->orderBy('id', 'asc')->take(2)->get();
        $anneeActive = DB::table('annee_scolaires')->where('est_active', 1)->first();

        // On récupère tous les IDs d'inscriptions de cette classe
        $inscriptionIds = DB::table('inscriptions')
            ->where('classe_id', $classeId)
            ->where('annee_scolaire_id', $anneeActive->id)
            ->pluck('id');



        // 2. NOUVEAU : On récupère TOUTES les notes de la classe en 1 seule requête SQL
        $allNotes = DB::table('notes')
            ->join('evaluations', 'notes.evaluation_id', '=', 'evaluations.id')
            ->whereIn('notes.inscription_id', $inscriptionIds)
            ->whereIn('evaluations.sequence_id', $sequences->pluck('id'))
            ->select('notes.*', 'evaluations.matiere_id', 'evaluations.sequence_id')
            ->get()
            ->groupBy('inscription_id'); // Très important pour trier les notes par élève









        // On boucle pour charger les données de chaque élève de la classe
        $bulletins = [];
        // foreach ($inscriptionIds as $id) {
        //     $bulletins[] = $this->chargerDonneesBulletin($id, $trimestreId, $sequences);
        // }



        // foreach ($inscriptionIds as $id) {
        //     // Au lieu de demander à la fonction de retourner chercher dans la BDD,
        //     // on lui passe directement les notes de cet élève déjà récupérées en mémoire.
        //     $notesEleve = $allNotes->get($id, collect());

        //     $bulletins[] = $this->chargerDonneesBulletin($id, $trimestreId, $sequences, $notesEleve);
        // }


        foreach ($inscriptionIds as $id) {
            // Récupère les notes dans le panier (le "allNotes")
            $notesEleve = $allNotes->get($id, collect());

            // On passe $notesEleve en 4ème argument
            $bulletins[] = $this->chargerDonneesBulletin($id, $trimestreId, $sequences, $notesEleve);
        }




        $pdf = Pdf::loadView('pages.admin.pdf.bulletin-single', compact('bulletins', 'trimestre', 'sequences', 'etablissement'))->setPaper('a4', 'portrait');

        return $pdf->download("Bulletins_Classe.pdf");
    }



    // 3. LA LOGIQUE UNIQUE (Ton code actuel, mis dans une fonction réutilisable)
    // private function chargerDonneesBulletin($inscriptionId, $trimestreId, $sequences, $notesEleve = null)
    // {
    //     $inscription = DB::table('inscriptions')
    //         ->join('eleves', 'inscriptions.eleve_id', '=', 'eleves.id')
    //         ->join('classes', 'inscriptions.classe_id', '=', 'classes.id')
    //         ->join('niveaux', 'classes.niveau_id', '=', 'niveaux.id')
    //         ->join('annee_scolaires', 'inscriptions.annee_scolaire_id', '=', 'annee_scolaires.id')
    //         ->where('inscriptions.id', $inscriptionId)
    //         ->select(
    //             'inscriptions.id as inscription_id',
    //             'inscriptions.classe_id',
    //             'inscriptions.annee_scolaire_id',
    //             'eleves.nom as eleve_nom',
    //             'eleves.prenom as eleve_prenom',
    //             'eleves.matricule',
    //             'eleves.sexe',
    //             'eleves.date_naissance',
    //             'eleves.lieu_naissance',
    //             DB::raw("CONCAT(niveaux.nom, ' ', classes.nom) as classe_nom"),
    //             'annee_scolaires.libelle as annee_libelle'
    //         )->first();

    //     $totalElevesClasse = DB::table('inscriptions')
    //         ->where('classe_id', $inscription->classe_id)
    //         ->where('annee_scolaire_id', $inscription->annee_scolaire_id)
    //         ->count();

    //     $matieres = DB::table('classe_matiere')
    //         ->join('matieres', 'classe_matiere.matiere_id', '=', 'matieres.id')
    //         ->leftJoin('affectations', function ($join) use ($inscription) {
    //             $join->on('affectations.matiere_id', '=', 'classe_matiere.matiere_id')
    //                 ->where('affectations.classe_id', '=', $inscription->classe_id);
    //         })
    //         ->leftJoin('enseignants', 'affectations.enseignant_id', '=', 'enseignants.id')
    //         ->leftJoin('users', 'enseignants.user_id', '=', 'users.id')
    //         ->where('classe_matiere.classe_id', $inscription->classe_id)
    //         ->select('matieres.id as matiere_id', 'matieres.nom as matiere_nom', DB::raw("COALESCE(users.name, 'Non assigné') as prof_nom"))
    //         ->get();

    //     $notesBrutes = DB::table('notes')
    //         ->join('evaluations', 'notes.evaluation_id', '=', 'evaluations.id')
    //         ->where('notes.inscription_id', $inscriptionId)
    //         ->whereIn('evaluations.sequence_id', $sequences->pluck('id'))
    //         ->select('evaluations.matiere_id', 'evaluations.sequence_id', DB::raw('AVG(notes.valeur) as valeur'))
    //         ->groupBy('evaluations.matiere_id', 'evaluations.sequence_id')
    //         ->get();

    //     $notes = [];
    //     foreach ($notesBrutes as $note) {
    //         $notes[$note->matiere_id][$note->sequence_id] = $note->valeur;
    //     }

    //     $coefficientsBruts = DB::table('moyennes')
    //         ->where('inscription_id', $inscriptionId)
    //         ->whereIn('sequence_id', $sequences->pluck('id'))
    //         ->select('matiere_id', 'coefficient')->distinct()->get();

    //     $coefficients = [];
    //     foreach ($coefficientsBruts as $c) {
    //         $coefficients[$c->matiere_id] = $c->coefficient;
    //     }

    //     // CORRECTION ICI : On cible explicitement le bilan global du trimestre
    //     $bilan = DB::table('bilans')
    //         ->where('inscription_id', $inscriptionId)
    //         ->where('trimestre_id', $trimestreId)
    //         ->whereNull('sequence_id') // ⚠️ Exclut les séquences pour prendre le vrai trimestre !
    //         ->first();

    //     return compact('inscription', 'totalElevesClasse', 'matieres', 'notes', 'coefficients', 'bilan');
    // }







    private function chargerDonneesBulletin($inscriptionId, $trimestreId, $sequences, $notesEleve = null)
    {
        // 1. Récupération des informations de l'élève et de l'inscription
        $inscription = DB::table('inscriptions')
            ->join('eleves', 'inscriptions.eleve_id', '=', 'eleves.id')
            ->join('classes', 'inscriptions.classe_id', '=', 'classes.id')
            ->join('niveaux', 'classes.niveau_id', '=', 'niveaux.id')
            ->join('annee_scolaires', 'inscriptions.annee_scolaire_id', '=', 'annee_scolaires.id')
            ->where('inscriptions.id', $inscriptionId)
            ->select(
                'inscriptions.id as inscription_id',
                'inscriptions.classe_id',
                'inscriptions.annee_scolaire_id',
                'eleves.nom as eleve_nom',
                'eleves.prenom as eleve_prenom',
                'eleves.matricule',
                'eleves.sexe',
                'eleves.date_naissance',
                'eleves.lieu_naissance',
                DB::raw("CONCAT(niveaux.nom, ' ', classes.nom) as classe_nom"),
                'annee_scolaires.libelle as annee_libelle'
            )->first();

        $totalElevesClasse = DB::table('inscriptions')
            ->where('classe_id', $inscription->classe_id)
            ->where('annee_scolaire_id', $inscription->annee_scolaire_id)
            ->count();

        // 2. Récupération des matières
        $matieres = DB::table('classe_matiere')
            ->join('matieres', 'classe_matiere.matiere_id', '=', 'matieres.id')
            ->leftJoin('affectations', function ($join) use ($inscription) {
                $join->on('affectations.matiere_id', '=', 'classe_matiere.matiere_id')
                    ->where('affectations.classe_id', '=', $inscription->classe_id);
            })
            ->leftJoin('enseignants', 'affectations.enseignant_id', '=', 'enseignants.id')
            ->leftJoin('users', 'enseignants.user_id', '=', 'users.id')
            ->where('classe_matiere.classe_id', $inscription->classe_id)
            ->select('matieres.id as matiere_id', 'matieres.nom as matiere_nom', DB::raw("COALESCE(users.name, 'Non assigné') as prof_nom"))
            ->get();

        // 3. Récupération des notes (Optimisée avec le paramètre $notesEleve)
        if ($notesEleve !== null) {
            // On utilise les données déjà chargées dans le contrôleur (Mode Rapide)
            $notesBrutes = $notesEleve;
        } else {
            // Mode Normal : on va chercher les données dans la base
            $notesBrutes = DB::table('notes')
                ->join('evaluations', 'notes.evaluation_id', '=', 'evaluations.id')
                ->where('notes.inscription_id', $inscriptionId)
                ->whereIn('evaluations.sequence_id', $sequences->pluck('id'))
                ->select('evaluations.matiere_id', 'evaluations.sequence_id', DB::raw('AVG(notes.valeur) as valeur'))
                ->groupBy('evaluations.matiere_id', 'evaluations.sequence_id')
                ->get();
        }

        $notes = [];
        foreach ($notesBrutes as $note) {
            $notes[$note->matiere_id][$note->sequence_id] = $note->valeur;
        }

        // 4. Récupération des coefficients
        $coefficientsBruts = DB::table('moyennes')
            ->where('inscription_id', $inscriptionId)
            ->whereIn('sequence_id', $sequences->pluck('id'))
            ->select('matiere_id', 'coefficient')->distinct()->get();

        $coefficients = [];
        foreach ($coefficientsBruts as $c) {
            $coefficients[$c->matiere_id] = $c->coefficient;
        }

        // 5. Récupération du bilan
        $bilan = DB::table('bilans')
            ->where('inscription_id', $inscriptionId)
            ->where('trimestre_id', $trimestreId)
            ->whereNull('sequence_id')
            ->first();

        return compact('inscription', 'totalElevesClasse', 'matieres', 'notes', 'coefficients', 'bilan');
    }
}
