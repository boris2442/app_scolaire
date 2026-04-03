<?php

namespace App\Services;

use App\Models\Affectation;
use App\Models\Bilan;
use App\Models\Inscription;
use App\Models\Moyenne;
use App\Models\Note;
use Illuminate\Support\Facades\DB;

class MoyenneService
{
    /**
     * Calcule et enregistre les moyennes par matière pour une classe et une séquence.
     */
    // public function calculerMoyennesSequentielles($classeId, $sequenceId)
  
    public function calculerMoyennesSequentielles($classeId, $sequenceId)
    {
        $affectations = Affectation::where('classe_id', $classeId)->get();
        $inscriptions = Inscription::where('classe_id', $classeId)->get();

        foreach ($affectations as $affectation) {
            $notesMatiere = [];

            // Sécurité : Si le coef est null ou 0, on met 1 par défaut pour éviter l'erreur SQL
            $coefMatiere = ($affectation->coefficient && $affectation->coefficient > 0)
                ? $affectation->coefficient
                : 1;

            foreach ($inscriptions as $inscription) {
                $evaluationId = $this->getEvaluationId($affectation, $sequenceId);

                $note = Note::where([
                    'inscription_id' => $inscription->id,
                    'evaluation_id' => $evaluationId
                ])->first();

                $valeurNote = $note ? $note->valeur : 0;

                // On utilise $coefMatiere (sécurisé) au lieu de $affectation->coefficient
                Moyenne::updateOrCreate(
                    [
                        'inscription_id' => $inscription->id,
                        'matiere_id'     => $affectation->matiere_id,
                        'sequence_id'    => $sequenceId,
                    ],
                    [
                        'valeur'       => $valeurNote,
                        'coefficient'  => $coefMatiere,
                        'total_points' => $valeurNote * $coefMatiere,
                        'rang'         => 0,
                    ]
                );

                $notesMatiere[$inscription->id] = $valeurNote;
            }

            $this->attribuerRangsMatiere($classeId, $affectation->matiere_id, $sequenceId, $notesMatiere);
        }

        return true;
    }


    /**
     * Algorithme de tri pour attribuer les rangs par matière.
     */
    private function attribuerRangsMatiere($classeId, $matiereId, $sequenceId, $notes)
    {
        // On trie les notes du plus grand au plus petit
        arsort($notes);

        $rang = 1;
        $precedenteNote = null;
        $positionReelle = 1;

        foreach ($notes as $inscriptionId => $note) {
            // Gestion des ex-aequo
            if ($precedenteNote !== null && $note < $precedenteNote) {
                $rang = $positionReelle;
            }

            Moyenne::where([
                'inscription_id' => $inscriptionId,
                'matiere_id'     => $matiereId,
                'sequence_id'    => $sequenceId,
            ])->update(['rang' => $rang]);

            $precedenteNote = $note;
            $positionReelle++;
        }
    }

    /**
     * Helper pour trouver l'évaluation correspondante (à adapter selon ta logique)
     */
    private function getEvaluationId($affectation, $sequenceId)
    {
        // Ici on suppose qu'il n'y a qu'une évaluation par séquence/matière/classe
        return \App\Models\Evaluation::where([
            'classe_id' => $affectation->classe_id,
            'matiere_id' => $affectation->matiere_id,
            'sequence_id' => $sequenceId
        ])->value('id');
    }











    /**
     * Calcule le bilan final (moyenne générale) pour tous les élèves d'une classe.
     */
    public function genererBilansSequentiels($classeId, $sequenceId, $anneeScolaireId)
    {
        $inscriptions = Inscription::where('classe_id', $classeId)->get();
        $moyennesGenerales = [];

        foreach ($inscriptions as $inscription) {
            // 1. Récupérer toutes les moyennes de l'élève pour cette séquence
            $detailsMoyennes = Moyenne::where([
                'inscription_id' => $inscription->id,
                'sequence_id'    => $sequenceId
            ])->get();

            $totalPoints = $detailsMoyennes->sum('total_points');
            $totalCoefs  = $detailsMoyennes->sum('coefficient');

            $moyenneG = ($totalCoefs > 0) ? ($totalPoints / $totalCoefs) : 0;

            // 2. Enregistrer ou mettre à jour le Bilan
            $bilan = \App\Models\Bilan::updateOrCreate(
                [
                    'inscription_id'    => $inscription->id,
                    'sequence_id'       => $sequenceId,
                    'annee_scolaire_id' => $anneeScolaireId,
                ],
                [
                    'total_points' => $totalPoints,
                    'total_coefs'  => $totalCoefs,
                    'moyenne'      => $moyenneG,
                    'effectif_classe' => $inscriptions->count(),
                    'mention'      => $this->getMention($moyenneG),
                    'rang'         => 0, // Sera mis à jour juste après
                ]
            );

            $moyennesGenerales[$inscription->id] = $moyenneG;
        }

        // 3. Calculer les rangs généraux et les stats de classe
        $this->attribuerRangsGeneraux($classeId, $sequenceId, $moyennesGenerales);

        return true;
    }

    /**
     * Attribue les rangs et calcule les moyennes min/max de la classe.
     */
    private function attribuerRangsGeneraux($classeId, $sequenceId, $moyennes)
    {
        arsort($moyennes); // Tri décroissant des moyennes

        $rang = 1;
        $precedenteMoyenne = null;
        $positionReelle = 1;

        $max = count($moyennes) > 0 ? max($moyennes) : 0;
        $min = count($moyennes) > 0 ? min($moyennes) : 0;
        $avg = count($moyennes) > 0 ? array_sum($moyennes) / count($moyennes) : 0;

        foreach ($moyennes as $inscriptionId => $moyenne) {
            if ($precedenteMoyenne !== null && $moyenne < $precedenteMoyenne) {
                $rang = $positionReelle;
            }

            Bilan::where([
                'inscription_id' => $inscriptionId,
                'sequence_id'    => $sequenceId
            ])->update([
                'rang' => $rang,
                'moyenne_premier' => $max,
                'moyenne_dernier' => $min,
                'moyenne_classe'  => $avg
            ]);

            $precedenteMoyenne = $moyenne;
            $positionReelle++;
        }
    }

    /**
     * Système de mentions camerounais standard.
     */
    private function getMention($moyenne)
    {
        if ($moyenne >= 16) return "Très Bien";
        if ($moyenne >= 14) return "Bien";
        if ($moyenne >= 12) return "Assez Bien";
        if ($moyenne >= 10) return "Passable";
        if ($moyenne >= 8)  return "Médiocre";
        return "Faible";
    }
}
