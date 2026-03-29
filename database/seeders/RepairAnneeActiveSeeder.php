<?php

namespace Database\Seeders;

use App\Models\AnneeScolaire;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class RepairAnneeActiveSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
   public function run(): void
    {
        // 1. Correction : doesntHave (avec 'nt') pour trouver celles qui sont vides
        $anneesVides = AnneeScolaire::doesntHave('trimestres')->get();

        if ($anneesVides->isEmpty()) {
            $this->command->info("Toutes tes années ont déjà une structure. Rien à réparer !");
            return;
        }

        DB::transaction(function () use ($anneesVides) {
            foreach ($anneesVides as $annee) {
                // Pour chaque année vide, on applique ton automatisation
                for ($i = 1; $i <= 3; $i++) {
                    $trimestre = $annee->trimestres()->create([
                        'nom' => "Trimestre $i"
                    ]);

                    // Création des 2 séquences par trimestre
                    for ($j = 1; $j <= 2; $j++) {
                        $numSeq = ($i - 1) * 2 + $j;
                        $trimestre->sequences()->create([
                            'nom' => "Séquence $numSeq"
                        ]);
                    }
                }
            }
        });

        // 2. Correction : Utiliser $this->command->info pour voir le résultat dans la console
        $this->command->info($anneesVides->count() . " années scolaires ont été restructurées avec succès !");
    }
}

