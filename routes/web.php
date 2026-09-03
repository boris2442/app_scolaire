<?php

use App\Http\Controllers\AcademiqueController;
use App\Http\Controllers\Admin\AuditSaisieController;
use App\Http\Controllers\Admin\BulletinPrintController;
use App\Http\Controllers\Admin\ResultatController;
use App\Http\Controllers\Admin\StatisticController;
use App\Http\Controllers\Admin\StatistiqueController;
use App\Http\Controllers\AffectationController;
use App\Http\Controllers\AfterLoginController;
use App\Http\Controllers\AnneeScolaireController;
use App\Http\Controllers\CheckProgramController;
use App\Http\Controllers\ClasseController;
use App\Http\Controllers\ClasseMatiereController;
use App\Http\Controllers\CreneauController;
use App\Http\Controllers\DashboardTeacherController;
use App\Http\Controllers\DepartementController;
use App\Http\Controllers\DisciplineController;
use App\Http\Controllers\EleveController;
use App\Http\Controllers\EnseignantController;
use App\Http\Controllers\EtablissementController;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\Exports\DepartmentExportController;
use App\Http\Controllers\Exports\ExportInscriptionController;
use App\Http\Controllers\Exports\StudentControllerExport;
use App\Http\Controllers\Exports\TeacherExportController;
use App\Http\Controllers\GroupeMatiereController;
use App\Http\Controllers\LeconController;
use App\Http\Controllers\MatiereController;
use App\Http\Controllers\ParametreAcademiqueController;
use App\Http\Controllers\PresenceAndServiceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SeanceController;
use App\Http\Controllers\TeacherProfileController;
use App\Http\Controllers\TrimestreController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\SGMiddleware;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');



Route::middleware(['auth'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('teachers', [EnseignantController::class, 'index'])->name('enseignants.index');
    });


// });











Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('welcome-dashboard', [AfterLoginController::class, 'index'])->name('after.login.page');
    Route::patch('/teacher-profile', [TeacherProfileController::class, 'update'])
        ->name('enseignant.profile.update');

    // Route pour télécharger l'attestation de présence effective
    Route::get('presence/{id}/attestation-presence', [PresenceAndServiceController::class, 'generateAttestationPresence'])
        ->name('teacher.attestation.presence');

    Route::get('presence/{id}/attestation-take-service', [PresenceAndServiceController::class, 'generateAttestationPriseService'])
        ->name('teacher.attestation.take-service');
    // });
    //attestation reprise de service
    Route::get('presence/{id}/attestation-reprise-service', [PresenceAndServiceController::class, 'generateAttestationRepriseService'])
        ->name('teacher.attestation.reprise-service');



    //Rou globale configuration middleware admin
    Route::middleware('admin')->group(function () {


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







        Route::prefix('admin')->name('admin.')->group(function () {
            Route::resource('trimestres', TrimestreController::class);


            // Route::get('/results', [ResultatController::class, 'index'])->name('resultats.index');
            //  Route::post('/results/calculs', [ResultatController::class, 'calculer'])->name('resultats.calculer');

            // On pourra ajouter plus tard :
            // Route::get('/resultats/classe/{id}', [ResultatController::class, 'show'])->name('resultats.show');
        });
    });






    // Modeule Evaluations

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/evaluations', [EvaluationController::class, 'index'])->name('evaluations.index');
        Route::post('/evaluations', [EvaluationController::class, 'store'])->name('evaluations.store');
        Route::get('/evaluations/{id}/saisie', [EvaluationController::class, 'saisie'])->name('evaluations.saisie');
        Route::post('/evaluations/{id}/bulk-store', [EvaluationController::class, 'bulkStoreNotes'])->name('evaluations.bulk-store');
        Route::get('/evaluations/{id}/download-stats', [EvaluationController::class, 'telechargerStats'])
            ->name('evaluations.telecharger-stats');
    });




    Route::prefix('teatcher')->name('enseignant.')->group(function () {
        Route::get('/dashboard', [DashboardTeacherController::class, 'index'])->name('dashboard');
    });




    // Route pour afficher le formulaire de sélection

    //Middleware sg
    Route::middleware('sg')->group(function () {
        Route::prefix('discipline')->name('discipline.')->group(function () {
            Route::get('/selection', [DisciplineController::class, 'index'])->name('index');

            Route::get('/saisie', [DisciplineController::class, 'saisie'])->name('saisie');

            Route::post('/store', [DisciplineController::class, 'store'])->name('store');
        });
    });


    Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {

        // Route pour afficher le formulaire de configuration
        Route::get('/settings-classes', [ParametreAcademiqueController::class, 'index'])
            ->name('admin.parametres-classes.index');

        // Route pour enregistrer les changements
        Route::post('/settings-classes', [ParametreAcademiqueController::class, 'store'])
            ->name('admin.parametres-classes.store');
    });
});




Route::middleware(['auth'])->group(function () {});

//Route with censor and admin

