<?php

namespace App\Services;

use App\Models\Affectation;
use App\Models\Bilan;
use App\Models\Evaluation;
use App\Models\Inscription;
use App\Models\Moyenne;
use App\Models\Note;
use App\Models\Sequence;
use Illuminate\Support\Facades\DB;

class MoyenneService
{
    /**
     * Calcule et enregistre les moyennes par matière (stats incluses).
     */
    public function calculerMoyennesSequentielles($classeId, $sequenceId)
    {
        // 1. On récupère la structure réelle (matières + coefficients)
        $structureClasse = DB::table('classe_matiere')
            ->where('classe_id', $classeId)
            ->get();

        $inscriptions = Inscription::where('classe_id', $classeId)->get();

        foreach ($structureClasse as $matiereStructure) {
            $notesMatiere = [];
            $coefMatiere = ($matiereStructure->coefficient && $matiereStructure->coefficient > 0)
                ? $matiereStructure->coefficient
                : 1;

            foreach ($inscriptions as $inscription) {
                // Récupération de l'ID de l'évaluation
                $evaluationId = $this->getEvaluationId($matiereStructure, $sequenceId);

                // $note = Note::where([
                //     'inscription_id' => $inscription->id,
                //     'evaluation_id'  => $evaluationId
                // ])->first();

                // $valeurNote = $note ? $note->valeur : 0;




                // On cherche la note
                $noteRecord = Note::where([
                    'inscription_id' => $inscription->id,
                    'evaluation_id'  => $evaluationId
                ])->first();

                // DISTINCTION CRUCIALE :
                // Si l'enregistrement n'existe pas du tout ($noteRecord est null)
                if (!$noteRecord) {
                    $valeurNote = 0;
                    $appreciation = "Non Évalué";
                } else {
                    // Si l'enregistrement existe (même si la valeur est 0)
                    $valeurNote = $noteRecord->valeur;
                    $appreciation = $this->getAppreciationMatiere($valeurNote);
                }







                // Enregistrement initial de la moyenne
                Moyenne::updateOrCreate(
                    [
                        'inscription_id' => $inscription->id,
                        'matiere_id'     => $matiereStructure->matiere_id,
                        'sequence_id'    => $sequenceId,
                    ],
                    [
                        'valeur'       => $valeurNote,
                        'coefficient'  => $coefMatiere,
                        'total_points' => $valeurNote * $coefMatiere,
                        'rang'         => 0,
                        'appreciation' => $appreciation, // On enregistre l'appréciation ici aussi
                    ]
                );

                $notesMatiere[$inscription->id] = $valeurNote;
            }

            // 2. Calcul des Rangs, Min, Max et Moyenne de Classe pour CETTE matière
            $this->remplirStatsEtRangsMatiere($classeId, $matiereStructure->matiere_id, $sequenceId, $notesMatiere);
        }

        return true;
    }

    private function remplirStatsEtRangsMatiere($classeId, $matiereId, $sequenceId, $notes)
    {
        if (empty($notes)) return;

        arsort($notes);

        $min = min($notes);
        $max = max($notes);
        $avg = array_sum($notes) / count($notes);

        $rang = 1;
        $precedenteNote = null;
        $positionReelle = 1;

        foreach ($notes as $inscriptionId => $note) {
            if ($precedenteNote !== null && $note < $precedenteNote) {
                $rang = $positionReelle;
            }

            Moyenne::where([
                'inscription_id' => $inscriptionId,
                'matiere_id'     => $matiereId,
                'sequence_id'    => $sequenceId,
            ])->update([
                'rang'           => $rang,
                'moyenne_classe' => $avg,
                'min_classe'     => $min,
                'max_classe'     => $max,
                'appreciation'   => $this->getAppreciationMatiere($note)
            ]);

            $precedenteNote = $note;
            $positionReelle++;
        }
    }

    private function getAppreciationMatiere($note)
    {

        if ($note >= 17) return "Excellent";
        if ($note >= 14) return "Très Bien";
        if ($note >= 12) return "Bien";
        if ($note >= 10) return "Passable";
        return "Insuffisant";
    }

    public function genererBilansSequentiels($classeId, $sequenceId, $anneeScolaireId)
    {
        $inscriptions = Inscription::where('classe_id', $classeId)->get();
        $moyennesGenerales = [];

        foreach ($inscriptions as $inscription) {
            $detailsMoyennes = Moyenne::where([
                'inscription_id' => $inscription->id,
                'sequence_id'    => $sequenceId
            ])->get();

            $totalPoints = $detailsMoyennes->sum('total_points');
            $totalCoefs  = $detailsMoyennes->sum('coefficient');
            $moyenneG    = ($totalCoefs > 0) ? ($totalPoints / $totalCoefs) : 0;

            Bilan::updateOrCreate(
                [
                    'inscription_id'    => $inscription->id,
                    'sequence_id'       => $sequenceId,
                    'annee_scolaire_id' => $anneeScolaireId,
                ],
                [
                    'total_points'    => $totalPoints,
                    'total_coefs'     => $totalCoefs,
                    'moyenne'         => $moyenneG,
                    'effectif_classe' => $inscriptions->count(),
                    'mention'         => $this->getMention($moyenneG),
                    'rang'            => 0,
                ]
            );

            $moyennesGenerales[$inscription->id] = $moyenneG;
        }

        $this->attribuerRangsGeneraux($classeId, $sequenceId, $moyennesGenerales);
        return true;
    }

