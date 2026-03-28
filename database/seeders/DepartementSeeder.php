<?php

namespace Database\Seeders;

use App\Models\Departement;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $depts = [
            ['nom' => 'Informatique et TIC', 'code' => 'INF'],
            ['nom' => 'Mathématiques', 'code' => 'MATH'],
            ['nom' => 'Lettres et Langues (Français/Anglais)', 'code' => 'LET'],
            ['nom' => 'Sciences Physiques', 'code' => 'PHYS'],
            ['nom' => 'Sciences de la Vie et de la Terre', 'code' => 'SVT'],
            ['nom' => 'Histoire et Géographie', 'code' => 'HISGEO'],
            ['nom' => 'Philosophie et Citoyenneté', 'code' => 'PHILO'],
            ['nom' => 'Génie Civil et Construction', 'code' => 'GC'],
            ['nom' => 'Génie Électrique', 'code' => 'GE'],
            ['nom' => 'Génie Mécanique', 'code' => 'GM'],
            ['nom' => 'Comptabilité et Gestion', 'code' => 'CG'],
            ['nom' => 'Marketing et Vente', 'code' => 'MKT'],
            ['nom' => 'Économie et Droit', 'code' => 'ECO'],
            ['nom' => 'Arts et Culture', 'code' => 'ARTS'],
            ['nom' => 'Éducation Physique et Sportive', 'code' => 'EPS'],
            ['nom' => 'Tourisme et Hôtellerie', 'code' => 'TH'],
            ['nom' => 'Santé et Social', 'code' => 'SANTE'],
            ['nom' => 'Langues Nationales', 'code' => 'LNAT'],
            ['nom' => 'Agronomie et Pastoralisme', 'code' => 'AGRO'],
            ['nom' => 'Chimie Industrielle', 'code' => 'CHIM'],
        ];

        foreach ($depts as $dept) {
            Departement::updateOrCreate(
                ['code' => $dept['code']], // Évite les doublons si tu relances le seeder
                ['nom' => $dept['nom']]
            );
        }
    }
}
