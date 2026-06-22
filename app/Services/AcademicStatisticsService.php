<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class AcademicStatisticsService
{
    /**
     * Étape 1 : Calcule les moyennes par matière à partir des notes brutes
     * Remplis la table 'moyennes' (Capture 1)
     */
    public function calculerMoyennesMatieresPourSequence($sequenceId, $inscriptionId)
    {
        // Récupérer les notes brutes de la table 'notes' (Capture 3)
        $notesParMatiere = DB::table('notes')
            ->join('evaluations', 'notes.evaluation_id', '=', 'evaluations.id')
            ->where('notes.inscription_id', $inscriptionId)
            ->where('evaluations.sequence_id', $sequenceId)
            ->select(
                'evaluations.matiere_id',
                'evaluations.coefficient',
                DB::raw('AVG(notes.valeur) as note_moyenne')
            )
            ->groupBy('evaluations.matiere_id', 'evaluations.coefficient')
            ->get();

        foreach ($notesParMatiere as $item) {
            $totalPoints = $item->note_moyenne * $item->coefficient;
            $appreciation = $this->obtenirMentionOuAppreciation($item->note_moyenne);

            // Mise à jour ou insertion dans la table 'moyennes'
            DB::table('moyennes')->updateOrInsert(
                [
                    'inscription_id' => $inscriptionId,
                    'matiere_id'     => $item->matiere_id,
                    'sequence_id'    => $sequenceId,
                ],
                [
                    'valeur'       => $item->note_moyenne,
                    'coefficient'  => $item->coefficient,
                    'total_points' => $totalPoints,
                    'appreciation' => $appreciation,
                    'effectif_classe'   => 0, // 👈 AJOUTE CETTE LIGNE ICI
                    'updated_at'   => now(),
                ]
            );
        }
    }

    /**
     * Étape 2 : Calcule la moyenne générale de l'élève sur la séquence
     * Remplis la table 'bilans' (Capture 2)
     */
    public function calculerBilanGeneralSequence($sequenceId, $inscriptionId, $anneeScolaireId)
    {
        // Récupérer la somme des points depuis la table 'moyennes'
        $donneesGlobales = DB::table('moyennes')
            ->where('inscription_id', $inscriptionId)
            ->where('sequence_id', $sequenceId)
            ->select(
                DB::raw('SUM(total_points) as total_points_general'),
                DB::raw('SUM(coefficient) as total_coefficients_general')
            )
            ->first();

        $moyenneGenerale = 0;
        if ($donneesGlobales && $donneesGlobales->total_coefficients_general > 0) {
            $moyenneGenerale = $donneesGlobales->total_points_general / $donneesGlobales->total_coefficients_general;
        }

        $mention = $this->obtenirMentionOuAppreciation($moyenneGenerale);

        // Mise à jour ou insertion dans la table 'bilans'
        DB::table('bilans')->updateOrInsert(
            [
                'inscription_id' => $inscriptionId,
                'sequence_id'    => $sequenceId,
            ],
            [
                'moyenne'          => $moyenneGenerale,
                'total_points'     => $donneesGlobales->total_points_general ?? 0,
                'total_coefs'      => $donneesGlobales->total_coefficients_general ?? 0,
                'mention'          => $mention,
                'annee_scolaire_id' => $anneeScolaireId, // Ajusté selon ta colonne 'annee_scolaire_id'
                'updated_at'       => now(),
            ]
        );
    }

    /**
     * Étape 3 : Distribue les rangs et calcule l'effectif de la classe
     * Met à jour la table 'bilans'
     */
    public function attribuerRangsClasseForSequence($sequenceId, $classeId)
    {
        // Récupérer les bilans des élèves de cette classe triés par moyenne DESC
        $bilans = DB::table('bilans')
            ->join('inscriptions', 'bilans.inscription_id', '=', 'inscriptions.id')
            ->where('inscriptions.classe_id', $classeId)
            ->where('bilans.sequence_id', $sequenceId)
            ->select('bilans.id', 'bilans.moyenne')
            ->orderBy('bilans.moyenne', 'desc')
            ->get();

        // dd($bilans);

        $effectif = $bilans->count();
        $rang = 1;

        foreach ($bilans as $index => $bilan) {
            // Gestion stricte des ex-æquo
            if ($index > 0 && $bilan->moyenne == $bilans[$index - 1]->moyenne) {
                // Reste au même rang que le précédent
            } else {
                $rang = $index + 1;
            }

            DB::table('bilans')
                ->where('id', $bilan->id)
                ->update([
                    'rang'            => $rang,
                    'effectif_classe' => $effectif,
                ]);
        }
    }

    /**
     * Système d'appréciation unique pour harmoniser les tables
     */
    private function obtenirMentionOuAppreciation($note)
    {
        if ($note >= 16) return 'Très Bien';
        if ($note >= 14) return 'Bien';
        if ($note >= 12) return 'Assez Bien';
        if ($note >= 10) return 'Passable';
        return 'Insuffisant';
    }




    public function calculerBilanGeneralTrimestre($trimestreId, $inscriptionId, $anneeScolaireId)
    {
        // 1. Le code cherche quelles séquences appartiennent à ce trimestre (ex: Séquence 1 et Séquence 2)
        $sequenceIds = DB::table('sequences')->where('trimestre_id', $trimestreId)->pluck('id');

        // 2. Il va dans la table 'bilans' et additionne les points et les coefficients de ces deux séquences pour cet élève
        $donneesTrimestre = DB::table('bilans')
            ->where('inscription_id', $inscriptionId)
            ->whereIn('sequence_id', $sequenceIds)
            ->select(
                DB::raw('AVG(moyenne) as moyenne_trimestrielle'),
                DB::raw('SUM(total_points) as total_points_trimestre'),
                DB::raw('SUM(total_coefs) as total_coefs_trimestre')
            )
            ->first();

        $moyenneTrimestre = $donneesTrimestre->moyenne_trimestrielle ?? 0;
        $mention = $this->obtenirMentionOuAppreciation($moyenneTrimestre);

        // 3. IMPORTANT : Il crée une NOUVELLE LIGNE dans la table 'bilans'.
        // Cette ligne représente le TRIMESTRE complet. 
        // Donc 'sequence_id' reste VIDE (null) et 'trimestre_id' est REMPLI.
 DB::table('bilans')->updateOrInsert(
    [
        'inscription_id' => $inscriptionId,
        'trimestre_id'   => $trimestreId,
        'sequence_id'    => null,
        'annee_scolaire_id' => $anneeScolaireId,  // ⬅️ AJOUTE CETTE LIGNE ICI
    ],
    [
        'moyenne'           => $moyenneTrimestre,
        'total_points'      => $donneesTrimestre->total_points_trimestre ?? 0,
        'total_coefs'       => $donneesTrimestre->total_coefs_trimestre ?? 0,
        'mention'           => $mention,
        'annee_scolaire_id' => $anneeScolaireId,
        'rang'              => 0,
        'updated_at'        => now(),
    ]
);
    }




    public function attribuerRangsClasseForTrimestre($trimestreId, $classeId)
    {
        $bilans = DB::table('bilans')
            ->join('inscriptions', 'bilans.inscription_id', '=', 'inscriptions.id')
            ->where('inscriptions.classe_id', $classeId)
            ->where('bilans.trimestre_id', $trimestreId)
            ->whereNull('bilans.sequence_id')
            // MODIFICATION ICI : On force l'ID du bilan sous le nom 'bilan_id'
            ->select('bilans.id', 'bilans.moyenne')
            ->orderBy('bilans.moyenne', 'desc')
            ->get();

        // 🔍 DEBUG : Affiche les bilans trouvés
        \Log::info('DEBUG attribuerRangsClasseForTrimestre', [
            'trimestre_id' => $trimestreId,
            'classe_id' => $classeId,
            'bilans_count' => $bilans->count(),
            'bilans_data' => $bilans->toArray(),
        ]);

        $effectif = $bilans->count();
        $rang = 1;

        foreach ($bilans as $index => $bilan) {
            if ($index > 0 && $bilan->moyenne == $bilans[$index - 1]->moyenne) {
                // Même moyenne = même rang
            } else {
                $rang = $index + 1;
            }

            // MODIFICATION ICI : On utilise le bon alias 'bilan_id'
            DB::table('bilans')
                ->where('id', $bilan->id)
                ->update([
                    'rang'            => $rang,
                    'effectif_classe' => $effectif,
                ]);
        }
    }
}