    private function attribuerRangsGeneraux($classeId, $sequenceId, $moyennes)
    {
        if (empty($moyennes)) return;
        arsort($moyennes);

        $max = max($moyennes);
        $min = min($moyennes);
        $avg = array_sum($moyennes) / count($moyennes);

        $rang = 1;
        $precedenteMoyenne = null;
        $positionReelle = 1;

        foreach ($moyennes as $inscriptionId => $moyenne) {
            if ($precedenteMoyenne !== null && $moyenne < $precedenteMoyenne) {
                $rang = $positionReelle;
            }

            Bilan::where([
                'inscription_id' => $inscriptionId,
                'sequence_id'    => $sequenceId
            ])->update([
                'rang'            => $rang,
                'moyenne_premier' => $max,
                'moyenne_dernier' => $min,
                'moyenne_classe'  => $avg
            ]);

            $precedenteMoyenne = $moyenne;
            $positionReelle++;
        }
    }

    private function getEvaluationId($matiereStructure, $sequenceId)
    {
        return Evaluation::where([
            'classe_id'   => $matiereStructure->classe_id,
            'matiere_id'  => $matiereStructure->matiere_id,
            'sequence_id' => $sequenceId
        ])->value('id');
    }
    /**
     * Système de mentions camerounais standard.
     */
    private function getMention($moyenne)
    {
        if ($moyenne >= 18) return "Excellent";
        if ($moyenne >= 16) return "Très Bien";
        if ($moyenne >= 14) return "Bien";
        if ($moyenne >= 12) return "Assez Bien";
        if ($moyenne >= 10) return "Passable";
        if ($moyenne >= 8)  return "Médiocre";
        return "Faible";
    }









    public function calculerMoyennesTrimestrielles($classeId, $trimestreId)
    {
        // 1. Trouver les IDs des deux séquences liées à ce trimestre
        $sequences = Sequence::where('trimestre_id', $trimestreId)->pluck('id');

        if ($sequences->count() < 2) {
            // Optionnel : Gérer le cas où une seule séquence est faite
        }

        $inscriptions = Inscription::where('classe_id', $classeId)->get();
        $structureClasse = DB::table('classe_matiere')->where('classe_id', $classeId)->get();

        foreach ($structureClasse as $matiere) {
            $notesTrimestre = [];

            foreach ($inscriptions as $inscription) {
                // Récupérer les moyennes séquentielles déjà calculées
                $moyennesSeq = Moyenne::where('inscription_id', $inscription->id)
                    ->where('matiere_id', $matiere->matiere_id)
                    ->whereIn('sequence_id', $sequences)
                    ->get();

                // Calcul de la moyenne du trimestre pour cette matière
                $valeurTrim = $moyennesSeq->count() > 0 ? $moyennesSeq->avg('valeur') : 0;
                $coef = $matiere->coefficient ?? 1;

                // Enregistrement (On utilise le champ trimestre_id cette fois !)
                Moyenne::updateOrCreate(
                    [
                        'inscription_id' => $inscription->id,
                        'matiere_id'     => $matiere->matiere_id,
                        'trimestre_id'   => $trimestreId, // Différent de la séquence
                    ],
                    [
                        'valeur'       => $valeurTrim,
                        'coefficient'  => $coef,
                        'total_points' => $valeurTrim * $coef,
                        'sequence_id'  => null, // Important : c'est un bilan de trimestre
                        'appreciation' => $this->getAppreciationMatiere($valeurTrim),
                        'rang'         => 0, // <--- AJOUTE CETTE LIGNE ICI
                    ]
                );

                $notesTrimestre[$inscription->id] = $valeurTrim;
            }

            // Calcul des stats de classe pour le trimestre (Min, Max, Rang)
            $this->remplirStatsTrimestreMatiere($classeId, $matiere->matiere_id, $trimestreId, $notesTrimestre);
        }

        return true;
    }









    private function remplirStatsTrimestreMatiere($classeId, $matiereId, $trimestreId, $notes)
    {
        if (empty($notes)) return;

        arsort($notes);

        $min = min($notes);
        $max = max($notes);
        $avg = array_sum($notes) / count($notes);

        $rang = 1;
        $precedenteNote = null;
        $positionReelle = 1;

        foreach ($notes as $inscriptionId => $note) {
            if ($precedenteNote !== null && $note < $precedenteNote) {
                $rang = $positionReelle;
            }

            Moyenne::where([
                'inscription_id' => $inscriptionId,
                'matiere_id'     => $matiereId,
                'trimestre_id'   => $trimestreId,
            ])->update([
                'rang'           => $rang,
                'moyenne_classe' => $avg,
                'min_classe'     => $min,
                'max_classe'     => $max,
            ]);

            $precedenteNote = $note;
            $positionReelle++;
        }
    }
}
