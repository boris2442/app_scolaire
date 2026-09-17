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
        $anneeActive = DB::table('annee_scolaires')->where('est_active', 1)->first();

        if (! $anneeActive) {
            abort(500, 'Aucune année scolaire active configurée.');
        }

        $trimestres = DB::table('trimestres')
            ->where('annee_scolaire_id', $anneeActive->id)
            ->get();

        $trimestreId = $request->get('trimestre_id') ?? ($trimestres->first()->id ?? null);

        $classes = DB::table('classes')
            ->leftJoin('inscriptions', function ($join) use ($anneeActive) {
                $join->on('inscriptions.classe_id', '=', 'classes.id')
                    ->where('inscriptions.annee_scolaire_id', '=', $anneeActive->id);
            })
            ->select(
                'classes.id',
                'classes.nom as classe_nom',
                DB::raw('COUNT(inscriptions.id) as total_eleves')
            )
            ->groupBy('classes.id', 'classes.nom')
            ->orderBy('classes.nom', 'asc')
            ->get();

        return view('pages.admin.bulletins.index', compact('classes', 'trimestres', 'trimestreId'));
    }

    // 2. Affichage du Hub d'une classe (La liste des élèves)
    public function classeHub($classeId, Request $request)
    {
        $trimestreId = $request->get('trimestre_id');
        $anneeActive = DB::table('annee_scolaires')->where('est_active', 1)->first();

        // VÉRIFICATION DE LA CLÔTURE DES SÉQUENCES DU TRIMESTRE
        if ($trimestreId) {
            $sequencesNonCloses = DB::table('sequences')
                ->where('trimestre_id', $trimestreId)
                ->where('is_closed', 0)
                ->exists();

            if ($sequencesNonCloses) {
                return redirect()
                    ->back()
                    ->with('error', 'Les évaluations doivent être closes pour accéder à cette page et imprimer ');
            }
        }

        $classe = DB::table('classes')
            ->where('classes.id', $classeId)
            ->select('classes.id', 'classes.nom')
            ->first();

        $eleves = DB::table('inscriptions')
            ->join('eleves', 'inscriptions.eleve_id', '=', 'eleves.id')
            ->where('inscriptions.classe_id', $classeId)
            ->where('inscriptions.annee_scolaire_id', $anneeActive->id)
            ->select('inscriptions.id as inscription_id', 'eleves.nom', 'eleves.prenom', 'eleves.matricule', 'eleves.sexe', 'eleves.date_naissance', 'eleves.lieu_naissance')
            ->orderBy('eleves.nom', 'asc')
            ->get();

        return view('pages.admin.bulletins.classe-hub', compact('classe', 'eleves', 'trimestreId'));
    }

    // 3. Impression d'un SEUL élève (Lecture directe depuis `moyennes`)
    public function imprimerEleve($inscriptionId, $trimestreId)
    {
        $etablissement = DB::table('etablissements')->first();
        $trimestre = DB::table('trimestres')->where('id', $trimestreId)->first();
        $sequences = DB::table('sequences')->where('trimestre_id', $trimestreId)->orderBy('id', 'asc')->take(2)->get();

        // Charger les données de l'élève
        $bulletin = $this->chargerDonneesBulletinDepuisMoyennes($inscriptionId, $trimestreId, $sequences);

        // Récupération des statistiques globales pré-calculées de la classe pour ce trimestre
        $stats = $this->obtenirStatistiquesClasse($bulletin['inscription']->classe_id, $trimestreId);

        $bulletins = [$bulletin];

        $pdf = Pdf::loadView('pages.admin.pdf.bulletin-single', compact('bulletins', 'trimestre', 'sequences', 'etablissement', 'stats'))
            ->setPaper('a4', 'portrait');

        return $pdf->download("Bulletin_{$bulletin['inscription']->eleve_nom}.pdf");
    }

    // 4. Impression de TOUTE la classe d'un coup (Optimisée 100% SQL)
    public function imprimerClasse($classeId, $trimestreId)
    {
        $etablissement = DB::table('etablissements')->first();
        $trimestre = DB::table('trimestres')->where('id', $trimestreId)->first();
        $sequences = DB::table('sequences')->where('trimestre_id', $trimestreId)->orderBy('id', 'asc')->take(2)->get();
        $anneeActive = DB::table('annee_scolaires')->where('est_active', 1)->first();

        $inscriptionIds = DB::table('inscriptions')
            ->where('classe_id', $classeId)
            ->where('annee_scolaire_id', $anneeActive->id)
            ->pluck('id');

        $bulletins = [];
        foreach ($inscriptionIds as $id) {
            $bulletins[] = $this->chargerDonneesBulletinDepuisMoyennes($id, $trimestreId, $sequences);
        }

        $stats = $this->obtenirStatistiquesClasse($classeId, $trimestreId);

        $pdf = Pdf::loadView('pages.admin.pdf.bulletin-single', compact('bulletins', 'trimestre', 'sequences', 'etablissement', 'stats'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('Bulletins_Classe.pdf');
    }

    /**
     * Reconstitution instantanée du bulletin depuis la table 'moyennes'
     */
    private function chargerDonneesBulletinDepuisMoyennes($inscriptionId, $trimestreId, $sequences)
    {
        $inscription = DB::table('inscriptions')
            ->join('eleves', 'inscriptions.eleve_id', '=', 'eleves.id')
            ->join('classes', 'inscriptions.classe_id', '=', 'classes.id')
            ->join('annee_scolaires', 'inscriptions.annee_scolaire_id', '=', 'annee_scolaires.id')
            ->where('inscriptions.id', $inscriptionId)
            ->select(
                'inscriptions.id as inscription_id',
                'inscriptions.classe_id',
                'inscriptions.est_redoublant',
                'inscriptions.annee_scolaire_id',
                'eleves.nom as eleve_nom',
                'eleves.prenom as eleve_prenom',
                'eleves.matricule',
                'eleves.sexe',
                'eleves.date_naissance',
                'eleves.lieu_naissance',
                'classes.nom as classe_nom',
                'classes.section',
                'annee_scolaires.libelle as annee_libelle'
            )->first();

        $totalElevesClasse = DB::table('inscriptions')
            ->where('classe_id', $inscription->classe_id)
            ->where('annee_scolaire_id', $inscription->annee_scolaire_id)
            ->count();

        $suiviDisciplinaire = DB::table('suivi_disciplinaires')
            ->where('inscription_id', $inscriptionId)
            ->where('trimestre_id', $trimestreId)
            ->first();

        // Récupération directe des moyennes pré-calculées pour les séquences du trimestre
        $sequenceIds = $sequences->pluck('id');
        $moyennesBrutes = DB::table('moyennes')
            ->where('inscription_id', $inscriptionId)
            ->whereIn('sequence_id', $sequenceIds)
            ->get();

        $notes = [];
        $coefficients = [];
        foreach ($moyennesBrutes as $m) {
            $notes[$m->matiere_id][$m->sequence_id] = $m->valeur;
            $coefficients[$m->matiere_id] = $m->coefficient;
        }

        // Récupération des matières structurées par groupe
        $matieres = DB::table('classe_matiere')
            ->join('matieres', 'classe_matiere.matiere_id', '=', 'matieres.id')
            ->leftJoin('groupes_matieres', 'matieres.groupe_matiere_id', '=', 'groupes_matieres.id')
            ->leftJoin('affectations', function ($join) use ($inscription) {
                $join->on('affectations.matiere_id', '=', 'classe_matiere.matiere_id')
                    ->where('affectations.classe_id', '=', $inscription->classe_id)
                    // CORRECTION ICI : Filtrer par l'année scolaire de l'inscription
                    ->where('affectations.annee_scolaire_id', '=', $inscription->annee_scolaire_id);
            })
            ->leftJoin('enseignants', 'affectations.enseignant_id', '=', 'enseignants.id')
            ->leftJoin('users', 'enseignants.user_id', '=', 'users.id')
            ->where('classe_matiere.classe_id', $inscription->classe_id)
            ->select(
                'matieres.id as matiere_id',
                'matieres.nom as matiere_nom',
                'classe_matiere.coefficient',
                'groupes_matieres.id as groupe_id',
                'groupes_matieres.nom as groupe_nom',
                'groupes_matieres.ordre as groupe_ordre',
                DB::raw("GROUP_CONCAT(DISTINCT users.name SEPARATOR ' / ') as prof_nom")
            )
            ->groupBy(
                'matieres.id',
                'matieres.nom',
                'classe_matiere.coefficient',
                'groupes_matieres.id',
                'groupes_matieres.nom',
                'groupes_matieres.ordre'
            )
            ->orderBy('groupes_matieres.ordre', 'asc')
            ->get()
            ->groupBy('groupe_id');

        // --- CALCUL EN TEMPS RÉEL (Remplace la requête sur la table $bilan) ---

        // 1. Moyenne générale de cet élève pour le trimestre
        $moyenneEleve = DB::table('moyennes')
            ->where('inscription_id', $inscriptionId)
            ->whereIn('sequence_id', $sequenceIds)
            ->select(DB::raw('SUM(total_points) / SUM(coefficient) as moyenne_trimestre'))
            ->value('moyenne_trimestre');

        $moyenneEleve = $moyenneEleve ? round($moyenneEleve, 2) : 0;

        // 2. Calcul du Rang de cet élève dans la classe pour le trimestre
        $rangEleve = DB::table('moyennes')
            ->join('inscriptions', 'moyennes.inscription_id', '=', 'inscriptions.id')
            ->where('inscriptions.classe_id', $inscription->classe_id)
            ->where('inscriptions.annee_scolaire_id', $inscription->annee_scolaire_id) // FIX 1 : Filtrer par l'année active
            ->whereIn('moyennes.sequence_id', $sequenceIds)
            ->select('moyennes.inscription_id', DB::raw('ROUND(SUM(total_points) / SUM(coefficient), 2) as moy_trim'))
            ->groupBy('moyennes.inscription_id')
            ->havingRaw('ROUND(SUM(total_points) / SUM(coefficient), 2) > ?', [$moyenneEleve]) // FIX 2 : Comparer les moyennes arrondies
            ->get()
            ->count() + 1;

        return [
            'inscription' => $inscription,
            'totalElevesClasse' => $totalElevesClasse,
            'matieres' => $matieres,
            'notes' => $notes,
            'coefficients' => $coefficients,
            'suivi' => $suiviDisciplinaire,
            'moyenneEleve' => $moyenneEleve,
            'rang' => $rangEleve,
        ];
    }

    /**
     * Calcul léger des statistiques globales de la classe
     */
    private function obtenirStatistiquesClasse($classeId, $trimestreId)
    {
        $sequenceIds = DB::table('sequences')
            ->where('trimestre_id', $trimestreId)
            ->pluck('id');

        if ($sequenceIds->isEmpty()) {
            return ['moyenne' => 0, 'min' => 0, 'max' => 0, 'taux_reussite' => 0];
        }

        // Récupère la moyenne trimestrielle calculée pour chaque élève de la classe
        $moyennesEleves = DB::table('moyennes')
            ->join('inscriptions', 'moyennes.inscription_id', '=', 'inscriptions.id')
            ->where('inscriptions.classe_id', $classeId)
            ->whereIn('moyennes.sequence_id', $sequenceIds)
            ->select('moyennes.inscription_id', DB::raw('SUM(total_points) / SUM(coefficient) as moyenne_trimestre'))
            ->groupBy('moyennes.inscription_id')
            ->get();

        if ($moyennesEleves->isEmpty()) {
            return ['moyenne' => 0, 'min' => 0, 'max' => 0, 'taux_reussite' => 0];
        }

        $total = $moyennesEleves->count();
        $admis = $moyennesEleves->where('moyenne_trimestre', '>=', 10)->count();

        return [
            'moyenne' => round($moyennesEleves->avg('moyenne_trimestre'), 2),
            'min' => round($moyennesEleves->min('moyenne_trimestre'), 2),
            'max' => round($moyennesEleves->max('moyenne_trimestre'), 2),
            'taux_reussite' => round(($admis / $total) * 100, 2),
        ];
    }

    public function imprimerTableauHonneur($classeId, $trimestreId)
    {
        $etablissement = DB::table('etablissements')->first();
        $trimestre = DB::table('trimestres')->where('id', $trimestreId)->first();
        $classe = DB::table('classes')->where('id', $classeId)->first();
        $anneeActive = DB::table('annee_scolaires')->where('est_active', 1)->first();

        // Récupération directe depuis la table 'bilans' (Seuil >= 12)
        $resultats = DB::table('bilans')
            ->join('inscriptions', 'bilans.inscription_id', '=', 'inscriptions.id')
            ->join('eleves', 'inscriptions.eleve_id', '=', 'eleves.id')
            ->where('inscriptions.classe_id', $classeId)
            ->where('inscriptions.annee_scolaire_id', $anneeActive->id)
            ->where('bilans.trimestre_id', $trimestreId)
            ->whereNull('bilans.sequence_id')
            ->where('bilans.moyenne', '>=', 12)
            ->select('eleves.nom', 'eleves.prenom', 'eleves.sexe', 'bilans.moyenne')
            ->orderBy('bilans.moyenne', 'desc')
            ->get();

        $pdf = Pdf::loadView('pages.admin.pdf.tableau-honneur', compact('classe', 'trimestre', 'etablissement', 'resultats', 'anneeActive'))
            ->setPaper('a4', 'landscape');

        return $pdf->download("Tableau_Honneur_{$classe->nom}.pdf");
    }

    public function imprimerStatsClasse($classeId, $trimestreId)
    {
        $etablissement = DB::table('etablissements')->first();
        $trimestre = DB::table('trimestres')->where('id', $trimestreId)->first();
        $classe = DB::table('classes')->where('id', $classeId)->first();
        $anneeActive = DB::table('annee_scolaires')->where('est_active', 1)->first();

        $sequences = DB::table('sequences')
            ->where('trimestre_id', $trimestreId)
            ->orderBy('id', 'asc')
            ->pluck('id');

        // 1. Récupération des élèves inscrits
        $inscriptionsEleves = DB::table('inscriptions')
            ->join('eleves', 'inscriptions.eleve_id', '=', 'eleves.id')
            ->where('inscriptions.classe_id', $classeId)
            ->where('inscriptions.annee_scolaire_id', $anneeActive->id)
            ->select('inscriptions.id as inscription_id', 'eleves.nom', 'eleves.prenom')
            ->get();

        $totalEleves = $inscriptionsEleves->count();

        // Initialisation uniforme du tableau des tranches
        $tranchesInitiales = [
            'excellence' => 0,
            'tres_bien' => 0,
            'bien' => 0,
            'assez_bien' => 0,
            'passable' => 0,
            'echec' => 0,
        ];

        if ($totalEleves === 0 || $sequences->isEmpty()) {
            $statsGlobales = [
                'total_eleves' => 0,
                'moyenne_generale' => number_format(0, 2),
                'note_max' => number_format(0, 2),
                'note_min' => number_format(0, 2),
                'major_nom' => 'Aucun',
                'major_prenom' => 'Aucun',
                'dernier_nom' => 'Aucun',
                'dernier_prenom' => 'Aucun',
                'admis' => 0,
                'refuses' => 0,
                'taux_reussite' => number_format(0, 2),
                'tranches' => $tranchesInitiales,
            ];

            $pdf = Pdf::loadView('pages.admin.pdf.stats-classe', compact('classe', 'trimestre', 'etablissement', 'statsGlobales'))
                ->setPaper('a4', 'portrait');

            return $pdf->download("Statistiques_{$classe->nom}.pdf");
        }

        // 2. Calcul groupé des moyennes trimestrielles de chaque élève via la table 'moyennes'
        $moyennesEleves = DB::table('moyennes')
            ->join('inscriptions', 'moyennes.inscription_id', '=', 'inscriptions.id')
            ->where('inscriptions.classe_id', $classeId)
            ->whereIn('moyennes.sequence_id', $sequences)
            ->select(
                'moyennes.inscription_id',
                DB::raw('ROUND(SUM(total_points) / SUM(coefficient), 2) as moyenne_trimestre')
            )
            ->groupBy('moyennes.inscription_id')
            ->get()
            ->keyBy('inscription_id');

        // Initialisation
        $sommeMoyennes = 0;
        $admis = 0;
        $noteMax = -1;
        $noteMin = 21;
        $majorNom = $majorPrenom = 'Aucun';
        $dernierNom = $dernierPrenom = 'Aucun';
        $tranches = $tranchesInitiales;

        foreach ($inscriptionsEleves as $eleve) {
            $id = $eleve->inscription_id;

            // Moyenne de l'élève pour le trimestre (0 si pas de notes)
            $m = isset($moyennesEleves[$id]) ? (float) $moyennesEleves[$id]->moyenne_trimestre : 0.0;

            $sommeMoyennes += $m;

            if ($m >= 10) {
                $admis++;
            }

            // Chaîne imbriquée propre (1 seule tranche par élève)
            if ($m >= 18) {
                $tranches['excellence']++;
            } elseif ($m >= 16) {
                $tranches['tres_bien']++;
            } elseif ($m >= 14) {
                $tranches['bien']++;
            } elseif ($m >= 12) {
                $tranches['assez_bien']++;
            } elseif ($m >= 10) {
                $tranches['passable']++;
            } else {
                $tranches['echec']++;
            }

            // Major
            if ($m > $noteMax) {
                $noteMax = $m;
                $majorNom = $eleve->nom;
                $majorPrenom = $eleve->prenom;
            }

            // Dernier
            if ($m < $noteMin) {
                $noteMin = $m;
                $dernierNom = $eleve->nom;
                $dernierPrenom = $eleve->prenom;
            }
        }

        $moyenneClasse = $sommeMoyennes / $totalEleves;
        $refuses = $totalEleves - $admis;
        $tauxReussite = ($admis / $totalEleves) * 100;

        $statsGlobales = [
            'total_eleves' => $totalEleves,
            'moyenne_generale' => number_format($moyenneClasse, 2),
            'note_max' => number_format(max(0, $noteMax), 2),
            'note_min' => number_format($noteMin == 21 ? 0 : $noteMin, 2),
            'major_nom' => $majorNom,
            'major_prenom' => $majorPrenom,
            'dernier_nom' => $dernierNom,
            'dernier_prenom' => $dernierPrenom,
            'admis' => $admis,
            'refuses' => $refuses,
            'taux_reussite' => number_format($tauxReussite, 2),
            'tranches' => $tranches,
        ];

        $pdf = Pdf::loadView('pages.admin.pdf.stats-classe', compact('classe', 'trimestre', 'etablissement', 'statsGlobales'))
            ->setPaper('a4', 'portrait');

        return $pdf->download("Statistiques_{$classe->nom}.pdf");
    }

    /**
     * Impression de l'état de contrôle des notes d'une classe
     *
     * Objectif :
     * Permettre aux enseignants et à l'administration de vérifier
     * toutes les notes saisies avant l'impression des bulletins.
     */
    public function imprimerEtatControleNotes($classeId, $trimestreId)
    {
        // ---------------------------------------------------------
        // 1. Informations générales
        // ---------------------------------------------------------

        $etablissement = DB::table('etablissements')->first();

        $trimestre = DB::table('trimestres')
            ->where('id', $trimestreId)
            ->first();

        $classe = DB::table('classes')
            ->where('id', $classeId)
            ->first();

        if (! $trimestre || ! $classe) {
            abort(404, 'Classe ou trimestre introuvable.');
        }

        $anneeActive = DB::table('annee_scolaires')
            ->where('est_active', 1)
            ->first();

        if (! $anneeActive) {
            abort(500, 'Aucune année scolaire active configurée.');
        }

        // ---------------------------------------------------------
        // 2. Récupération des séquences du trimestre
        // ---------------------------------------------------------

        $sequences = DB::table('sequences')
            ->where('trimestre_id', $trimestreId)
            ->orderBy('id', 'asc')
            ->get();

        if ($sequences->isEmpty()) {
            abort(404, 'Aucune séquence configurée pour ce trimestre.');
        }

        $sequenceIds = $sequences->pluck('id');

        // ---------------------------------------------------------
        // 3. Récupération des élèves de la classe
        // ---------------------------------------------------------

        $eleves = DB::table('inscriptions')
            ->join('eleves', 'inscriptions.eleve_id', '=', 'eleves.id')
            ->where('inscriptions.classe_id', $classeId)
            ->where('inscriptions.annee_scolaire_id', $anneeActive->id)
            ->select(
                'inscriptions.id as inscription_id',
                'eleves.nom',
                'eleves.prenom',
                'eleves.matricule',
                'eleves.sexe'
            )
            ->orderBy('eleves.nom', 'asc')
            ->orderBy('eleves.prenom', 'asc')
            ->get();

        // ---------------------------------------------------------
        // 4. Matières de la classe + enseignants
        // ---------------------------------------------------------

        $matieres = DB::table('classe_matiere')
            ->join(
                'matieres',
                'classe_matiere.matiere_id',
                '=',
                'matieres.id'
            )
            ->leftJoin(
                'groupes_matieres',
                'matieres.groupe_matiere_id',
                '=',
                'groupes_matieres.id'
            )
            ->leftJoin('affectations', function ($join) use ($classeId, $anneeActive) {
                $join->on(
                    'affectations.matiere_id',
                    '=',
                    'classe_matiere.matiere_id'
                )
                    ->where(
                        'affectations.classe_id',
                        '=',
                        $classeId
                    )
                    ->where(
                        'affectations.annee_scolaire_id',
                        '=',
                        $anneeActive->id
                    );
            })
            ->leftJoin(
                'enseignants',
                'affectations.enseignant_id',
                '=',
                'enseignants.id'
            )
            ->leftJoin(
                'users',
                'enseignants.user_id',
                '=',
                'users.id'
            )
            ->where(
                'classe_matiere.classe_id',
                $classeId
            )
            ->select(
                'matieres.id as matiere_id',
                'matieres.nom as matiere_nom',
                'classe_matiere.coefficient',
                'groupes_matieres.id as groupe_id',
                'groupes_matieres.nom as groupe_nom',
                'groupes_matieres.ordre as groupe_ordre',
                DB::raw(
                    "GROUP_CONCAT(
                    DISTINCT users.name
                    SEPARATOR ' / '
                ) as enseignant_nom"
                )
            )
            ->groupBy(
                'matieres.id',
                'matieres.nom',
                'classe_matiere.coefficient',
                'groupes_matieres.id',
                'groupes_matieres.nom',
                'groupes_matieres.ordre'
            )
            ->orderBy('groupes_matieres.ordre', 'asc')
            ->orderBy('matieres.nom', 'asc')
            ->get();

        // ---------------------------------------------------------
        // 5. Récupération de toutes les notes en une seule requête
        // ---------------------------------------------------------

        /*
         * Structure finale :
         *
         * $notes[inscription_id][matiere_id][sequence_id] = valeur
         */
        $moyennes = DB::table('moyennes')
            ->whereIn('inscription_id', $eleves->pluck('inscription_id'))
            ->whereIn('sequence_id', $sequenceIds)
            ->select(
                'inscription_id',
                'matiere_id',
                'sequence_id',
                'valeur'
            )
            ->get();
        $notes = [];

        foreach ($moyennes as $moyenne) {
            $notes[
                $moyenne->inscription_id
            ][
                $moyenne->matiere_id
            ][
                $moyenne->sequence_id
            ] = $moyenne->valeur;
        }

        // ---------------------------------------------------------
        // 6. Génération du PDF
        // ---------------------------------------------------------

        $pdf = Pdf::loadView(
            'pages.admin.pdf.etat-controle-notes',
            compact(
                'etablissement',
                'anneeActive',
                'classe',
                'trimestre',
                'sequences',
                'eleves',
                'matieres',
                'notes'
            )
        )->setPaper('a3', 'landscape');

        return $pdf->download(
            "Etat_Controle_Notes_{$classe->nom}_{$trimestre->nom}.pdf"
        );
    }
}
