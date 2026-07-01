<?php

use App\Http\Controllers\AcademiqueController;
use App\Http\Controllers\Admin\AuditSaisieController;
use App\Http\Controllers\Admin\BulletinPrintController;
use App\Http\Controllers\Admin\ResultatController;
use App\Http\Controllers\Admin\StatisticController;
use App\Http\Controllers\Admin\StatistiqueController;
use App\Http\Controllers\AffectationController;
use App\Http\Controllers\AnneeScolaireController;
use App\Http\Controllers\ClasseController;
use App\Http\Controllers\ClasseMatiereController;
use App\Http\Controllers\DashboardTeacherController;
use App\Http\Controllers\DepartementController;
use App\Http\Controllers\DisciplineController;
use App\Http\Controllers\EleveController;
use App\Http\Controllers\EnseignantController;
use App\Http\Controllers\EtablissementController;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\GroupeMatiereController;
use App\Http\Controllers\MatiereController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TrimestreController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

//Rou globale configuration middleware admin
Route::middleware(['auth', 'admin'])->group(function () {

    Route::get('admin/student/print', [EleveController::class, 'imprimer'])->name('admin.eleves.imprimer');
    // Section Paramètres
    Route::get('/configuration-school', [EtablissementController::class, 'edit'])->name('settings.index');
    Route::put('/configuration-school', [EtablissementController::class, 'update'])->name('settings.update');


    //Annees scolaires




    // On regroupe tout sous le préfixe 'settings'
    Route::prefix('settings')->name('settings.')->group(function () {

        // Cette ligne gère TOUT (Index, Store, Edit, Update, Destroy)
        // Elle crée automatiquement la route 'settings.annees.edit' et 'settings.annees.update'
        Route::resource('years', AnneeScolaireController::class)->parameters([
            'years' => 'annee_scolaire' // Pour que Laravel injecte bien le modèle dans ton Controller
        ]);

        // On ajoute juste la route personnalisée pour l'activation (PATCH est plus correct que GET ici)
        Route::patch('years/{annee_scolaire}/activer', [AnneeScolaireController::class, 'set_active'])->name('years.active');
    });



    Route::prefix('settings/academic')->name('settings.academique.')->group(function () {
        Route::get('/', [AcademiqueController::class, 'index'])->name('index');

        // CYCLES
        Route::post('/cycles', [AcademiqueController::class, 'storeCycle'])->name('cycles.store');
        Route::get('/cycles/{cycle}/edit', [AcademiqueController::class, 'editCycle'])->name('cycles.edit');
        Route::put('/cycles/{cycle}', [AcademiqueController::class, 'updateCycle'])->name('cycles.update');
        Route::delete('/cycles/{cycle}', [AcademiqueController::class, 'destroyCycle'])->name('cycles.destroy');

        // NIVEAUX
        Route::post('/level', [AcademiqueController::class, 'storeNiveau'])->name('niveaux.store');
        Route::get('/level/{niveau}/edit', [AcademiqueController::class, 'editNiveau'])->name('niveaux.edit');
        Route::put('/level/{niveau}', [AcademiqueController::class, 'updateNiveau'])->name('niveaux.update');
        Route::delete('/level/{niveau}', [AcademiqueController::class, 'destroyNiveau'])->name('niveaux.destroy');
    });

    Route::prefix('settings/classes')->name('settings.classes.')->group(function () {
        Route::get('/', [ClasseController::class, 'index'])->name('index');
        Route::post('/', [ClasseController::class, 'store'])->name('store');
        Route::delete('/{classe}', [ClasseController::class, 'destroy'])->name('destroy');
    });


    Route::prefix('settings/courses')->name('settings.matieres.')->group(function () {
        Route::get('/', [MatiereController::class, 'index'])->name('index');
        Route::post('/', [MatiereController::class, 'store'])->name('store');
        Route::get('edit/{matiere}', [MatiereController::class, 'edit'])->name('edit');
        Route::put('/{matiere}', [MatiereController::class, 'update'])->name('update');
        Route::delete('/{matiere}', [MatiereController::class, 'destroy'])->name('destroy');
    });

    Route::get('settings/classes/{classe}/matieres', [ClasseMatiereController::class, 'edit'])->name('settings.classes.matieres.edit');
    Route::post('settings/classes/{classe}/matieres', [ClasseMatiereController::class, 'update'])->name('settings.classes.matieres.update');



    Route::prefix('admin/students')->name('admin.students.')->group(function () {
        Route::get('/corbeille', [EleveController::class, 'trashed'])->name('trashed');
        Route::patch('/{id}/restore', [EleveController::class, 'restore'])->name('restore');
        Route::delete('/{id}/force-delete', [EleveController::class, 'forceDelete'])->name('force-delete');
    });



    Route::prefix('admin')->name('admin.')->group(function () {

        // --- GESTION DES ELEVES ---
        // Cette ressource gère l'index, le create, le store, l'edit, le show, etc.


        Route::resource('students', EleveController::class);

        // --- RECHERCHE RAPIDE (Optionnel pour plus tard) ---
        Route::get('search/students', [EleveController::class, 'search'])->name('eleves.search');
    });


    Route::prefix('admin')->name('admin.')->group(function () {



        // --- MODULE ENSEIGNANTS ---
        Route::get('teachers', [EnseignantController::class, 'index'])->name('enseignants.index');
        Route::get('teachers/create', [EnseignantController::class, 'create'])->name('enseignants.create');
        Route::post('teachers', [EnseignantController::class, 'store'])->name('enseignants.store');
        Route::get('teachers/{enseignant}/edit', [EnseignantController::class, 'edit'])->name('enseignants.edit');
        Route::put('teachers/{enseignant}', [EnseignantController::class, 'update'])->name('enseignants.update');
        Route::delete('teachers/{enseignant}', [EnseignantController::class, 'destroy'])->name('enseignants.destroy');
        Route::get('teachers/{enseignant}', [EnseignantController::class, 'show'])->name('enseignants.show');

        // --- MODULE PEDAGOGIQUE (AFFECTATIONS) ---
        // Rappel : Place la route 'index' avant d'éventuels paramètres dynamiques
        Route::get('affectations', [AffectationController::class, 'index'])->name('affectations.index');
        Route::post('affectations', [AffectationController::class, 'store'])->name('affectations.store');
        // Dans routes/web.php, à l'intérieur de ton groupe 'admin'
        Route::post('/affectations/store/bulk-store', [AffectationController::class, 'bulkStore'])->name('affectations.bulk-store');
    });

    Route::prefix('admin')->name('admin.')->group(function () {
        // ... tes autres routes ...

        Route::get('/audit-saisie', [AuditSaisieController::class, 'index'])->name('audit.saisie');

        // On pourra ajouter plus tard :
        // Route::get('/audit-saisie/classe/{id}', [AuditSaisieController::class, 'show'])->name('audit.saisie.show');
        // Route::get('/audit-saisie', [AuditSaisieController::class, 'index'])->name('audit.saisie');
        Route::get('/statistics', [StatistiqueController::class, 'index'])->name('statistiques.index');
        // Dans routes/web.php (dans ton groupe de middleware admin)
        Route::get('/statistics/classe/{classe_id}/{sequence_id}', [StatistiqueController::class, 'detailClasse'])
            ->name('statistiques.classe.detail');
    });


    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('trimestres', TrimestreController::class);
    });

    Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
        // ... tes autres routes ...

        Route::get('/results', [ResultatController::class, 'index'])->name('resultats.index');
        Route::post('/results/calculs', [ResultatController::class, 'calculer'])->name('resultats.calculer');

        // On pourra ajouter plus tard :
        // Route::get('/resultats/classe/{id}', [ResultatController::class, 'show'])->name('resultats.show');
    });


    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('/departments', DepartementController::class);
    });
});






