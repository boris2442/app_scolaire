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
        // On récupère l'année active
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

        // Récupérer toutes les classes avec le nombre d'élèves inscrits pour cette année (Sans la table niveaux)
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

        // Informations de la classe (On prend directement le nom de la classe)
        $classe = DB::table('classes')
            ->where('classes.id', $classeId)
            ->select('classes.id', 'classes.nom')
            ->first();

        // Liste des élèves inscrits dans cette classe
        $eleves = DB::table('inscriptions')
            ->join('eleves', 'inscriptions.eleve_id', '=', 'eleves.id')
            ->where('inscriptions.classe_id', $classeId)
            ->where('inscriptions.annee_scolaire_id', $anneeActive->id)
            ->select('inscriptions.id as inscription_id', 'eleves.nom', 'eleves.prenom', 'eleves.matricule', 'eleves.sexe', 'eleves.date_naissance', 'eleves.lieu_naissance')
            ->orderBy('eleves.nom', 'asc')
            ->get();

        return view('pages.admin.bulletins.classe-hub', compact('classe', 'eleves', 'trimestreId'));
    }

    // 3. Impression d'un SEUL élève
    public function imprimerEleve($inscriptionId, $trimestreId)
    {
        $etablissement = DB::table('etablissements')->first();
        $trimestre = DB::table('trimestres')->where('id', $trimestreId)->first();
        $sequences = DB::table('sequences')->where('trimestre_id', $trimestreId)->orderBy('id', 'asc')->take(2)->get();

        $bulletins = [
            $this->chargerDonneesBulletin($inscriptionId, $trimestreId, $sequences),
        ];

        $stats = $this->calculerStatistiquesEnMemoire($bulletins);
        $pdf = Pdf::loadView('pages.admin.pdf.bulletin-single', compact('bulletins', 'trimestre', 'sequences', 'etablissement', 'stats'))->setPaper('a4', 'portrait');

        return $pdf->download("Bulletin_Eleve.pdf");
    }

    // 4. Impression de TOUTE la classe d'un coup
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

        $allNotes = DB::table('notes')
            ->join('evaluations', 'notes.evaluation_id', '=', 'evaluations.id')
            ->whereIn('notes.inscription_id', $inscriptionIds)
            ->whereIn('evaluations.sequence_id', $sequences->pluck('id'))
            ->select('notes.*', 'evaluations.matiere_id', 'evaluations.sequence_id')
            ->get()
            ->groupBy('inscription_id');

        $bulletins = [];
        $moyennesIndividuelles = [];

        foreach ($inscriptionIds as $id) {
            $notesEleve = $allNotes->get($id, collect());
            $bulletin = $this->chargerDonneesBulletin($id, $trimestreId, $sequences, $notesEleve);

            $anneeActiveId = $bulletin['inscription']->annee_scolaire_id;
            $idT1 = $this->getTrimestreIdParIndex($anneeActiveId, 0);
            $idT2 = $this->getTrimestreIdParIndex($anneeActiveId, 1);
            $idT3 = $this->getTrimestreIdParIndex($anneeActiveId, 2);

            $bulletin['moyenne_t1'] = $this->calculerMoyenneTrimestre($id, $idT1);
            $bulletin['moyenne_t2'] = $this->calculerMoyenneTrimestre($id, $idT2);
            $bulletin['moyenne_t3'] = $this->calculerMoyenneTrimestre($id, $idT3);

            $bulletin['moyenne_annuelle'] = ($bulletin['moyenne_t1'] + $bulletin['moyenne_t2'] + $bulletin['moyenne_t3']) / 3;
            $bulletin['moyenne_calculee'] = ($trimestreId == $idT3) ? $bulletin['moyenne_t3'] : (($trimestreId == $idT2) ? $bulletin['moyenne_t2'] : $bulletin['moyenne_t1']);
            $bulletin['est_troisieme_trimestre'] = ($trimestreId == $idT3);

            $bulletins[] = $bulletin;
            $moyennesIndividuelles[] = (float)$bulletin['moyenne_calculee'];
        }

        // Calcul du RANG TRIMESTRIEL
        $classementTrim = $bulletins;
        usort($classementTrim, function ($a, $b) {
            return $b['moyenne_calculee'] <=> $a['moyenne_calculee'];
        });

        // Calcul du RANG ANNUEL
        $classementAnnuel = $bulletins;
        usort($classementAnnuel, function ($a, $b) {
            return $b['moyenne_annuelle'] <=> $a['moyenne_annuelle'];
        });

        // Injection des rangs
        foreach ($bulletins as &$b) {
            $rangT = 1;
            foreach ($classementTrim as $c) {
                if ($c['moyenne_calculee'] > $b['moyenne_calculee']) $rangT++;
            }
            $b['rang'] = $rangT;

            $rangA = 1;
            foreach ($classementAnnuel as $c) {
                if ($c['moyenne_annuelle'] > $b['moyenne_annuelle']) $rangA++;
            }
            $b['rang_annuel'] = $rangA;
        }

        $totalEleves = count($moyennesIndividuelles);
        $stats = [
            'moyenne' => $totalEleves > 0 ? array_sum($moyennesIndividuelles) / $totalEleves : 0,
            'min' => $totalEleves > 0 ? min($moyennesIndividuelles) : 0,
            'max' => $totalEleves > 0 ? max($moyennesIndividuelles) : 0,
            'taux_reussite' => $totalEleves > 0 ? (count(array_filter($moyennesIndividuelles, fn($m) => $m >= 10)) / $totalEleves) * 100 : 0
        ];

        $pdf = Pdf::loadView('pages.admin.pdf.bulletin-single', compact('bulletins', 'trimestre', 'sequences', 'etablissement', 'stats'))->setPaper('a4', 'portrait');

        return $pdf->download("Bulletins_Classe.pdf");
    }

    private function chargerDonneesBulletin($inscriptionId, $trimestreId, $sequences, $notesEleve = null)
    {
        // 1. Récupération des informations de l'élève et de l'inscription (Sans la table niveaux)
        $inscription = DB::table('inscriptions')
            ->join('eleves', 'inscriptions.eleve_id', '=', 'eleves.id')
            ->join('classes', 'inscriptions.classe_id', '=', 'classes.id')
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
                'classes.nom as classe_nom', // On prend directement le nom de la classe
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

        $matieres = DB::table('classe_matiere')
            ->join('matieres', 'classe_matiere.matiere_id', '=', 'matieres.id')
            ->leftJoin('groupes_matieres', 'matieres.groupe_matiere_id', '=', 'groupes_matieres.id')
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
                'classe_matiere.coefficient',
                'groupes_matieres.id as groupe_id',
                'groupes_matieres.nom as groupe_nom',
                'groupes_matieres.ordre as groupe_ordre',
                DB::raw("COALESCE(users.name, 'Non assigné') as prof_nom")
            )
            ->orderBy('groupes_matieres.ordre', 'asc')
            ->get()
            ->groupBy('groupe_id');

        if ($notesEleve !== null) {
            $notesBrutes = $notesEleve;
        } else {
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

        $coefficientsBruts = DB::table('moyennes')
            ->where('inscription_id', $inscriptionId)
            ->whereIn('sequence_id', $sequences->pluck('id'))
            ->select('matiere_id', 'coefficient')->distinct()->get();

        $coefficients = [];
        foreach ($coefficientsBruts as $c) {
            $coefficients[$c->matiere_id] = $c->coefficient;
        }

        $bilan = DB::table('bilans')
            ->where('inscription_id', $inscriptionId)
            ->where('trimestre_id', $trimestreId)
            ->whereNull('sequence_id')
            ->first();

        return [
            'inscription' => $inscription,
            'totalElevesClasse' => $totalElevesClasse,
            'matieres' => $matieres,
            'notes' => $notes,
            'coefficients' => $coefficients,
            'bilan' => $bilan,
            'suivi' => $suiviDisciplinaire,
        ];
    }

    private function calculerStatistiquesEnMemoire($bulletins)
    {
        $moyennes = [];
        $nbReussites = 0;

        foreach ($bulletins as $b) {
            $m = $b['bilan']['moyenne'] ?? $b['bilan']->moyenne ?? 0;

            $moyennes[] = (float)$m;
            if ($m >= 10) {
                $nbReussites++;
            }
        }

        $count = count($moyennes);
        if ($count == 0) return ['min' => 0, 'max' => 0, 'moyenne' => 0, 'taux_reussite' => 0, 'total' => 0];

        return [
            'min' => number_format(min($moyennes), 2),
            'max' => number_format(max($moyennes), 2),
            'moyenne' => number_format(array_sum($moyennes) / $count, 2),
            'taux_reussite' => number_format(($nbReussites / $count) * 100, 2),
            'total_inscrits' => $count
        ];
    }

    private function calculerMoyenneTrimestre($inscriptionId, $trimestreId)
    {
        $inscription = DB::table('inscriptions')->where('id', $inscriptionId)->first();
        if (!$inscription) return 0;

        $classeId = $inscription->classe_id;
        $sequences = DB::table('sequences')->where('trimestre_id', $trimestreId)->pluck('id');

        if ($sequences->isEmpty()) return 0;

        $matieres = DB::table('classe_matiere')
            ->join('matieres', 'classe_matiere.matiere_id', '=', 'matieres.id')
            ->where('classe_matiere.classe_id', $classeId)
            ->select('matieres.id', 'classe_matiere.coefficient')
            ->get();

        $notes = DB::table('notes')
            ->join('evaluations', 'notes.evaluation_id', '=', 'evaluations.id')
            ->where('notes.inscription_id', $inscriptionId)
            ->whereIn('evaluations.sequence_id', $sequences)
            ->select('evaluations.matiere_id', DB::raw('AVG(notes.valeur) as moyenne_matiere'))
            ->groupBy('evaluations.matiere_id')
            ->get()
            ->pluck('moyenne_matiere', 'matiere_id');

        $totalPoints = 0;
        $totalCoeffs = 0;

        foreach ($matieres as $m) {
            $valeur = $notes->get($m->id, 0);
            $totalPoints += $valeur * $m->coefficient;
            $totalCoeffs += $m->coefficient;
        }

        return $totalCoeffs > 0 ? $totalPoints / $totalCoeffs : 0;
    }

    private function getTrimestreIdParIndex($anneeId, $index)
    {
        $trimestres = DB::table('trimestres')
            ->where('annee_scolaire_id', $anneeId)
            ->orderBy('id', 'asc')
            ->get();

        return $trimestres[$index]->id ?? null;
    }




