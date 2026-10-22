<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classe;
use App\Models\Inscription;
use App\Models\School;
use App\Models\Trimestre;
use App\Models\Year;
use App\Services\ScolariteService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BulletinPrintController extends Controller
{
    // 1. Affichage de la Grille des Classes
    public function index(Request $request)
    {
        $actifYear = DB::table('annee_scolaires')->where('est_active', 1)->first();

        if (! $actifYear) {
            abort(500, 'Aucune année scolaire active configurée.');
        }

        $trimesters = DB::table('trimestres')
            ->where('annee_scolaire_id', $actifYear->id)
            ->get();

        $trimesterId = $request->get('trimestre_id') ?? ($trimesters->first()->id ?? null);

        $classes = DB::table('classes')
            ->leftJoin('inscriptions', function ($join) use ($actifYear) {
                $join->on('inscriptions.classe_id', '=', 'classes.id')
                    ->where('inscriptions.annee_scolaire_id', '=', $actifYear->id);
            })
            ->select(
                'classes.id',
                'classes.nom as classe_nom',
                DB::raw('COUNT(inscriptions.id) as total_eleves')
            )
            ->groupBy('classes.id', 'classes.nom')
            ->orderBy('classes.nom', 'asc')
            ->get();

        return view('pages.admin.reports.index', compact('classes', 'trimesters', 'trimesterId'));
    }

    // 2. Affichage du Hub d'une classe (La liste des élèves)
    public function classeHub($classeId, Request $request, ScolariteService $scolariteService)
    {
        $trimesterId = $request->get('trimestre_id');
        $actifYear = DB::table('annee_scolaires')->where('est_active', 1)->first();
        // VÉRIFICATION DE LA CLÔTURE DES SÉQUENCES DU TRIMESTRE
        if ($trimesterId) {
            $trimester = Trimestre::findOrFail($trimesterId);
            // 🔒 SÉCURITÉ : On vérifie que le trimestre appartient bien à l'année scolaire active
            $scolariteService->validateTrimester($trimester);
            $unClosedSequences = DB::table('sequences')
                ->where('trimestre_id', $trimesterId)
                ->where('is_closed', 0)
                ->exists();

            if ($unClosedSequences) {
                return redirect()
                    ->back()
                    ->with('error', 'Les évaluations doivent être closes pour accéder à cette page et imprimer ');
            }
        }

        $classe = DB::table('classes')
            ->where('classes.id', $classeId)
            ->select('classes.id', 'classes.nom')
            ->first();

        $students = DB::table('inscriptions')
            ->join('eleves', 'inscriptions.eleve_id', '=', 'eleves.id')
            ->where('inscriptions.classe_id', $classeId)
            ->where('inscriptions.annee_scolaire_id', $actifYear->id)
            ->select('inscriptions.id as inscription_id', 'eleves.nom', 'eleves.prenom', 'eleves.matricule', 'eleves.sexe', 'eleves.date_naissance', 'eleves.lieu_naissance')
            ->orderBy('eleves.nom', 'asc')
            // ->get();
            ->paginate(15)
            ->withQueryString();

        return view('pages.admin.reports.classe-hub', compact('classe', 'students', 'trimesterId'));
    }

    // 3. Impression d'un SEUL élève (Lecture directe depuis `moyennes`)
    public function printStudent($enrollmentId, $trimesterId)
    {
        $school = DB::table('etablissements')->first();
        $trimester = DB::table('trimestres')->where('id', $trimesterId)->first();
        $sequences = DB::table('sequences')->where('trimestre_id', $trimesterId)->orderBy('id', 'asc')->take(2)->get();

        // Charger les données de l'élève
        $reportCard = $this->loadReportFromAvg($enrollmentId, $trimesterId, $sequences);

        // Récupération des statistiques globales pré-calculées de la classe pour ce trimestre
        $stats = $this->getStatisticsClass($reportCard['inscription']->classe_id, $trimesterId);

        $reportCards = [$reportCard];

        $pdf = Pdf::loadView('pages.admin.pdf.bulletin-single', compact('reportCards', 'trimester', 'sequences', 'school', 'stats'))
            ->setPaper('a4', 'portrait');

        return $pdf->download(str("Bulletin {$reportCard['inscription']->eleve_nom}")->slug('_').'.pdf');
    }

    // 4. Impression de TOUTE la classe d'un coup (Optimisée 100% SQL)

    // 4. Impression de TOUTE la classe (Version ultra-rapide en ~5-7 requêtes SQL au total)
    public function printClasse($classeId, $trimesterId)
    {
        $school = DB::table('etablissements')->first();
        $trimester = DB::table('trimestres')->where('id', $trimesterId)->first();
        $sequences = DB::table('sequences')->where('trimestre_id', $trimesterId)->orderBy('id', 'asc')->take(2)->get();
        $sequenceIds = $sequences->pluck('id');
        $actifYear = DB::table('annee_scolaires')->where('est_active', 1)->first();

        // 1. Charger tous les élèves de la classe en UNE SEULE requête
        $enrollments = DB::table('inscriptions')
            ->join('eleves', 'inscriptions.eleve_id', '=', 'eleves.id')
            ->join('classes', 'inscriptions.classe_id', '=', 'classes.id')
            ->join('annee_scolaires', 'inscriptions.annee_scolaire_id', '=', 'annee_scolaires.id')
            ->where('inscriptions.classe_id', $classeId)
            ->where('inscriptions.annee_scolaire_id', $actifYear->id)
            ->select(
                'inscriptions.id as inscription_id',
                'inscriptions.classe_id',
                'inscriptions.est_redoublant',
                'inscriptions.annee_scolaire_id',
                'eleves.nom as eleve_nom',
                'eleves.photo as student_picture',
                'eleves.prenom as eleve_prenom',
                'eleves.matricule',
                'eleves.sexe',
                'eleves.date_naissance',
                'eleves.lieu_naissance',
                'classes.nom as classe_nom',
                'classes.section',
                'annee_scolaires.libelle as annee_libelle'
            )->get();

        $totalStudentsInClass = $enrollments->count();
        $enrollmentIds = $enrollments->pluck('inscription_id');

        // 2. Charger les matières de la classe UNE SEULE FOIS pour tout le monde
        $matieres = DB::table('classe_matiere')
            ->join('matieres', 'classe_matiere.matiere_id', '=', 'matieres.id')
            ->leftJoin('groupes_matieres', 'matieres.groupe_matiere_id', '=', 'groupes_matieres.id')
            ->leftJoin('affectations', function ($join) use ($classeId, $actifYear) {
                $join->on('affectations.matiere_id', '=', 'classe_matiere.matiere_id')
                    ->where('affectations.classe_id', '=', $classeId)
                    ->where('affectations.annee_scolaire_id', '=', $actifYear->id);
            })
            ->leftJoin('enseignants', 'affectations.enseignant_id', '=', 'enseignants.id')
            ->leftJoin('users', 'enseignants.user_id', '=', 'users.id')
            ->where('classe_matiere.classe_id', $classeId)
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
                'matieres.id', 'matieres.nom', 'classe_matiere.coefficient',
                'groupes_matieres.id', 'groupes_matieres.nom', 'groupes_matieres.ordre'
            )
            ->orderBy('groupes_matieres.ordre', 'asc')
            ->get()
            ->groupBy('groupe_id');

        // 3. Charger TOUTES les moyennes de la classe d'un coup
        $allMoyennes = DB::table('moyennes')
            ->whereIn('inscription_id', $enrollmentIds)
            ->whereIn('sequence_id', $sequenceIds)
            ->get()
            ->groupBy('inscription_id');

        // 4. Charger TOUS les suivis disciplinaires d'un coup
        $tousLesSuivis = DB::table('suivi_disciplinaires')
            ->whereIn('inscription_id', $enrollmentIds)
            ->where('trimestre_id', $trimesterId)
            ->get()
            ->keyBy('inscription_id');

        // 5. Calculer TOUTES les moyennes générales et rangs de la classe en 1 seule requête SQL
        $bilanClasse = DB::table('moyennes')
            ->whereIn('inscription_id', $enrollmentIds)
            ->whereIn('sequence_id', $sequenceIds)
            ->select(
                'inscription_id',
                DB::raw('ROUND(SUM(total_points) / SUM(coefficient), 2) as moyenne_trimestre')
            )
            ->groupBy('inscription_id')
            ->orderByDesc('moyenne_trimestre')
            ->get();

        // Générer les rangs en mémoire PHP
        $rangsEtMoyennes = [];
        $rangActuel = 1;
        foreach ($bilanClasse as $b) {
            $rangsEtMoyennes[$b->inscription_id] = [
                'moyenne' => $b->moyenne_trimestre,
                'rang' => $rangActuel++,
            ];
        }

        $professeurPrincipal = $this->getProfesseurPrincipal(
            $classeId,
            $actifYear->id
        );

        // 6. Assemblage en mémoire
        $reportCards = [];
        foreach ($enrollments as $ins) {
            $id = $ins->inscription_id;
            $moyennesEleve = $allMoyennes->get($id, collect());

            $notes = [];
            $coefficients = [];
            foreach ($moyennesEleve as $m) {
                $notes[$m->matiere_id][$m->sequence_id] = $m->valeur;
                $coefficients[$m->matiere_id] = $m->coefficient;
            }

            $reportCards[] = [
                'inscription' => $ins,
                'totalStudentsInClass' => $totalStudentsInClass,
                'matieres' => $matieres,
                'notes' => $notes,
                'coefficients' => $coefficients,
                'suivi' => $tousLesSuivis->get($id),
                'moyenneEleve' => $rangsEtMoyennes[$id]['moyenne'] ?? 0,
                'rang' => $rangsEtMoyennes[$id]['rang'] ?? 'N/A',
                'professeurPrincipal' => $professeurPrincipal,
            ];
        }

        $stats = $this->getStatisticsClass($classeId, $trimesterId);

        $pdf = Pdf::loadView('pages.admin.pdf.bulletin-single', compact('reportCards', 'trimester', 'sequences', 'school', 'stats'))
            ->setPaper('a4', 'portrait');

        return $pdf->download(str('Bulletins_Classe')->slug('_').'.pdf');
    }

    /**
     * Reconstitution instantanée du bulletin depuis la table 'moyennes'
     */
    private function loadReportFromAvg($enrollmentId, $trimesterId, $sequences)
    {
        $enrollment = DB::table('inscriptions')
            ->join('eleves', 'inscriptions.eleve_id', '=', 'eleves.id')
            ->join('classes', 'inscriptions.classe_id', '=', 'classes.id')
            ->join('annee_scolaires', 'inscriptions.annee_scolaire_id', '=', 'annee_scolaires.id')
            ->where('inscriptions.id', $enrollmentId)
            ->select(
                'inscriptions.id as inscription_id',
                'inscriptions.classe_id',
                'inscriptions.est_redoublant',
                'inscriptions.annee_scolaire_id',
                'eleves.nom as eleve_nom',
                // image de l'eleve
                'eleves.photo as student_picture',
                'eleves.prenom as eleve_prenom',
                'eleves.matricule',
                'eleves.sexe',
                'eleves.date_naissance',
                'eleves.lieu_naissance',
                'classes.nom as classe_nom',
                'classes.section',
                'annee_scolaires.libelle as annee_libelle'
            )->first();

        $totalStudentsInClass = DB::table('inscriptions')
            ->where('classe_id', $enrollment->classe_id)
            ->where('annee_scolaire_id', $enrollment->annee_scolaire_id)
            ->count();

        $suiviDisciplinaire = DB::table('suivi_disciplinaires')
            ->where('inscription_id', $enrollmentId)
            ->where('trimestre_id', $trimesterId)
            ->first();

        // Récupération directe des moyennes pré-calculées pour les séquences du trimestre
        $sequenceIds = $sequences->pluck('id');
        $moyennesBrutes = DB::table('moyennes')
            ->where('inscription_id', $enrollmentId)
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
            ->leftJoin('affectations', function ($join) use ($enrollment) {
                $join->on('affectations.matiere_id', '=', 'classe_matiere.matiere_id')
                    ->where('affectations.classe_id', '=', $enrollment->classe_id)
                    // CORRECTION ICI : Filtrer par l'année scolaire de l'inscription
                    ->where('affectations.annee_scolaire_id', '=', $enrollment->annee_scolaire_id);
            })
            ->leftJoin('enseignants', 'affectations.enseignant_id', '=', 'enseignants.id')
            ->leftJoin('users', 'enseignants.user_id', '=', 'users.id')
            ->where('classe_matiere.classe_id', $enrollment->classe_id)
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
            ->where('inscription_id', $enrollmentId)
            ->whereIn('sequence_id', $sequenceIds)
            ->select(DB::raw('SUM(total_points) / SUM(coefficient) as moyenne_trimestre'))
            ->value('moyenne_trimestre');

        $moyenneEleve = $moyenneEleve ? round($moyenneEleve, 2) : 0;

        // 2. Calcul du Rang de cet élève dans la classe pour le trimestre
        $studentRank = DB::table('moyennes')
            ->join('inscriptions', 'moyennes.inscription_id', '=', 'inscriptions.id')
            ->where('inscriptions.classe_id', $enrollment->classe_id)
            ->where('inscriptions.annee_scolaire_id', $enrollment->annee_scolaire_id) // FIX 1 : Filtrer par l'année active
            ->whereIn('moyennes.sequence_id', $sequenceIds)
            ->select('moyennes.inscription_id', DB::raw('ROUND(SUM(total_points) / SUM(coefficient), 2) as moy_trim'))
            ->groupBy('moyennes.inscription_id')
            ->havingRaw('ROUND(SUM(total_points) / SUM(coefficient), 2) > ?', [$moyenneEleve]) // FIX 2 : Comparer les moyennes arrondies
            ->get()
            ->count() + 1;

        $professeurPrincipal = $this->getProfesseurPrincipal(
            $enrollment->classe_id,
            $enrollment->annee_scolaire_id
        );

        // dd($professeurPrincipal);
        return [
            'inscription' => $enrollment,
            'totalStudentsInClass' => $totalStudentsInClass,
            'matieres' => $matieres,
            'notes' => $notes,
            'coefficients' => $coefficients,
            'suivi' => $suiviDisciplinaire,
            'moyenneEleve' => $moyenneEleve,
            'rang' => $studentRank,
            'professeurPrincipal' => $professeurPrincipal,
        ];
    }

    private function getProfesseurPrincipal($classeId, $anneeScolaireId)
    {
        return DB::table('classe_enseignant_principal')
            ->join(
                'enseignants',
                'classe_enseignant_principal.enseignant_id',
                '=',
                'enseignants.id'
            )
            ->join(
                'users',
                'enseignants.user_id',
                '=',
                'users.id'
            )
            ->where('classe_enseignant_principal.classe_id', $classeId)
            ->where('classe_enseignant_principal.annee_scolaire_id', $anneeScolaireId)
            ->select(
                'enseignants.id',
                'users.name'
            )
            ->first();
    }

    /**
     * Calcul léger des statistiques globales de la classe
     */
    private function getStatisticsClass($classeId, $trimesterId)
    {
        $sequenceIds = DB::table('sequences')
            ->where('trimestre_id', $trimesterId)
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

    public function imprimerTableauHonneur($classeId, $trimesterId)
    {
        $school = DB::table('etablissements')->first();
        $trimester = DB::table('trimestres')->where('id', $trimesterId)->first();
        $classe = DB::table('classes')->where('id', $classeId)->first();
        $actifYear = DB::table('annee_scolaires')->where('est_active', 1)->first();

        // Récupération directe depuis la table 'bilans' (Seuil >= 12)
        $results = DB::table('bilans')
            ->join('inscriptions', 'bilans.inscription_id', '=', 'inscriptions.id')
            ->join('eleves', 'inscriptions.eleve_id', '=', 'eleves.id')
            ->where('inscriptions.classe_id', $classeId)
            ->where('inscriptions.annee_scolaire_id', $actifYear->id)
            ->where('bilans.trimestre_id', $trimesterId)
            ->whereNull('bilans.sequence_id')
            ->where('bilans.moyenne', '>=', 12)
            ->select('eleves.nom', 'eleves.prenom', 'eleves.sexe', 'bilans.moyenne')
            ->orderBy('bilans.moyenne', 'desc')
            ->get();

        $pdf = Pdf::loadView('pages.admin.pdf.tableau-honneur', compact('classe', 'trimester', 'school', 'results', 'actifYear'))
            ->setPaper('a4', 'landscape');

        return $pdf->download(str("Tableau_Honneur_{$classe->nom}")->slug('_').'.pdf');
    }

    public function imprimerStatsClasse($classeId, $trimesterId)
    {
        $school = DB::table('etablissements')->first();
        $trimester = DB::table('trimestres')->where('id', $trimesterId)->first();
        $classe = DB::table('classes')->where('id', $classeId)->first();
        $actifYear = DB::table('annee_scolaires')->where('est_active', 1)->first();

        $sequences = DB::table('sequences')
            ->where('trimestre_id', $trimesterId)
            ->orderBy('id', 'asc')
            ->pluck('id');

        // 1. Récupération des élèves inscrits
        $enrollmentsStudents = DB::table('inscriptions')
            ->join('eleves', 'inscriptions.eleve_id', '=', 'eleves.id')
            ->where('inscriptions.classe_id', $classeId)
            ->where('inscriptions.annee_scolaire_id', $actifYear->id)
            ->select('inscriptions.id as inscription_id', 'eleves.nom', 'eleves.prenom')
            ->get();

        $totalEleves = $enrollmentsStudents->count();

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

            $pdf = Pdf::loadView('pages.admin.pdf.stats-classe', compact('classe', 'trimestre', 'school', 'statsGlobales', 'actifYear'))
                ->setPaper('a4', 'portrait');

            return $pdf->download(str("Statistiques_{$classe->nom}")->slug('_').'.pdf');
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
        $majorName = $majorSurname = 'Aucun';
        $lastName = $lastSurname = 'Aucun';
        $tranches = $tranchesInitiales;

        foreach ($enrollmentsStudents as $student) {
            $id = $student->inscription_id;

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
                $majorName = $student->nom;
                $majorSurname = $student->prenom;
            }

            // Dernier
            if ($m < $noteMin) {
                $noteMin = $m;
                $lastName = $student->nom;
                $lastSurname = $student->prenom;
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
            'major_nom' => $majorName,
            'major_prenom' => $majorSurname,
            'dernier_nom' => $lastName,
            'dernier_prenom' => $lastSurname,
            'admis' => $admis,
            'refuses' => $refuses,
            'taux_reussite' => number_format($tauxReussite, 2),
            'tranches' => $tranches,
        ];

        $pdf = Pdf::loadView('pages.admin.pdf.stats-classe', compact('classe', 'trimester', 'school', 'statsGlobales'))
            ->setPaper('a4', 'portrait');

        return $pdf->download(str("Statistiques_{$classe->nom}")->slug('_').'.pdf');
    }

    /**
     * Impression de l'état de contrôle des notes d'une classe
     *
     * Objectif :
     * Permettre aux enseignants et à l'administration de vérifier
     * toutes les notes saisies avant l'impression des bulletins.
     */
    public function imprimerEtatControleNotes($classeId, $trimesterId)
    {
        // ---------------------------------------------------------
        // 1. Informations générales
        // ---------------------------------------------------------

        $school = DB::table('etablissements')->first();

        $trimester = DB::table('trimestres')
            ->where('id', $trimesterId)
            ->first();

        $classe = DB::table('classes')
            ->where('id', $classeId)
            ->first();

        if (! $trimester || ! $classe) {
            abort(404, 'Classe ou trimestre introuvable.');
        }

        $actifYear = DB::table('annee_scolaires')
            ->where('est_active', 1)
            ->first();

        if (! $actifYear) {
            abort(500, 'Aucune année scolaire active configurée.');
        }

        // ---------------------------------------------------------
        // 2. Récupération des séquences du trimestre
        // ---------------------------------------------------------

        $sequences = DB::table('sequences')
            ->where('trimestre_id', $trimesterId)
            ->orderBy('id', 'asc')
            ->get();

        if ($sequences->isEmpty()) {
            abort(404, 'Aucune séquence configurée pour ce trimestre.');
        }

        $sequenceIds = $sequences->pluck('id');

        // ---------------------------------------------------------
        // 3. Récupération des élèves de la classe
        // ---------------------------------------------------------

        $students = DB::table('inscriptions')
            ->join('eleves', 'inscriptions.eleve_id', '=', 'eleves.id')
            ->where('inscriptions.classe_id', $classeId)
            ->where('inscriptions.annee_scolaire_id', $actifYear->id)
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
            ->leftJoin('affectations', function ($join) use ($classeId, $actifYear) {
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
                        $actifYear->id
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
            ->whereIn('inscription_id', $students->pluck('inscription_id'))
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
                'school',
                'actifYear',
                'classe',
                'trimester',
                'sequences',
                'students',
                'matieres',
                'notes'
            )
        )->setPaper('a3', 'landscape');
        $fileName = Str::slug("EtatControleNotes {$classe->nom} {$trimester->nom}").'.pdf';

        return $pdf->download($fileName);
    }

    /**
     * Impression du Bulletin Annuel pour toute la classe (6 Séquences + Moyennes Trimesterielles/Annuelles)
     */
    // public function imprimerClasseAnnuel($classeId)
    // {
    //     $school = DB::table('etablissements')->first();
    //     $actifYear = DB::table('annee_scolaires')->where('est_active', 1)->first();

    //     if (! $actifYear) {
    //         abort(500, 'Aucune année scolaire active configurée.');
    //     }

    //     // 🔒 Sécurité : Vérification de la clôture des 6 séquences
    //     $unClosedSequences = DB::table('sequences')
    //         ->join('trimestres', 'sequences.trimestre_id', '=', 'trimestres.id')
    //         ->where('trimestres.annee_scolaire_id', $actifYear->id)
    //         ->where('sequences.is_closed', 0)
    //         ->exists();

    //     if ($unClosedSequences) {
    //         return redirect()
    //             ->back()
    //             ->with('error', 'Impossible d\'imprimer les bulletins annuels : toutes les séquences de l\'année doivent être closes.');
    //     }

    //     // 1. Récupération ordonnée des trimestres et des 6 séquences
    //     $trimestres = DB::table('trimestres')
    //         ->where('annee_scolaire_id', $actifYear->id)
    //         ->orderBy('id', 'asc')
    //         ->get();

    //     $sequencesParTrimestre = [];
    //     $allSequenceIds = [];

    //     foreach ($trimestres as $trimestre) {
    //         $seqs = DB::table('sequences')
    //             ->where('trimestre_id', $trimestre->id)
    //             ->orderBy('id', 'asc')
    //             ->get();

    //         $sequencesParTrimestre[$trimestre->id] = $seqs;
    //         foreach ($seqs as $s) {
    //             $allSequenceIds[] = $s->id;
    //         }
    //     }

    //     $classe = DB::table('classes')->where('id', $classeId)->first();
    //     if (! $classe) {
    //         abort(404, 'Classe introuvable.');
    //     }

    //     // 2. Récupération des élèves inscrits
    //     $enrollments = DB::table('inscriptions')
    //         ->join('eleves', 'inscriptions.eleve_id', '=', 'eleves.id')
    //         ->join('classes', 'inscriptions.classe_id', '=', 'classes.id')
    //         ->join('annee_scolaires', 'inscriptions.annee_scolaire_id', '=', 'annee_scolaires.id')
    //         ->where('inscriptions.classe_id', $classeId)
    //         ->where('inscriptions.annee_scolaire_id', $actifYear->id)
    //         ->select(
    //             'inscriptions.id as inscription_id',
    //             'inscriptions.classe_id',
    //             'inscriptions.est_redoublant',
    //             'inscriptions.annee_scolaire_id',
    //             'eleves.nom as eleve_nom',
    //             'eleves.photo as student_picture',
    //             'eleves.prenom as eleve_prenom',
    //             'eleves.matricule',
    //             'eleves.sexe',
    //             'eleves.date_naissance',
    //             'eleves.lieu_naissance',
    //             'classes.nom as classe_nom',
    //             'classes.section',
    //             'annee_scolaires.libelle as annee_libelle'
    //         )->get();
    //     // Appel de la fonction pour récupérer la discipline
    //     // $disciplineStats = $this->getAnnualDisciplineStats($id);
    //     // $totalStudentsInClass = $enrollments->count();
    //     // $enrollmentIds = $enrollments->pluck('inscription_id');

    //     $totalStudentsInClass = $enrollments->count();

    //     $enrollmentIds = $enrollments->pluck('inscription_id');

    //     $disciplineStats = DB::table('suivi_disciplinaires')
    //         ->whereIn('inscription_id', $enrollmentIds)
    //         ->select(
    //             'inscription_id',
    //             DB::raw('SUM(absences) as total_absences'),
    //             DB::raw('SUM(retards) as total_retards'),
    //             DB::raw('SUM(avertissements) as total_avertissements'),
    //             DB::raw('SUM(blames) as total_blames'),
    //             DB::raw('SUM(suspensions) as total_suspensions'),
    //             DB::raw('SUM(exclusions) as total_exclusions')
    //         )
    //         ->groupBy('inscription_id')
    //         ->get()
    //         ->keyBy('inscription_id');

    //     // 3. Charger les matières
    //     $matieres = DB::table('classe_matiere')
    //         ->join('matieres', 'classe_matiere.matiere_id', '=', 'matieres.id')
    //         ->leftJoin('groupes_matieres', 'matieres.groupe_matiere_id', '=', 'groupes_matieres.id')
    //         ->leftJoin('affectations', function ($join) use ($classeId, $actifYear) {
    //             $join->on('affectations.matiere_id', '=', 'classe_matiere.matiere_id')
    //                 ->where('affectations.classe_id', '=', $classeId)
    //                 ->where('affectations.annee_scolaire_id', '=', $actifYear->id);
    //         })
    //         ->leftJoin('enseignants', 'affectations.enseignant_id', '=', 'enseignants.id')
    //         ->leftJoin('users', 'enseignants.user_id', '=', 'users.id')
    //         ->where('classe_matiere.classe_id', $classeId)
    //         ->select(
    //             'matieres.id as matiere_id',
    //             'matieres.nom as matiere_nom',
    //             'classe_matiere.coefficient',
    //             'groupes_matieres.id as groupe_id',
    //             'groupes_matieres.nom as groupe_nom',
    //             'groupes_matieres.ordre as groupe_ordre',
    //             DB::raw("GROUP_CONCAT(DISTINCT users.name SEPARATOR ' / ') as prof_nom")
    //         )
    //         ->groupBy(
    //             'matieres.id', 'matieres.nom', 'classe_matiere.coefficient',
    //             'groupes_matieres.id', 'groupes_matieres.nom', 'groupes_matieres.ordre'
    //         )
    //         ->orderBy('groupes_matieres.ordre', 'asc')
    //         ->get()
    //         ->groupBy('groupe_id');

    //     // 4. Charger TOUTES les moyennes (séquentielles et trimestrielles)
    //     $allMoyennes = DB::table('moyennes')
    //         ->whereIn('inscription_id', $enrollmentIds)
    //         ->get()
    //         ->groupBy('inscription_id');

    //     // 5. Calcul de la moyenne annuelle et des rangs annuels
    //     $bilanAnnuelClasse = DB::table('moyennes')
    //         ->whereIn('inscription_id', $enrollmentIds)
    //         ->whereIn('sequence_id', $allSequenceIds)
    //         ->select(
    //             'inscription_id',
    //             DB::raw('ROUND(SUM(total_points) / NULLIF(SUM(coefficient), 0), 2) as moyenne_annuelle')
    //         )
    //         ->groupBy('inscription_id')
    //         ->orderByDesc('moyenne_annuelle')
    //         ->get();

    //     $rangsAnnuels = [];
    //     $rangActuel = 1;
    //     foreach ($bilanAnnuelClasse as $b) {
    //         $rangsAnnuels[$b->inscription_id] = [
    //             'moyenne' => $b->moyenne_annuelle,
    //             'rang' => $rangActuel++,
    //         ];
    //     }

    //     $professeurPrincipal = $this->getProfesseurPrincipal($classeId, $actifYear->id);

    //     // 6. Assemblage des livrets d'élèves
    //     $reportCards = [];
    //     foreach ($enrollments as $ins) {
    //         $id = $ins->inscription_id;
    //         $disciplineStats = $this->getAnnualDisciplineStats($id);
    //         $moyennesEleve = $allMoyennes->get($id, collect());
    //         $notesSequences = [];
    //         $notesTrimestresGlobal = [];
    //         $coefficients = [];

    //         foreach ($moyennesEleve as $m) {
    //             if ($m->sequence_id) {
    //                 $notesSequences[$m->matiere_id][$m->sequence_id] = $m->valeur;
    //             }
    //             if ($m->trimestre_id && $m->matiere_id) {
    //                 // Moyenne de la matière pour ce trimestre
    //             }
    //             $coefficients[$m->matiere_id] = $m->coefficient;
    //         }

    //         // Calcul explicite des moyennes trimestrielles globales de l'élève (bas du bulletin)
    //         foreach ($trimestres as $t) {
    //             $seqsT = $sequencesParTrimestre[$t->id]->pluck('id');
    //             $moyT = DB::table('moyennes')
    //                 ->where('inscription_id', $id)
    //                 ->whereIn('sequence_id', $seqsT)
    //                 ->select(DB::raw('SUM(total_points) / NULLIF(SUM(coefficient), 0) as moy'))
    //                 ->value('moy');

    //             $notesTrimestresGlobal[$t->id] = $moyT ? round($moyT, 2) : null;
    //         }

    //         $reportCards[] = [
    //             'inscription' => $ins,
    //             'totalStudentsInClass' => $totalStudentsInClass,
    //             'matieres' => $matieres,
    //             'notesSequences' => $notesSequences,
    //             'notesTrimestres' => $notesTrimestresGlobal,
    //             // 'notesTrimestresGlobal' => $notesTrimestresGlobal,
    //             'coefficients' => $coefficients,
    //             'moyenneEleve' => $rangsAnnuels[$id]['moyenne'] ?? 0,
    //             'rang' => $rangsAnnuels[$id]['rang'] ?? 'N/A',
    //             'professeurPrincipal' => $professeurPrincipal,

    //             'disciplineStats' => $disciplineStats,
    //         ];
    //     }

    //     $pdf = Pdf::loadView('pages.admin.pdf.bulletin-annuel', compact('reportCards', 'trimestres', 'sequencesParTrimestre', 'school', 'actifYear', 'disciplineStats'))
    //         ->setPaper('a4', 'landscape');

    //     return $pdf->download(str("Bulletin_Annuel_{$classe->nom}")->slug('_').'.pdf');
    // }



public function imprimerClasseAnnuel($classeId)
{
    $school = DB::table('etablissements')->first();

    $actifYear = DB::table('annee_scolaires')
        ->where('est_active', 1)
        ->first();

    if (! $actifYear) {
        abort(500, 'Aucune année scolaire active configurée.');
    }

    // 🔒 Vérification de la clôture des séquences
    $unClosedSequences = DB::table('sequences')
        ->join(
            'trimestres',
            'sequences.trimestre_id',
            '=',
            'trimestres.id'
        )
        ->where('trimestres.annee_scolaire_id', $actifYear->id)
        ->where('sequences.is_closed', 0)
        ->exists();

    if ($unClosedSequences) {
        return redirect()
            ->back()
            ->with(
                'error',
                'Impossible d\'imprimer les bulletins annuels : toutes les séquences de l\'année doivent être closes.'
            );
    }

    // =========================================================
    // 1. Récupération des trimestres et séquences
    // =========================================================

    $trimestres = DB::table('trimestres')
        ->where('annee_scolaire_id', $actifYear->id)
        ->orderBy('id', 'asc')
        ->get();

    $sequencesParTrimestre = [];
    $allSequenceIds = [];

    foreach ($trimestres as $trimestre) {

        $seqs = DB::table('sequences')
            ->where('trimestre_id', $trimestre->id)
            ->orderBy('id', 'asc')
            ->get();

        $sequencesParTrimestre[$trimestre->id] = $seqs;

        foreach ($seqs as $sequence) {
            $allSequenceIds[] = $sequence->id;
        }
    }

    // =========================================================
    // 2. Vérification de la classe
    // =========================================================

    $classe = DB::table('classes')
        ->where('id', $classeId)
        ->first();

    if (! $classe) {
        abort(404, 'Classe introuvable.');
    }

    // =========================================================
    // 3. Récupération des élèves inscrits
    // =========================================================

    $enrollments = DB::table('inscriptions')
        ->join(
            'eleves',
            'inscriptions.eleve_id',
            '=',
            'eleves.id'
        )
        ->join(
            'classes',
            'inscriptions.classe_id',
            '=',
            'classes.id'
        )
        ->join(
            'annee_scolaires',
            'inscriptions.annee_scolaire_id',
            '=',
            'annee_scolaires.id'
        )
        ->where('inscriptions.classe_id', $classeId)
        ->where('inscriptions.annee_scolaire_id', $actifYear->id)
        ->select(
            'inscriptions.id as inscription_id',
            'inscriptions.classe_id',
            'inscriptions.est_redoublant',
            'inscriptions.annee_scolaire_id',
            'eleves.nom as eleve_nom',
            'eleves.photo as student_picture',
            'eleves.prenom as eleve_prenom',
            'eleves.matricule',
            'eleves.sexe',
            'eleves.date_naissance',
            'eleves.lieu_naissance',
            'classes.nom as classe_nom',
            'classes.section',
            'annee_scolaires.libelle as annee_libelle'
        )
        ->get();

    $totalStudentsInClass = $enrollments->count();

    $enrollmentIds = $enrollments->pluck('inscription_id');

    // =========================================================
    // 4. Discipline de TOUS les élèves
    //    Une seule requête SQL pour toute la classe
    // =========================================================

    $disciplineStats = DB::table('suivi_disciplinaires')
        ->whereIn('inscription_id', $enrollmentIds)
        ->select(
            'inscription_id',
            DB::raw('COALESCE(SUM(absences), 0) as total_absences'),
            DB::raw('COALESCE(SUM(retards), 0) as total_retards'),
            DB::raw('COALESCE(SUM(avertissements), 0) as total_avertissements'),
            DB::raw('COALESCE(SUM(blames), 0) as total_blames'),
            DB::raw('COALESCE(SUM(suspensions), 0) as total_suspensions'),
            DB::raw('COALESCE(SUM(exclusions), 0) as total_exclusions')
        )
        ->groupBy('inscription_id')
        ->get()
        ->keyBy('inscription_id');

    // =========================================================
    // 5. Charger les matières
    // =========================================================

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
        ->leftJoin('affectations', function ($join) use (
            $classeId,
            $actifYear
        ) {
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
                    $actifYear->id
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
                "GROUP_CONCAT(DISTINCT users.name SEPARATOR ' / ') as prof_nom"
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
        ->get()
        ->groupBy('groupe_id');

    // =========================================================
    // 6. Charger toutes les moyennes
    // =========================================================

    $allMoyennes = DB::table('moyennes')
        ->whereIn('inscription_id', $enrollmentIds)
        ->get()
        ->groupBy('inscription_id');

    // =========================================================
    // 7. Moyennes trimestrielles de TOUS les élèves
    //    Une seule requête au lieu d'une requête par élève/trimestre
    // =========================================================

    $moyennesTrimestrielles = DB::table('moyennes')
        ->whereIn('inscription_id', $enrollmentIds)
        ->whereIn('sequence_id', $allSequenceIds)
        ->select(
            'inscription_id',
            'trimestre_id',
            DB::raw(
                'SUM(total_points) / NULLIF(SUM(coefficient), 0) as moyenne'
            )
        )
        ->groupBy(
            'inscription_id',
            'trimestre_id'
        )
        ->get()
        ->groupBy('inscription_id');

    // =========================================================
    // 8. Calcul des moyennes annuelles et des rangs
    // =========================================================

    $bilanAnnuelClasse = DB::table('moyennes')
        ->whereIn('inscription_id', $enrollmentIds)
        ->whereIn('sequence_id', $allSequenceIds)
        ->select(
            'inscription_id',
            DB::raw(
                'ROUND(
                    SUM(total_points) /
                    NULLIF(SUM(coefficient), 0),
                    2
                ) as moyenne_annuelle'
            )
        )
        ->groupBy('inscription_id')
        ->orderByDesc('moyenne_annuelle')
        ->get();

    $rangsAnnuels = [];

    $rangActuel = 1;

    foreach ($bilanAnnuelClasse as $bilan) {

        $rangsAnnuels[$bilan->inscription_id] = [
            'moyenne' => $bilan->moyenne_annuelle,
            'rang' => $rangActuel++,
        ];
    }

    // =========================================================
    // 9. Professeur principal
    // =========================================================

    $professeurPrincipal = $this->getProfesseurPrincipal(
        $classeId,
        $actifYear->id
    );

    // =========================================================
    // 10. Assemblage des bulletins
    // =========================================================

    $reportCards = [];

    foreach ($enrollments as $ins) {

        $id = $ins->inscription_id;

        // ---------------------------------------------
        // Discipline de cet élève
        // ---------------------------------------------

        $studentDiscipline = $disciplineStats->get($id);

        // Si aucune donnée disciplinaire n'existe
        // pour cet élève, on crée des valeurs à 0.
        if (! $studentDiscipline) {
            $studentDiscipline = (object) [
                'total_absences' => 0,
                'total_retards' => 0,
                'total_avertissements' => 0,
                'total_blames' => 0,
                'total_suspensions' => 0,
                'total_exclusions' => 0,
            ];
        }

        // ---------------------------------------------
        // Moyennes de l'élève
        // ---------------------------------------------

        $moyennesEleve = $allMoyennes->get(
            $id,
            collect()
        );

        $notesSequences = [];
        $coefficients = [];

        foreach ($moyennesEleve as $moyenne) {

            if ($moyenne->sequence_id) {

                $notesSequences[
                    $moyenne->matiere_id
                ][$moyenne->sequence_id] = $moyenne->valeur;
            }

            if (
                $moyenne->trimestre_id &&
                $moyenne->matiere_id
            ) {
                // Moyenne de la matière pour ce trimestre
            }

            $coefficients[
                $moyenne->matiere_id
            ] = $moyenne->coefficient;
        }

        // ---------------------------------------------
        // Moyennes trimestrielles
        // ---------------------------------------------

        $notesTrimestresGlobal = [];

        $moyennesEleveTrimestres =
            $moyennesTrimestrielles->get(
                $id,
                collect()
            );

        foreach ($trimestres as $trimestre) {

            $moyenneTrimestre =
                $moyennesEleveTrimestres->firstWhere(
                    'trimestre_id',
                    $trimestre->id
                );

            $notesTrimestresGlobal[$trimestre->id] =
                $moyenneTrimestre
                    ? round($moyenneTrimestre->moyenne, 2)
                    : null;
        }

        // ---------------------------------------------
        // Bulletin de l'élève
        // ---------------------------------------------

        $reportCards[] = [
            'inscription' => $ins,

            'totalStudentsInClass' =>
                $totalStudentsInClass,

            'matieres' =>
                $matieres,

            'notesSequences' =>
                $notesSequences,

            'notesTrimestres' =>
                $notesTrimestresGlobal,

            'coefficients' =>
                $coefficients,

            'moyenneEleve' =>
                $rangsAnnuels[$id]['moyenne'] ?? 0,

            'rang' =>
                $rangsAnnuels[$id]['rang'] ?? 'N/A',

            'professeurPrincipal' =>
                $professeurPrincipal,

            'disciplineStats' =>
                $studentDiscipline,
        ];
    }

    // =========================================================
    // 11. Génération du PDF
    // =========================================================

    $pdf = Pdf::loadView(
        'pages.admin.pdf.bulletin-annuel',
        compact(
            'reportCards',
            'trimestres',
            'sequencesParTrimestre',
            'school',
            'actifYear'
        )
    )->setPaper('a4', 'landscape');

    return $pdf->download(
        str("Bulletin_Annuel_{$classe->nom}")
            ->slug('_') . '.pdf'
    );
}


public function imprimerRecapitulatifConseil($classeId)
{
    $school = DB::table('etablissements')->first();
    $actifYear = DB::table('annee_scolaires')->where('est_active', 1)->first();

    if (! $actifYear) {
        abort(500, 'Aucune année scolaire active configurée.');
    }

    $classe = DB::table('classes')->where('id', $classeId)->first();
    if (! $classe) {
        abort(404, 'Classe introuvable.');
    }

    // 1. Récupérer tous les trimestres et toutes les séquences de l'année active
    $trimestres = DB::table('trimestres')
        ->where('annee_scolaire_id', $actifYear->id)
        ->orderBy('id', 'asc')
        ->get();

    $allSequenceIds = DB::table('sequences')
        ->join('trimestres', 'sequences.trimestre_id', '=', 'trimestres.id')
        ->where('trimestres.annee_scolaire_id', $actifYear->id)
        ->pluck('sequences.id');

    // 2. Récupérer tous les élèves inscrits dans la classe
    $enrollments = DB::table('inscriptions')
        ->join('eleves', 'inscriptions.eleve_id', '=', 'eleves.id')
        ->where('inscriptions.classe_id', $classeId)
        ->where('inscriptions.annee_scolaire_id', $actifYear->id)
        ->select(
            'inscriptions.id as inscription_id',
            'inscriptions.est_redoublant',
            'eleves.nom',
            'eleves.prenom',
            'eleves.matricule',
            'eleves.date_naissance'
        )
        ->get();

    $enrollmentIds = $enrollments->pluck('inscription_id');

    // 3. Récupérer les moyennes trimestrielles de toute la classe en UNE SEULE requête
    $moyennesTrimestrielles = DB::table('moyennes')
        ->whereIn('inscription_id', $enrollmentIds)
        ->whereIn('sequence_id', $allSequenceIds)
        ->select(
            'inscription_id',
            'trimestre_id',
            DB::raw('SUM(total_points) / NULLIF(SUM(coefficient), 0) as moyenne')
        )
        ->groupBy('inscription_id', 'trimestre_id')
        ->get()
        ->groupBy('inscription_id');

    // 4. Calculer la moyenne annuelle et le classement de toute la classe en UNE SEULE requête (comme pour le bulletin annuel)
    $bilanAnnuelClasse = DB::table('moyennes')
        ->whereIn('inscription_id', $enrollmentIds)
        ->whereIn('sequence_id', $allSequenceIds)
        ->select(
            'inscription_id',
            DB::raw('ROUND(SUM(total_points) / NULLIF(SUM(coefficient), 0), 2) as moyenne_annuelle')
        )
        ->groupBy('inscription_id')
        ->orderByDesc('moyenne_annuelle')
        ->get();

    $rangsAnnuels = [];
    $rangActuel = 1;
    foreach ($bilanAnnuelClasse as $bilan) {
        $rangsAnnuels[$bilan->inscription_id] = [
            'moyenne' => $bilan->moyenne_annuelle,
            'rang' => $rangActuel++,
        ];
    }

    // 5. Assemblage des données pour la vue
    $recapData = [];
    foreach ($enrollments as $ins) {
        $id = $ins->inscription_id;
        $moyennesEleveTrimestres = $moyennesTrimestrielles->get($id, collect());

        $notesTrims = [];
        foreach ($trimestres as $t) {
            $mTrim = $moyennesEleveTrimestres->firstWhere('trimestre_id', $t->id);
            $notesTrims[$t->id] = $mTrim ? round($mTrim->moyenne, 2) : null;
        }

        $recapData[] = [
            'inscription' => $ins,
            'moyT1' => $notesTrims[$trimestres[0]->id ?? 0] ?? null,
            'moyT2' => $notesTrims[$trimestres[1]->id ?? 0] ?? null,
            'moyT3' => $notesTrims[$trimestres[2]->id ?? 0] ?? null,
            'moyAnnuelle' => $rangsAnnuels[$id]['moyenne'] ?? 0,
            'rang' => $rangsAnnuels[$id]['rang'] ?? 'N/A',
        ];
    }

    // Tri par ordre de mérite décroissant sur la moyenne annuelle
    usort($recapData, function ($a, $b) {
        return $b['moyAnnuelle'] <=> $a['moyAnnuelle'];
    });

    $pdf = Pdf::loadView('pages.admin.pdf.recapitulatif-conseil', compact('school', 'classe', 'recapData', 'actifYear'))
        ->setPaper('a4', 'landscape');

    return $pdf->download(str("Recapitulatif_Conseil_{$classe->nom}")->slug('_') . '.pdf');
}




   
}