// Modeule Evaluations

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/evaluations', [EvaluationController::class, 'index'])->name('evaluations.index');
    Route::post('/evaluations', [EvaluationController::class, 'store'])->name('evaluations.store');
    Route::get('/evaluations/{id}/saisie', [EvaluationController::class, 'saisie'])->name('evaluations.saisie');
    Route::post('/evaluations/{id}/bulk-store', [EvaluationController::class, 'bulkStoreNotes'])->name('evaluations.bulk-store');
    Route::get('/evaluations/{id}/telecharger-stats', [EvaluationController::class, 'telechargerStats'])
    ->name('evaluations.telecharger-stats');
});




Route::prefix('teatcher')->name('enseignant.')->group(function () {
    Route::get('/dashboard', [DashboardTeacherController::class, 'index'])->name('dashboard');
});






// // 1. L'adresse pour afficher la page du formulaire
// Route::get('/admin/statistiques', [StatisticController::class, 'afficherFormulaireStatistiques'])->name('admin.statistiques.index');

// // 2. L'adresse qui reçoit les données quand on clique sur le bouton "Calculer"
// Route::post('/admin/statistiques/generer', [StatisticController::class, 'générerSequence'])->name('admin.statistiques.generer_sequence');



// Route::get('/admin/statistiques/registre', [StatistiqueController::class, 'registreTrimestriel'])->name('admin.statistiques.registre');