public function imprimerStatsClasse($classeId, $trimestreId)
{
    $etablissement = DB::table('etablissements')->first();
    $trimestre = DB::table('trimestres')->where('id', $trimestreId)->first();
    $classe = DB::table('classes')->where('id', $classeId)->first();
    $sequences = DB::table('sequences')->where('trimestre_id', $trimestreId)->orderBy('id', 'asc')->take(2)->get();
    $anneeActive = DB::table('annee_scolaires')->where('est_active', 1)->first();

    // Récupérer les inscriptions avec les informations des élèves directement
    $inscriptionsEleves = DB::table('inscriptions')
        ->join('eleves', 'inscriptions.eleve_id', '=', 'eleves.id')
        ->where('inscriptions.classe_id', $classeId)
        ->where('inscriptions.annee_scolaire_id', $anneeActive->id)
        ->select('inscriptions.id as inscription_id', 'eleves.nom', 'eleves.prenom')
        ->get();

    $inscriptionIds = $inscriptionsEleves->pluck('inscription_id');

    $allNotes = DB::table('notes')
        ->join('evaluations', 'notes.evaluation_id', '=', 'evaluations.id')
        ->whereIn('notes.inscription_id', $inscriptionIds)
        ->whereIn('evaluations.sequence_id', $sequences->pluck('id'))
        ->select('notes.*', 'evaluations.matiere_id', 'evaluations.sequence_id')
        ->get()
        ->groupBy('inscription_id');

    $resultatsEleves = [];

    foreach ($inscriptionsEleves as $eleve) {
        $id = $eleve->inscription_id;
        $notesEleve = $allNotes->get($id, collect());
        $bulletin = $this->chargerDonneesBulletin($id, $trimestreId, $sequences, $notesEleve);

        $anneeActiveId = $bulletin['inscription']->annee_scolaire_id;
        $idT1 = $this->getTrimestreIdParIndex($anneeActiveId, 0);
        $idT2 = $this->getTrimestreIdParIndex($anneeActiveId, 1);
        $idT3 = $this->getTrimestreIdParIndex($anneeActiveId, 2);

        $moyenneCalculee = match((int)$trimestreId) {
            (int)$idT3 => $this->calculerMoyenneTrimestre($id, $idT3),
            (int)$idT2 => $this->calculerMoyenneTrimestre($id, $idT2),
            default => $this->calculerMoyenneTrimestre($id, $idT1),
        };

        $resultatsEleves[] = [
            'nom' => $eleve->nom,
            'prenom' => $eleve->prenom,
            'moyenne' => (float)$moyenneCalculee,
        ];
    }

    $totalEleves = count($resultatsEleves);
    $moyennesIndividuelles = array_column($resultatsEleves, 'moyenne');

    // 1. Statistiques Générales
    $moyenneClasse = $totalEleves > 0 ? array_sum($moyennesIndividuelles) / $totalEleves : 0;
    $noteMax = $totalEleves > 0 ? max($moyennesIndividuelles) : 0;
    $noteMin = $totalEleves > 0 ? min($moyennesIndividuelles) : 0;
    
    $admis = count(array_filter($moyennesIndividuelles, fn($m) => $m >= 10));
    $refuses = $totalEleves - $admis;
    $tauxReussite = $totalEleves > 0 ? ($admis / $totalEleves) * 100 : 0;

    // 2. Détection automatique du Major de la classe
    $majorNom = 'Aucun';
    $majorPrenom = '';
    if ($totalEleves > 0) {
        // Tri par ordre décroissant pour placer le premier en haut
        usort($resultatsEleves, fn($a, $b) => $b['moyenne'] <=> $a['moyenne']);
        $majorNom = $resultatsEleves[0]['nom'];
        $majorPrenom = $resultatsEleves[0]['prenom'];
    }

    // 3. Répartition par Tranches de Moyennes
    $tranches = [
        'excellence' => 0, // [16 - 20]
        'bien' => 0,       // [14 - 15.99]
        'assez_bien' => 0, // [12 - 13.99]
        'passable' => 0,   // [10 - 11.99]
        'echec' => 0       // [< 10]
    ];

    foreach ($moyennesIndividuelles as $m) {
        if ($m >= 16) {
            $tranches['excellence']++;
        } elseif ($m >= 14) {
            $tranches['bien']++;
        } elseif ($m >= 12) {
            $tranches['assez_bien']++;
        } elseif ($m >= 10) {
            $tranches['passable']++;
        } else {
            $tranches['echec']++;
        }
    }

    $statsGlobales = [
        'total_eleves' => $totalEleves,
        'moyenne_generale' => number_format($moyenneClasse, 2),
        'note_max' => number_format($noteMax, 2),
        'note_min' => number_format($noteMin, 2),
        'major_nom' => $majorNom,
        'major_prenom' => $majorPrenom,
        'admis' => $admis,
        'refuses' => $refuses,
        'taux_reussite' => number_format($tauxReussite, 2),
        'tranches' => $tranches
    ];

    $pdf = Pdf::loadView('pages.admin.pdf.stats-classe', compact('classe', 'trimestre', 'etablissement', 'statsGlobales'))
             ->setPaper('a4', 'portrait');

    return $pdf->download("Statistiques_{$classe->nom}.pdf");
}




















}
