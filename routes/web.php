<?php

use App\Http\Controllers\AcademiqueController;
use App\Http\Controllers\AffectationController;
use App\Http\Controllers\AnneeScolaireController;
use App\Http\Controllers\ClasseController;
use App\Http\Controllers\ClasseMatiereController;
use App\Http\Controllers\EleveController;
use App\Http\Controllers\EnseignantController;
use App\Http\Controllers\EtablissementController;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\MatiereController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

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



// Route::prefix('settings/academique')->name('settings.academique.')->group(function () {
//     Route::get('/', [AcademiqueController::class, 'index'])->name('index');
//     Route::post('/cycles', [AcademiqueController::class, 'storeCycle'])->name('cycles.store');
//     Route::post('/niveaux', [AcademiqueController::class, 'storeNiveau'])->name('niveaux.store');

//     // Routes Cycles
//     Route::put('/cycles/{cycle}', [AcademiqueController::class, 'updateCycle'])->name('cycles.update');
//     Route::delete('/cycles/{cycle}', [AcademiqueController::class, 'destroyCycle'])->name('cycles.destroy');

//     // Routes Niveaux
//     Route::put('/niveaux/{niveau}', [AcademiqueController::class, 'updateNiveau'])->name('niveaux.update');
//     Route::delete('/niveaux/{niveau}', [AcademiqueController::class, 'destroyNiveau'])->name('niveaux.destroy');
// });





Route::prefix('settings/academique')->name('settings.academique.')->group(function () {
    Route::get('/', [AcademiqueController::class, 'index'])->name('index');

    // CYCLES
    Route::post('/cycles', [AcademiqueController::class, 'storeCycle'])->name('cycles.store');
    Route::get('/cycles/{cycle}/edit', [AcademiqueController::class, 'editCycle'])->name('cycles.edit');
    Route::put('/cycles/{cycle}', [AcademiqueController::class, 'updateCycle'])->name('cycles.update');
    Route::delete('/cycles/{cycle}', [AcademiqueController::class, 'destroyCycle'])->name('cycles.destroy');

    // NIVEAUX
    Route::post('/niveaux', [AcademiqueController::class, 'storeNiveau'])->name('niveaux.store');
    Route::get('/niveaux/{niveau}/edit', [AcademiqueController::class, 'editNiveau'])->name('niveaux.edit');
    Route::put('/niveaux/{niveau}', [AcademiqueController::class, 'updateNiveau'])->name('niveaux.update');
    Route::delete('/niveaux/{niveau}', [AcademiqueController::class, 'destroyNiveau'])->name('niveaux.destroy');
});

Route::prefix('settings/classes')->name('settings.classes.')->group(function () {
    Route::get('/', [ClasseController::class, 'index'])->name('index');
    Route::post('/', [ClasseController::class, 'store'])->name('store');
    Route::delete('/{classe}', [ClasseController::class, 'destroy'])->name('destroy');
});


Route::prefix('settings/matieres')->name('settings.matieres.')->group(function () {
    Route::get('/', [MatiereController::class, 'index'])->name('index');
    Route::post('/', [MatiereController::class, 'store'])->name('store');
    Route::put('/{matiere}', [MatiereController::class, 'update'])->name('update');
    Route::delete('/{matiere}', [MatiereController::class, 'destroy'])->name('destroy');
});

Route::get('settings/classes/{classe}/matieres', [ClasseMatiereController::class, 'edit'])->name('settings.classes.matieres.edit');
Route::post('settings/classes/{classe}/matieres', [ClasseMatiereController::class, 'update'])->name('settings.classes.matieres.update');







Route::prefix('admin/eleves')->name('admin.eleves.')->group(function () {
    Route::get('/corbeille', [EleveController::class, 'trashed'])->name('trashed');
    Route::patch('/{id}/restore', [EleveController::class, 'restore'])->name('restore');
    Route::delete('/{id}/force-delete', [EleveController::class, 'forceDelete'])->name('force-delete');
});



Route::prefix('admin')->name('admin.')->group(function () {

    // --- GESTION DES ELEVES ---
    // Cette ressource gère l'index, le create, le store, l'edit, le show, etc.


    Route::resource('eleves', EleveController::class);

    // --- RECHERCHE RAPIDE (Optionnel pour plus tard) ---
    Route::get('search/eleves', [EleveController::class, 'search'])->name('eleves.search');
});

Route::prefix('admin')->name('admin.')->group(function () {



    // --- MODULE ENSEIGNANTS ---
    Route::get('enseignants', [EnseignantController::class, 'index'])->name('enseignants.index');
    Route::get('enseignants/create', [EnseignantController::class, 'create'])->name('enseignants.create');
    Route::post('enseignants', [EnseignantController::class, 'store'])->name('enseignants.store');

    // --- MODULE PEDAGOGIQUE (AFFECTATIONS) ---
    // Rappel : Place la route 'index' avant d'éventuels paramètres dynamiques
    Route::get('affectations', [AffectationController::class, 'index'])->name('affectations.index');
    Route::post('affectations', [AffectationController::class, 'store'])->name('affectations.store');
    // Dans routes/web.php, à l'intérieur de ton groupe 'admin'
    Route::post('/affectations/store/bulk-store', [AffectationController::class, 'bulkStore'])->name('affectations.bulk-store');
});

// Modeule Evaluations

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/evaluations', [EvaluationController::class, 'index'])->name('evaluations.index');
    Route::post('/evaluations', [EvaluationController::class, 'store'])->name('evaluations.store');
    Route::get('/evaluations/{id}/saisie', [EvaluationController::class, 'saisie'])->name('evaluations.saisie');
    Route::post('/evaluations/{id}/bulk-store', [EvaluationController::class, 'bulkStoreNotes'])->name('evaluations.bulk-store');
});
require __DIR__ . '/auth.php';