Route::middleware(['auth', 'censeur'])->group(function () {

    Route::get('/admin/audit-saisie', [AuditSaisieController::class, 'index'])->name('admin.audit.saisie');
    // Page principale : La grille avec le choix du trimestre
    Route::get('/admin/report', [BulletinPrintController::class, 'index'])
        ->name('admin.bulletins.index');

    Route::get('/admin/report/print/{inscription}/{trimestre}', [BulletinPrintController::class, 'imprimerTrimestriel'])
        ->name('admin.bulletins.imprimer');









    // // Page principale : La grille des 4 colonnes avec le choix du trimestre

    // Page secondaire : Le Hub de la classe sélectionnée (Liste des élèves)
    Route::get('/admin/report/class/{classe_id}', [BulletinPrintController::class, 'classeHub'])
        ->name('admin.bulletins.classe');

    // Route pour générer le PDF de toute la classe d'un coup
    Route::get('/admin/report/class/{classe_id}/print/{trimestre_id}', [BulletinPrintController::class, 'imprimerClasse'])
        ->name('admin.bulletins.imprimer-classe');

    // Route pour générer le PDF d'un seul élève isolé
    Route::get('/admin/report/student/{inscription_id}/print/{trimestre_id}', [BulletinPrintController::class, 'imprimerEleve'])
        ->name('admin.bulletins.imprimer-eleve');


    Route::get('/admin/report/classe/{classeId}/trimestre/{trimestreId}/stats', [BulletinPrintController::class, 'imprimerStatsClasse'])
        ->name('admin.bulletins.download-stats');

    Route::get('/admin/reports/tableau-honneur/{classeId}/{trimestreId}', [BulletinPrintController::class, 'imprimerTableauHonneur'])
        ->name('admin.bulletins.tableau-honneur');





    //users route

    Route::get('admin/users', [UserController::class, 'index'])->name('admin.users.index');
    Route::put('/users/{user}/role', [UserController::class, 'updateRole'])->name('admin.users.update-role');
    Route::delete('admin/users/{user}', [UserController::class, 'destroy'])
        ->name('admin.users.destroy');



    Route::get('admin/departments/export/', [DepartmentExportController::class, 'export'])->name('admin.departments.export');






    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('/departments', DepartementController::class)->except(['show']);
    });






    Route::resource('admin/groupes-matieres', GroupeMatiereController::class)
        ->names('admin.groupes')
        ->parameters(['groupes-matieres' => 'groupe']);         //   Route::resource('admin/groupes-matieres', GroupeMatiereController::class)->names('admin.groupes');

    Route::get('admin/student/print', [EleveController::class, 'imprimer'])->name('admin.eleves.imprimer');









    Route::get('admin/students/export/', [StudentControllerExport::class, 'export'])->name('admin.students.export');

    Route::get('admin/inscriptions/export/', [ExportInscriptionController::class, 'export'])->name('admin.inscriptions.export');
    Route::get('admin/teachers/export/', [TeacherExportController::class, 'export'])->name('admin.teachers.export');










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
        Route::get('/settings/classes/{classe}/edit', [ClasseController::class, 'edit'])->name('edit');
        Route::put('/settings/classes/{classe}', [ClasseController::class, 'update'])->name('update');
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
        Route::post('/importer', [EleveController::class, 'importer'])->name('importer');
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
        // Route::get('teachers', [EnseignantController::class, 'index'])->name('enseignants.index');
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

        //     Route::get('/audit-saisie', [AuditSaisieController::class, 'index'])->name('audit.saisie');

        // On pourra ajouter plus tard :

        Route::get('/statistics', [StatistiqueController::class, 'index'])->name('statistiques.index');
        // Dans routes/web.php (dans ton groupe de middleware admin)
        Route::get('/statistics/classe/{classe_id}/{sequence_id}', [StatistiqueController::class, 'detailClasse'])
            ->name('statistiques.classe.detail');
    });
});
//Audit saisie











Route::middleware(['auth', 'censeur'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Créneaux horaires
        Route::get('/creneaux', [CreneauController::class, 'index'])->name('creneaux.index');
        Route::post('/creneaux', [CreneauController::class, 'store'])->name('creneaux.store');
        Route::delete('/creneaux/{creneau}', [CreneauController::class, 'destroy'])->name('creneaux.destroy');

        // Emplois du temps
        Route::get('/emplois/classes', [SeanceController::class, 'indexClasses'])->name('emplois.classes');
        Route::get('/emplois/classe/{classeId}', [SeanceController::class, 'showByClasse'])->name('emplois.classe');

        Route::get('/emplois/classe/{classeId}/pdf', [SeanceController::class, 'telechargerPdfClasse'])->name('emplois.classe.pdf');
        Route::post('/emplois/seances', [SeanceController::class, 'store'])->name('seances.store');
    });
// });



Route::middleware(['auth', 'teacher'])->group(function () {
    Route::get('/emplois/teacher/{userId}', [SeanceController::class, 'showByEnseignant'])->name('emplois.enseignant');

    // Emploi du temps de l'enseignant (Téléchargement PDF)
    Route::get('/emplois/teacher/{userId}/pdf', [SeanceController::class, 'telechargerPdfEnseignant'])->name('emplois.enseignant.pdf');

    // Route::middleware(['auth', 'censeur'])->group(function () {

    // Afficher les leçons d'une matière pour une classe spécifique
    Route::get('/lessons/{subjectId}/{classRoomId}', [LeconController::class, 'index'])->name('lessons.index');

    // Enregistrer une nouvelle leçon
    Route::post('/lessons', [LeconController::class, 'store'])->name('lessons.store');
});
// Route::get('/emplois/teacher/{userId}', [SeanceController::class, 'showByEnseignant'])->name('emplois.enseignant');

// // Emploi du temps de l'enseignant (Téléchargement PDF)
// Route::get('/emplois/teacher/{userId}/pdf', [SeanceController::class, 'telechargerPdfEnseignant'])->name('emplois.enseignant.pdf');


Route::get('/avancement-programmes', [CheckProgramController::class, 'index'])
    ->middleware(['auth'])
    ->name('avancement.index');
require __DIR__ . '/auth.php';
