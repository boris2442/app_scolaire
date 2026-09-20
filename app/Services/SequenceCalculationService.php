<?php

namespace App\Services;

namespace App\Services;

use Illuminate\Support\Facades\DB;

class SequenceCalculationService
{
    /**
     * Calcule, enregistre les moyennes et met à jour les statistiques de classe.
     */
    public function processSequenceAverages(int $sequenceId, int $classeId): void
    {
        DB::transaction(function () use ($sequenceId, $classeId) {

            // 0. Récupérer le trimestre_id associé à la séquence
            $trimesterId = DB::table('sequences')
                ->where('id', $sequenceId)
                ->value('trimestre_id');

            // 1. Récupérer tous les élèves inscrits dans cette classe
            $studentIds = DB::table('inscriptions')
                ->where('classe_id', $classeId)
                ->pluck('id');

            if ($studentIds->isEmpty()) {
                return;
            }

            // 2. Récupérer toutes les matières et leurs coefficients pour cette classe
            $matieres = DB::table('classe_matiere')
                ->where('classe_id', $classeId)
                ->pluck('coefficient', 'matiere_id'); // [matiere_id => coefficient]

            if ($matieres->isEmpty()) {
                return;
            }

            // 3. Récupérer le nombre total d'évaluations prévues par matière pour cette séquence et cette classe
            $evaluationsCountByMatiere = DB::table('evaluations')
                ->where('sequence_id', $sequenceId)
                ->where('classe_id', $classeId)
                ->select('matiere_id', DB::raw('COUNT(id) as total_evals'))
                ->groupBy('matiere_id')
                ->pluck('total_evals', 'matiere_id');

            // 4. Récupérer la somme des notes réelles des élèves par matière
            $sumNotes = DB::table('notes')
                ->join('evaluations', 'notes.evaluation_id', '=', 'evaluations.id')
                ->where('evaluations.sequence_id', $sequenceId)
                ->where('evaluations.classe_id', $classeId)
                ->select(
                    'notes.inscription_id',
                    'evaluations.matiere_id',
                    DB::raw('SUM(notes.valeur) as total_notes_obtenues')
                )
                ->groupBy('notes.inscription_id', 'evaluations.matiere_id')
                ->get()
                ->keyBy(function ($item) {
                    return $item->inscription_id . '_' . $item->matiere_id;
                });

            // 5. Générer les enregistrements pour CHAQUE élève et CHAQUE matière (avec 0 si absence)
            $now = now();
            $records = [];

            foreach ($studentIds as $studentId) {
                foreach ($matieres as $matiereId => $coeff) {
                    $totalEvals = $evaluationsCountByMatiere[$matiereId] ?? 0;

                    // Si aucune évaluation n'a été créée pour cette matière dans la séquence, on passe
                    if ($totalEvals === 0) {
                        continue;
                    }

                    $key = $studentId . '_' . $matiereId;
                    $sumObtenue = isset($sumNotes[$key]) ? (float) $sumNotes[$key]->total_notes_obtenues : 0.0;

                    // Calcul de la moyenne : somme des notes divisée par le nombre d'évaluations (les manquantes comptent pour 0)
                    $moyenne = round($sumObtenue / $totalEvals, 2);
                    $totalPoints = round($moyenne * $coeff, 2);

                    $records[] = [
                        'inscription_id' => $studentId,
                        'matiere_id'     => $matiereId,
                        'sequence_id'    => $sequenceId,
                        'trimestre_id'   => $trimesterId,
                        'valeur'         => $moyenne,
                        'coefficient'    => $coeff,
                        'total_points'   => $totalPoints,
                        'created_at'     => $now,
                        'updated_at'     => $now,
                    ];
                }
            }

            if (empty($records)) {
                return;
            }

            // 6. Nettoyage ciblé et réinsertion
            DB::table('moyennes')
                ->where('sequence_id', $sequenceId)
                ->whereIn('inscription_id', $studentIds)
                ->delete();

            DB::table('moyennes')->insert($records);

            // 7. Calcul vectorisé des Rangs et Statistiques de Classe
            $this->updateClassStatistics($sequenceId, $studentIds);
        });
    }

    /**
     * Calcule le rang, min, max, moyenne de classe et appréciations.
     */
    private function updateClassStatistics(int $sequenceId, $studentIds): void
    {
        $stats = DB::table('moyennes')
            ->where('sequence_id', $sequenceId)
            ->whereIn('inscription_id', $studentIds)
            ->select('id', 'valeur', 'matiere_id')
            ->selectRaw('RANK() OVER (PARTITION BY matiere_id ORDER BY valeur DESC) as calculated_rank')
            ->selectRaw('MIN(valeur) OVER (PARTITION BY matiere_id) as calculated_min')
            ->selectRaw('MAX(valeur) OVER (PARTITION BY matiere_id) as calculated_max')
            ->selectRaw('ROUND(AVG(valeur) OVER (PARTITION BY matiere_id), 2) as calculated_avg')
            ->get();

        $cases = [
            'rang'           => [],
            'min_classe'     => [],
            'max_classe'     => [],
            'moyenne_classe' => [],
            'appreciation'   => [],
        ];

        $ids = [];

        foreach ($stats as $row) {
            $id = $row->id;
            $ids[] = $id;
            $valeur = (float) $row->valeur;

            $cases['rang'][]           = "WHEN {$id} THEN {$row->calculated_rank}";
            $cases['min_classe'][]     = "WHEN {$id} THEN {$row->calculated_min}";
            $cases['max_classe'][]     = "WHEN {$id} THEN {$row->calculated_max}";
            $cases['moyenne_classe'][] = "WHEN {$id} THEN {$row->calculated_avg}";
            $cases['appreciation'][]   = "WHEN {$id} THEN " . DB::getPdo()->quote($this->resolveAppreciation($valeur));
        }

        if (empty($ids)) {
            return;
        }

        $idList = implode(',', $ids);

        DB::statement("
            UPDATE moyennes 
            SET 
                rang = CASE id " . implode(' ', $cases['rang']) . " END,
                min_classe = CASE id " . implode(' ', $cases['min_classe']) . " END,
                max_classe = CASE id " . implode(' ', $cases['max_classe']) . " END,
                moyenne_classe = CASE id " . implode(' ', $cases['moyenne_classe']) . " END,
                appreciation = CASE id " . implode(' ', $cases['appreciation']) . " END
            WHERE id IN ({$idList})
        ");
    }

    /**
     * Attribue l'appréciation selon la note.
     */
    private function resolveAppreciation(float $note): string
    {
        return match (true) {
            $note >= 16 => 'Très Bien',
            $note >= 14 => 'Bien',
            $note >= 12 => 'Assez Bien',
            $note >= 10 => 'Passable',
            $note >= 8  => 'Insuffisant',
            default     => 'Médiocre',
        };
    }
}
