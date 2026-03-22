<?php

use App\Http\Controllers\EtablissementController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AnneeScolaireController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
// Section Paramètres
Route::get('/configuration-etablissement', [EtablissementController::class, 'edit'])->name('settings.index');
Route::put('/configuration-etablissement', [EtablissementController::class, 'update'])->name('settings.update');


//Annees scolaires


// Route::prefix('settings')->name('settings.')->group(function () {
//     Route::resource('annees', AnneeScolaireController::class)->parameters([
//         'annees' => 'annee_scolaire'
//     ]);
//     // Route spéciale pour l'activation
//     Route::patch('annees/{annee_scolaire}/activer', [AnneeScolaireController::class, 'set_active'])->name('annees.active');
// });

// On regroupe tout sous le préfixe 'settings'
Route::prefix('settings')->name('settings.')->group(function () {

    // Cette ligne gère TOUT (Index, Store, Edit, Update, Destroy)
    // Elle crée automatiquement la route 'settings.annees.edit' et 'settings.annees.update'
    Route::resource('annees', AnneeScolaireController::class)->parameters([
        'annees' => 'annee_scolaire' // Pour que Laravel injecte bien le modèle dans ton Controller
    ]);

    // On ajoute juste la route personnalisée pour l'activation (PATCH est plus correct que GET ici)
    Route::patch('annees/{annee_scolaire}/activer', [AnneeScolaireController::class, 'set_active'])->name('annees.active');
});



require __DIR__ . '/auth.php';
