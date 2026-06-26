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
            $this->chargerDonneesBulletin($inscriptionId, $trimestreId, $sequences),
        ];

        // 2. Récupérer les stats réelles de la classe de cet élève
        $classeId = $bulletins[0]['inscription']->classe_id;
        $anneeActive = DB::table('annee_scolaires')->where('est_active', 1)->first();
        $inscriptionIds = DB::table('inscriptions')
            ->where('classe_id', $classeId)
            ->where('annee_scolaire_id', $anneeActive->id)
            ->pluck('id');


        // $stats = $this->calculerStatistiquesClasse([$inscriptionId], $trimestreId);
        // AJOUTEZ CECI : Des statistiques vides par défaut pour l'impression solo

        $stats = $this->calculerStatistiquesEnMemoire($bulletins);
        $pdf = Pdf::loadView('pages.admin.pdf.bulletin-single', compact('bulletins', 'trimestre', 'sequences', 'etablissement', 'stats'))->setPaper('a4', 'portrait');

        return $pdf->download("Bulletin_Eleve.pdf");
    }

    // 2. NOUVEAU : Impression de TOUTE la classe d'un coup


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

        // 1. Récupération des notes en gros
        $allNotes = DB::table('notes')
            ->join('evaluations', 'notes.evaluation_id', '=', 'evaluations.id')
            ->whereIn('notes.inscription_id', $inscriptionIds)
            ->whereIn('evaluations.sequence_id', $sequences->pluck('id'))
            ->select('notes.*', 'evaluations.matiere_id', 'evaluations.sequence_id')
            ->get()
            ->groupBy('inscription_id');

        // 2. Boucle pour remplir $bulletins
        $bulletins = [];
        $moyennesIndividuelles = []; // Tableau pour stocker les moyennes de tous les élèves


        foreach ($inscriptionIds as $id) {
            $notesEleve = $allNotes->get($id, collect());
            $bulletin = $this->chargerDonneesBulletin($id, $trimestreId, $sequences, $notesEleve);

            // DÉBOGAGE : Si le bilan est null, on calcule la moyenne manuellement à partir des notes
            // On ignore le bilan pour le calcul des statistiques de classe
            $moyenneEleve = 0;

            // Si votre bulletin contient des notes, calculons la moyenne ici pour les stats
            if (!empty($notesEleve)) {
                // Logique de calcul simplifiée : somme des notes / nombre de notes
                $totalNotes = 0;
                foreach ($notesEleve as $n) {
                    $totalNotes += $n->valeur;
                }
                $moyenneEleve = count($notesEleve) > 0 ? $totalNotes / count($notesEleve) : 0;
            }

            $moyennesIndividuelles[] = (float)$moyenneEleve;

            // On injecte cette moyenne calculée dans le bulletin pour que la vue l'utilise
            $bulletin['moyenne_calculee'] = $moyenneEleve;
            $bulletins[] = $bulletin;






            // calcul de rang

            // 1. On crée le tableau de classement
            $classement = [];
            foreach ($bulletins as $b) {
                $classement[] = [
                    'id' => $b['inscription']->inscription_id,
                    'moyenne' => $b['moyenne_calculee']
                ];
            }

            // 2. On trie par moyenne (du plus grand au plus petit)
            usort($classement, function ($a, $b) {
                return $b['moyenne'] <=> $a['moyenne'];
            });

            // 3. ICI : On calcule les rangs en tenant compte des ex-aequo
            $rangs = [];
            foreach ($classement as $index => $item) {
                // Si la moyenne est identique au précédent, on prend le même rang
                if ($index > 0 && $item['moyenne'] == $classement[$index - 1]['moyenne']) {
                    $rangs[$item['id']] = $rangs[$classement[$index - 1]['id']];
                } else {
                    // Sinon, c'est le rang naturel
                    $rangs[$item['id']] = $index + 1;
                }
            }

            // 4. ICI : On injecte le rang dans chaque bulletin
            foreach ($bulletins as &$b) {
                $b['rang'] = $rangs[$b['inscription']->inscription_id];
            }
        }








        // 3. Calcul des statistiques "à la volée" (votre proposition)
        // $stats = $this->calculerStatistiquesEnMemoire($bulletins);
        // CALCUL DYNAMIQUE ET SANS REDONDANCE
        $totalEleves = count($moyennesIndividuelles);
        $stats = [
            'moyenne' => $totalEleves > 0 ? array_sum($moyennesIndividuelles) / $totalEleves : 0,
            'min' => $totalEleves > 0 ? min($moyennesIndividuelles) : 0,
            'max' => $totalEleves > 0 ? max($moyennesIndividuelles) : 0,
            'taux_reussite' => $totalEleves > 0 ? (count(array_filter($moyennesIndividuelles, fn($m) => $m >= 10)) / $totalEleves) * 100 : 0
        ];

        // dd($stats) ;

        $pdf = Pdf::loadView('pages.admin.pdf.bulletin-single', compact('bulletins', 'trimestre', 'sequences', 'etablissement', 'stats'))->setPaper('a4', 'portrait');

        return $pdf->download("Bulletins_Classe.pdf");
    }





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

        // DEBUG : Ajoutez ceci temporairement juste avant la requête
        //  dump($inscriptionId, $trimestreId);
        // 2. Récupération du suivi disciplinaire pour cet élève au trimestre donné
        $suiviDisciplinaire = DB::table('suivi_disciplinaires')
            ->where('inscription_id', $inscriptionId)
            ->where('trimestre_id', $trimestreId) // Assurez-vous d'avoir cette variable $trimestreId
            ->first();


        // DEBUG : Voyez-vous un objet ou NULL ?
        // dd($suiviDisciplinaire);


        // 2. Récupération des matières
        $matieres = DB::table('classe_matiere')
            ->join('matieres', 'classe_matiere.matiere_id', '=', 'matieres.id')
            // Jointure sur les groupes de matières
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
                'groupes_matieres.id as groupe_id', // Ajouté pour le groupBy
                'groupes_matieres.nom as groupe_nom', // Ajouté pour l'affichage
                'groupes_matieres.ordre as groupe_ordre', // Très important pour le tri
                DB::raw("COALESCE(users.name, 'Non assigné') as prof_nom")
            )
            ->orderBy('groupes_matieres.ordre', 'asc') // Tri par groupe
            ->get()
            ->groupBy('groupe_id'); // On regroupe par ID de groupe;






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

        // return compact('inscription', 'totalElevesClasse', 'matieres', 'notes', 'coefficients', 'bilan');

        // À la fin de chargerDonneesBulletin
        return [
            'inscription' => $inscription,
            'totalElevesClasse' => $totalElevesClasse,
            'matieres' => $matieres,
            'notes' => $notes,
            'coefficients' => $coefficients,
            'bilan' => $bilan,
            'suivi' => $suiviDisciplinaire, // Ajout du suivi disciplinaire
        ];
    }







    // Nouvelle fonction privée qui calcule tout à partir des bulletins générés
    private function calculerStatistiquesEnMemoire($bulletins)
    {
        $moyennes = [];
        $nbReussites = 0;

        foreach ($bulletins as $b) {
            // On récupère la moyenne depuis le bulletin généré
            // Remplacez 'moyenne_generale' par la clé exacte que votre fonction chargerDonneesBulletin utilise
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
}
