<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\EmploiDuTempsSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Optionnel : tu peux garder ou supprimer l'utilisateur de test
        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // Appel de ton seeder d'emploi du temps
        $this->call([
            EmploiDuTempsSeeder::class,
        ]);
    }
}