Route::get('/admin/report/print/{inscription}/{trimestre}', [BulletinPrintController::class, 'imprimerTrimestriel'])
    ->name('admin.bulletins.imprimer');













// // Page principale : La grille des 4 colonnes avec le choix du trimestre
// Route::get('/admin/bulletins', [BulletinPrintController::class, 'index'])
//     ->name('admin.bulletins.index');

// // Page secondaire : Le Hub de la classe sélectionnée (Liste des élèves)
// Route::get('/admin/bulletins/classe/{classe_id}', [BulletinPrintController::class, 'classeHub'])
//     ->name('admin.bulletins.classe');


// // Route pour générer le PDF de toute la classe d'un coup
// Route::get('/admin/bulletins/classe/{classe_id}/imprimer/{trimestre_id}', [BulletinPrintController::class, 'imprimerClasse'])
//     ->name('admin.bulletins.imprimer-classe');

// // Route pour générer le PDF d'un seul élève isolé
// Route::get('/admin/bulletins/inscription/{inscription_id}/imprimer/{trimestre_id}', [BulletinPrintController::class, 'imprimerEleve'])
//     ->name('admin.bulletins.imprimer-eleve');



// Route::get('/admin/bulletins/classe/{classeId}/trimestre/{trimestreId}', [BulletinPrintController::class, 'imprimerClasse'])
//     ->name('admin.bulletins.classe');










// Page principale : La grille avec le choix du trimestre
Route::get('/admin/report', [BulletinPrintController::class, 'index'])
    ->name('admin.bulletins.index');

// Page secondaire : Le Hub de la classe sélectionnée (Liste des élèves)
Route::get('/admin/report/class/{classe_id}', [BulletinPrintController::class, 'classeHub'])
    ->name('admin.bulletins.classe');

// Route pour générer le PDF de toute la classe d'un coup
Route::get('/admin/report/class/{classe_id}/print/{trimestre_id}', [BulletinPrintController::class, 'imprimerClasse'])
    ->name('admin.bulletins.imprimer-classe');

// Route pour générer le PDF d'un seul élève isolé
Route::get('/admin/report/student/{inscription_id}/print/{trimestre_id}', [BulletinPrintController::class, 'imprimerEleve'])
    ->name('admin.bulletins.imprimer-eleve');







Route::resource('admin/groupes-matieres', GroupeMatiereController::class)->names('admin.groupes');

// Route pour afficher le formulaire de sélection
Route::get('/discipline/selection', [DisciplineController::class, 'index'])->name('discipline.index');

// Route pour afficher la grille après validation du choix
Route::get('/discipline/saisie', [DisciplineController::class, 'saisie'])->name('discipline.saisie');

Route::post('/discipline/store', [DisciplineController::class, 'store'])->name('discipline.store');
require __DIR__ . '/auth.php';
