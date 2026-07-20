<?php

namespace Database\Seeders;

use App\Models\Creneau;
use App\Models\Jour;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmploiDuTempsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Remplir les jours de la semaine
        $jours = [
            ['nom' => 'Lundi', 'ordre' => 1],
            ['nom' => 'Mardi', 'ordre' => 2],
            ['nom' => 'Mercredi', 'ordre' => 3],
            ['nom' => 'Jeudi', 'ordre' => 4],
            ['nom' => 'Vendredi', 'ordre' => 5],
            ['nom' => 'Samedi', 'ordre' => 6],
        ];

        foreach ($jours as $jour) {
            Jour::firstOrCreate(['nom' => $jour['nom']], $jour);
        }

        // 2. Remplir les créneaux horaires types de l'établissement
        $creneaux = [
            ['heure_debut' => '07:30:00', 'heure_fin' => '08:00:00', 'libelle' => 'Heure 1'],
            ['heure_debut' => '08:00:00', 'heure_fin' => '08:55:00', 'libelle' => 'Heure 2'],
            ['heure_debut' => '08:55:00', 'heure_fin' => '09:50:00', 'libelle' => 'Heure 3'],
            ['heure_debut' => '09:50:00', 'heure_fin' => '10:45:00', 'libelle' => 'Heure 4'],
            ['heure_debut' => '11:00:00', 'heure_fin' => '11:55:00', 'libelle' => 'Heure 5'],
            ['heure_debut' => '12:40:00', 'heure_fin' => '13:30:00', 'libelle' => 'Heure 6'],
            ['heure_debut' => '13:50:00', 'heure_fin' => '14:40:00', 'libelle' => 'Heure 7'],
            ['heure_debut' => '14:40:00', 'heure_fin' => '15:30:00', 'libelle' => 'Heure 8'],
            ['heure_debut' => '15:30:00', 'heure_fin' => '16:00:00', 'libelle' => 'Heure 9'],
        ];

        foreach ($creneaux as $creneau) {
            Creneau::firstOrCreate(
                ['heure_debut' => $creneau['heure_debut'], 'heure_fin' => $creneau['heure_fin']],
                $creneau
            );
        }
    }
}
