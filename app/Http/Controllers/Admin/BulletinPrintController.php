<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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




    public function imprimerEleve($inscriptionId, $trimestreId)
    {
        // 1. Récupérer les coordonnées de l'établissement (Table etablissements)
        $etablissement = DB::table('etablissements')->first(); // Récupère la première ligne

        $inscription = DB::table('inscriptions')
            ->join('eleves', 'inscriptions.eleve_id', '=', 'eleves.id')
            ->join('classes', 'inscriptions.classe_id', '=', 'classes.id')
            ->join('niveaux', 'classes.niveau_id', '=', 'niveaux.id')
            ->join('annee_scolaires', 'inscriptions.annee_scolaire_id', '=', 'annee_scolaires.id')
            ->where('inscriptions.id', $inscriptionId)
            ->select(
                'inscriptions.id as inscription_id',
                'inscriptions.classe_id',
                'inscriptions.annee_scolaire_id', // <-- AJOUTÉ : Pour pouvoir faire le comptage précis de la classe
                'eleves.nom as eleve_nom',
                'eleves.prenom as eleve_prenom',
                'eleves.matricule',
                'eleves.sexe',
                'eleves.date_naissance',
                'eleves.lieu_naissance',
                DB::raw("CONCAT(niveaux.nom, ' ', classes.nom) as classe_nom"),
                'annee_scolaires.libelle as annee_libelle'
            )
            ->first();

        if (!$inscription) {
            abort(404, "Données d'inscription introuvables.");
        }

        // [CORRECTION NOMBRE ÉLÈVES] : Compte le nombre exact d'inscrits dans CETTE classe pour CETTE année
        $totalElevesClasse = DB::table('inscriptions')
            ->where('classe_id', $inscription->classe_id)
            ->where('annee_scolaire_id', $inscription->annee_scolaire_id)
            ->count();










        // 2. Récupérer les informations du trimestre et ses séquences rattachées
        $trimestre = DB::table('trimestres')->where('id', $trimestreId)->first();

        $sequences = DB::table('sequences')
            ->where('trimestre_id', $trimestreId)
            ->orderBy('id', 'asc')
            ->take(2)
            ->get();

        // 3. OPTIMISATION 2 : Chargement des matières et des profs en une seule fois via LEFT JOIN
        // Ça évite de chercher le prof de chaque matière un par un plus tard
        $matieres = DB::table('classe_matiere')
            ->join('matieres', 'classe_matiere.matiere_id', '=', 'matieres.id')
            ->leftJoin('affectations', function ($join) use ($inscription) {
                $join->on('affectations.matiere_id', '=', 'classe_matiere.matiere_id')
                    ->where('affectations.classe_id', '=', $inscription->classe_id);
            })
            ->leftJoin('enseignants', 'affectations.enseignant_id', '=', 'enseignants.id')
            ->leftJoin('users', 'enseignants.user_id', '=', 'users.id')
            ->where('classe_matiere.classe_id', $inscription->classe_id)
            ->select(
                'matieres.id as matiere_id',
                'matieres.nom as matiere_nom',
                DB::raw("COALESCE(users.name, 'Non assigné') as prof_nom")
            )
            ->get();

        // 4. OPTIMISATION 3 : Aller chercher les vraies notes dans 'notes' via la table 'evaluations'
        $notesBrutes = DB::table('notes')
            ->join('evaluations', 'notes.evaluation_id', '=', 'evaluations.id')
            ->where('notes.inscription_id', $inscriptionId)
            ->whereIn('evaluations.sequence_id', $sequences->pluck('id'))
            ->select(
                'evaluations.matiere_id',
                'evaluations.sequence_id',
                DB::raw('AVG(notes.valeur) as valeur') // AVG au cas où un prof a saisi 2 évaluations dans la même séquence
            )
            ->groupBy('evaluations.matiere_id', 'evaluations.sequence_id')
            ->get();

        // On remplit le tableau associatif pour le Blade
        $notes = [];
        foreach ($notesBrutes as $note) {
            $notes[$note->matiere_id][$note->sequence_id] = $note->valeur;
        }

        // Extraction des coefficients depuis la table moyennes (qui elle est bien renseignée chez toi)
        $coefficientsBruts = DB::table('moyennes')
            ->where('inscription_id', $inscriptionId)
            ->whereIn('sequence_id', $sequences->pluck('id'))
            ->select('matiere_id', 'coefficient')
            ->distinct()
            ->get();

        $coefficients = [];
        foreach ($coefficientsBruts as $c) {
            $coefficients[$c->matiere_id] = $c->coefficient;
        }

        // 5. Récupérer le bilan global précalculé (Rang, Moyenne générale de la classe, Mentions)
        $bilan = DB::table('bilans')
            ->where('inscription_id', $inscriptionId)
            ->where('trimestre_id', $trimestreId)
            ->first();


        // Dans ton Controller de génération de PDF :
        // dd([
        //     'bilan_complet' => $bilan,
        //     'rang_recupere' => $bilan->rang ?? 'Non défini',
        //     'total_eleves' => $totalElevesClasse
        // ]);
        // 6. Compilation du PDF vertical (Portrait) via DomPDF
        $pdf = Pdf::loadView('pages.admin.pdf.bulletin-single', compact(
            'inscription',
            'trimestre',
            'sequences',
            'matieres',
            'notes',
            'coefficients',
            'bilan',
            'etablissement',
            'totalElevesClasse'

        ))->setPaper('a4', 'portrait');

        // Nettoyage du nom de fichier pour le téléchargement/flux
        $slugNom = str_replace(' ', '_', $inscription->eleve_nom . '_' . $inscription->eleve_prenom);
        return $pdf->download("Bulletin_" . $slugNom . ".pdf");
        // return $pdf->stream("Bulletin_" . $slugNom . ".pdf");
    }
}
