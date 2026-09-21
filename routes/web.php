<?php

use App\Http\Controllers\AcademicController;
use App\Http\Controllers\Admin\BulletinPrintController;
use App\Http\Controllers\Admin\DataAuditController;
use App\Http\Controllers\Admin\ResultController;
use App\Http\Controllers\Admin\StatistiqueController;
use App\Http\Controllers\AfterLoginController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\CheckProgramController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\ClassSubjectController;
use App\Http\Controllers\DashboardTeacherController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DisciplineController;
use App\Http\Controllers\Exports\DepartmentExportController;
use App\Http\Controllers\Exports\ExportInscriptionController;
use App\Http\Controllers\Exports\StudentControllerExport;
use App\Http\Controllers\Exports\TeacherExportController;
use App\Http\Controllers\GlobalStatController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\PresenceAndServiceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\SequenceController;
use App\Http\Controllers\SessionCourseController;
use App\Http\Controllers\SettingAcademicController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\SubjectGroupController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\TeacherProfileController;
use App\Http\Controllers\TimeSlotController;
use App\Http\Controllers\TrimesterController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\YearController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware('scolarite.coherence')->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->middleware(['auth', 'verified'])->name('dashboard');

    Route::middleware(['auth']) // , 'scolarite.coherence'
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {
            Route::get('teachers', [TeacherController::class, 'index'])->name('enseignants.index');
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
        Route::get('presence/attestation', [PresenceAndServiceController::class, 'generateAttestationPresence'])
            ->name('teacher.attestation.presence');

        Route::get('presence/attestation-take-service', [PresenceAndServiceController::class, 'generateAttestationPriseService'])
            ->name('teacher.attestation.take-service');
        // });
        // attestation reprise de service
        Route::get('presence/attestation-reprise-service', [PresenceAndServiceController::class, 'generateAttestationRepriseService'])
            ->name('teacher.attestation.reprise-service');

        // Rou globale configuration middleware admin
        Route::middleware('admin')->group(function () {

            // Section Paramètres
            Route::get('/configuration-school', [SchoolController::class, 'edit'])->name('settings.index');
            Route::put('/configuration-school', [SchoolController::class, 'update'])->name('settings.update');

            // Annees scolaires

            // On regroupe tout sous le préfixe 'settings'
            Route::prefix('settings')->name('settings.')->group(function () {

                // Cette ligne gère TOUT (Index, Store, Edit, Update, Destroy)
                // Elle crée automatiquement la route 'settings.annees.edit' et 'settings.annees.update'
                Route::resource('years', YearController::class)->parameters([
                    'years' => 'year', // Pour que Laravel injecte bien le modèle dans ton Controller
                ]);

                // On ajoute juste la route personnalisée pour l'activation (PATCH est plus correct que GET ici)
                Route::patch('years/{year}/activer', [YearController::class, 'set_active'])->name('years.active');
            });

            Route::prefix('admin')->name('admin.')->group(function () {
                Route::resource('trimestres', TrimesterController::class);

                // Route::get('/results', [ResultController::class, 'index'])->name('resultats.index');
                //  Route::post('/results/calculs', [ResultController::class, 'calculer'])->name('resultats.calculer');

                // On pourra ajouter plus tard :
                // Route::get('/resultats/classe/{id}', [ResultController::class, 'show'])->name('resultats.show');
            });
        });

        // Modeule Evaluations

        Route::prefix('admin')->name('admin.')->group(function () {
            Route::get('/evaluations', [AssessmentController::class, 'index'])->name('evaluations.index');
            Route::post('/evaluations', [AssessmentController::class, 'store'])->name('evaluations.store');
            Route::get('/evaluations/{id}/saisie', [AssessmentController::class, 'saisie'])->name('evaluations.saisie');
            Route::post('/evaluations/{id}/bulk-store', [AssessmentController::class, 'bulkStoreNotes'])->name('evaluations.bulk-store');
            Route::get('/evaluations/{id}/download-stats', [AssessmentController::class, 'telechargerStats'])
                ->name('evaluations.telecharger-stats');
        });

        Route::prefix('teacher')->name('enseignant.')->group(function () {
            Route::get('/dashboard', [DashboardTeacherController::class, 'index'])->name('dashboard');
        });

        // Route pour afficher le formulaire de sélection

        // Middleware sg
        Route::middleware('sg')->group(function () {
            Route::prefix('discipline')->name('discipline.')->group(function () {
                Route::get('/selection', [DisciplineController::class, 'index'])->name('index');

                Route::get('/saisie', [DisciplineController::class, 'saisie'])->name('saisie');

                Route::post('/store', [DisciplineController::class, 'store'])->name('store');
            });
        });

        Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {

            // Route pour afficher le formulaire de configuration
            Route::get('/settings-classes', [SettingAcademicController::class, 'index'])
                ->name('admin.parametres-classes.index');

            // Route pour enregistrer les changements
            Route::post('/settings-classes', [SettingAcademicController::class, 'store'])
                ->name('admin.parametres-classes.store');
        });
    });

    Route::middleware(['auth'])->group(function () {});

    // Route with censor and admin

    Route::middleware(['auth', 'censeur'])->group(function () {

        // Routes de gestion du verrouillage des séquences
        Route::get('/sequences', [SequenceController::class, 'index'])->name('admin.sequences.index');
        // Route::put('/sequences/{id}', [SequenceController::class, 'update'])->name('admin.sequences.update');
        Route::put('/sequences/{sequence}', [SequenceController::class, 'update'])
            ->name('admin.sequences.update');

        Route::get('/admin/audit-saisie', [DataAuditController::class, 'index'])->name('admin.audit.saisie');
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
        Route::get('/admin/report/class/{classe_id}/print/{trimestre_id}', [BulletinPrintController::class, 'printClasse'])
            ->name('admin.bulletins.imprimer-classe');

        // Route pour générer le PDF d'un seul élève isolé
        Route::get('/admin/report/student/{inscriptionId}/print/{trimestreId}', [BulletinPrintController::class, 'printStudent'])
            ->name('admin.bulletins.imprimer-eleve');

        Route::get('/admin/report/classe/{classeId}/trimestre/{trimestreId}/stats', [BulletinPrintController::class, 'imprimerStatsClasse'])
            ->name('admin.bulletins.download-stats');

        Route::get('/admin/reports/tableau-honneur/{classeId}/{trimestreId}', [BulletinPrintController::class, 'imprimerTableauHonneur'])
            ->name('admin.bulletins.tableau-honneur');

        // users route

        Route::get('admin/users', [UserController::class, 'index'])->name('admin.users.index');
        Route::put('/users/{user}/role', [UserController::class, 'updateRole'])->name('admin.users.update-role');
        Route::delete('admin/users/{user}', [UserController::class, 'destroy'])
            ->name('admin.users.destroy');

        Route::get('admin/departments/export/', [DepartmentExportController::class, 'export'])->name('admin.departments.export');

        Route::prefix('admin')->name('admin.')->group(function () {
            Route::resource('/departments', DepartmentController::class)->except(['show']);
        });

        Route::resource('admin/subject-groups', SubjectGroupController::class)
            ->names('admin.groupes')
            ->parameters(['groupes-matieres' => 'groupe']);         //   Route::resource('admin/groupes-matieres', SubjectGroupController::class)->names('admin.groupes');

        Route::get('admin/student/print', [StudentController::class, 'imprimer'])->name('admin.eleves.imprimer');

        Route::get('admin/students/export/', [StudentControllerExport::class, 'export'])->name('admin.students.export');

        Route::get('admin/inscriptions/export/', [ExportInscriptionController::class, 'export'])->name('admin.inscriptions.export');
        Route::get('admin/teachers/export/', [TeacherExportController::class, 'export'])->name('admin.teachers.export');

        Route::prefix('settings/academic')->name('settings.academique.')->group(function () {
            Route::get('/', [AcademicController::class, 'index'])->name('index');

            // CYCLES
            Route::post('/cycles', [AcademicController::class, 'storeCycle'])->name('cycles.store');
            Route::get('/cycles/{cycle}/edit', [AcademicController::class, 'editCycle'])->name('cycles.edit');
            Route::put('/cycles/{cycle}', [AcademicController::class, 'updateCycle'])->name('cycles.update');
            Route::delete('/cycles/{cycle}', [AcademicController::class, 'destroyCycle'])->name('cycles.destroy');

            // NIVEAUX
            Route::post('/level', [AcademicController::class, 'storeNiveau'])->name('niveaux.store');
            Route::get('/level/{niveau}/edit', [AcademicController::class, 'editNiveau'])->name('niveaux.edit');
            Route::put('/level/{niveau}', [AcademicController::class, 'updateNiveau'])->name('niveaux.update');
            Route::delete('/level/{niveau}', [AcademicController::class, 'destroyNiveau'])->name('niveaux.destroy');
        });

        Route::prefix('settings/classes')->name('settings.classes.')->group(function () {
            Route::get('/', [ClassController::class, 'index'])->name('index');
            Route::post('/', [ClassController::class, 'store'])->name('store');
            Route::delete('/{classe}', [ClassController::class, 'destroy'])->name('destroy');
            Route::get('/settings/classes/{classe}/edit', [ClassController::class, 'edit'])->name('edit');
            Route::put('/settings/classes/{classe}', [ClassController::class, 'update'])->name('update');

        });

        Route::prefix('settings/courses')->name('settings.matieres.')->group(function () {
            Route::get('/', [SubjectController::class, 'index'])->name('index');
            Route::post('/', [SubjectController::class, 'store'])->name('store');
            Route::get('edit/{matiere}', [SubjectController::class, 'edit'])->name('edit');
            Route::put('/{matiere}', [SubjectController::class, 'update'])->name('update');
            Route::delete('/{matiere}', [SubjectController::class, 'destroy'])->name('destroy');
        });

        Route::get('settings/classes/{classe}/matieres', [ClassSubjectController::class, 'edit'])->name('settings.classes.matieres.edit');
        Route::post('settings/classes/{classe}/matieres', [ClassSubjectController::class, 'update'])->name('settings.classes.matieres.update');

        Route::prefix('admin/students')->name('admin.students.')->group(function () {
            Route::get('/corbeille', [StudentController::class, 'trashed'])->name('trashed');
            Route::patch('/{id}/restore', [StudentController::class, 'restore'])->name('restore');
            Route::delete('/{id}/force-delete', [StudentController::class, 'forceDelete'])->name('force-delete');
            Route::post('/importer', [StudentController::class, 'importer'])->name('importer');
        });

        Route::prefix('admin')->name('admin.')->group(function () {

            // --- GESTION DES ELEVES ---
            // Cette ressource gère l'index, le create, le store, l'edit, le show, etc.

            Route::resource('students', StudentController::class);

            // --- RECHERCHE RAPIDE (Optionnel pour plus tard) ---
            Route::get('search/students', [StudentController::class, 'search'])->name('eleves.search');
        });

        Route::prefix('admin')->name('admin.')->group(function () {

            // --- MODULE ENSEIGNANTS ---
            // Route::get('teachers', [TeacherController::class, 'index'])->name('enseignants.index');
            Route::get('teachers/create', [TeacherController::class, 'create'])->name('enseignants.create');
            Route::post('teachers', [TeacherController::class, 'store'])->name('enseignants.store');
            Route::get('teachers/{teacher}/edit', [TeacherController::class, 'edit'])->name('enseignants.edit');
            Route::put('teachers/{teacher}', [TeacherController::class, 'update'])->name('enseignants.update');
            Route::delete('teachers/{teacher}', [TeacherController::class, 'destroy'])->name('enseignants.destroy');
            Route::get('teachers/{teacher}', [TeacherController::class, 'show'])->name('enseignants.show');

            // --- MODULE PEDAGOGIQUE (AFFECTATIONS) ---
            // Rappel : Place la route 'index' avant d'éventuels paramètres dynamiques
            Route::get('affectations', [AssignmentController::class, 'index'])->name('affectations.index');
            Route::post('affectations', [AssignmentController::class, 'store'])->name('affectations.store');
            // Dans routes/web.php, à l'intérieur de ton groupe 'admin'
            Route::post('/affectations/store/bulk-store', [AssignmentController::class, 'bulkStore'])->name('affectations.bulk-store');
        });

        Route::prefix('admin')->name('admin.')->group(function () {
            // ... tes autres routes ...

            //     Route::get('/audit-saisie', [DataAuditController::class, 'index'])->name('audit.saisie');

            // On pourra ajouter plus tard :

            Route::get('/statistics', [StatistiqueController::class, 'index'])->name('statistiques.index');
            // Dans routes/web.php (dans ton groupe de middleware admin)
            Route::get('/statistics/classe/{classe_id}/{sequence_id}', [StatistiqueController::class, 'detailClasse'])
                ->name('statistiques.classe.detail');
        });
    });
    // Audit saisie

    Route::middleware(['auth', 'censeur'])
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {

            // Créneaux horaires
            Route::get('/times-slot', [TimeSlotController::class, 'index'])->name('creneaux.index');
            Route::post('/times-slot', [TimeSlotController::class, 'store'])->name('creneaux.store');
            Route::delete('/times-slot/{creneau}', [TimeSlotController::class, 'destroy'])->name('creneaux.destroy');

            // Emplois du temps
            Route::get('/emplois/classes', [SessionCourseController::class, 'indexClasses'])->name('emplois.classes');
            Route::get('/emplois/classe/{classeId}', [SessionCourseController::class, 'showByClasse'])->name('emplois.classe');

            Route::get('/emplois/classe/{classeId}/pdf', [SessionCourseController::class, 'telechargerPdfClasse'])->name('emplois.classe.pdf');
            Route::post('/emplois/seances', [SessionCourseController::class, 'store'])->name('seances.store');
        });
    // });

    Route::middleware('auth')->group(function () {
        Route::get('/emplois/teacher/{userId}', [SessionCourseController::class, 'showByEnseignant'])->name('emplois.enseignant');

        // Emploi du temps de l'enseignant (Téléchargement PDF)
        Route::get('/emplois/teacher/{userId}/pdf', [SessionCourseController::class, 'telechargerPdfEnseignant'])->name('emplois.enseignant.pdf');

        // Route::middleware(['auth', 'censeur'])->group(function () {

        // Afficher les leçons d'une matière pour une classe spécifique
        Route::get('/lessons/{subjectId}/{classRoomId}', [LessonController::class, 'index'])->name('lessons.index');

        // Enregistrer une nouvelle leçon
        Route::post('/lessons', [LessonController::class, 'store'])->name('lessons.store');
    });
    // Route::get('/emplois/teacher/{userId}', [SessionCourseController::class, 'showByEnseignant'])->name('emplois.enseignant');

    // // Emploi du temps de l'enseignant (Téléchargement PDF)
    // Route::get('/emplois/teacher/{userId}/pdf', [SessionCourseController::class, 'telechargerPdfEnseignant'])->name('emplois.enseignant.pdf');

    Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::put('/teacher/{id}/reset-password', [UserController::class, 'resetPassword'])->name('enseignants.reset-password');
    });

    Route::get('/program-progress', [CheckProgramController::class, 'index'])
        ->middleware(['auth'])
        ->name('avancement.index');

    Route::post('/sequences/{sequence}/classes/{classe}/calculate', [SequenceController::class, 'calculateClassAverages'])
        ->name('admin.sequences.calculate');

    Route::get(
        '/admin/report/classe/{classeId}/trimestre/{trimestreId}/controle-notes',
        [BulletinPrintController::class, 'imprimerEtatControleNotes']
    )->name('admin.bulletins.etat-controle-notes');

    Route::get('/trimestres/{trimestreId}/globales-stats', [GlobalStatController::class, 'imprimerStatsGlobales'])
        ->name('stats.globales.pdf')
        ->middleware(['auth']);
});
require __DIR__.'/auth.php';
