<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AnswerController;
use App\Http\Controllers\CompetitorController;
use App\Http\Controllers\CompleteProfileController;
use App\Http\Controllers\DataUserController;
use App\Http\Controllers\DescriptionController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\FormTypeController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\MonitoringDashboardController;
use App\Http\Controllers\OptionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\QuestionTypeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SubUnitController;
use App\Http\Controllers\SubUnitQuestionController;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\UnitController;

use App\Http\Controllers\Auth\LoginController;


/*
|--------------------------------------------------------------------------
| Login dan Register
|--------------------------------------------------------------------------
*/

Route::controller(LoginController::class)->middleware('guest')->group(function () {
    Route::get('/login', 'showLoginForm')
        ->name('login');

    Route::post('/login', 'login');

    Route::post('/logout', 'logout')
        ->withoutMiddleware('guest')
        ->middleware('auth')
        ->name('logout');
});


/*
|--------------------------------------------------------------------------
| Semua route yang membutuhkan login
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:Admin,pm,surveyor,monitoring')->group(function () {
        Route::get(
            '/admin/dashboard',
            [MonitoringDashboardController::class, 'index']
        )->name('admin.dashboard');

        Route::get(
            '/pm/dashboard',
            [MonitoringDashboardController::class, 'index']
        )->name('pm.dashboard');

        Route::get(
            '/surveyor/dashboard',
            [MonitoringDashboardController::class, 'index']
        )->name('surveyor.dashboard');

        Route::get(
            '/monitoring/respondent/{userId}/detail',
            [MonitoringDashboardController::class, 'respondentDetail']
        )
            ->whereNumber('userId')
            ->name('monitoring.respondent.detail');
    });

    Route::middleware('role:Admin')->group(function () {


    /*
    |--------------------------------------------------------------------------
    | Data User
    |--------------------------------------------------------------------------
    */

    Route::controller(DataUserController::class)->group(function () {
        Route::get('/datauser', 'index')
            ->name('admin.datauser');

        Route::post('/datauser/store', 'store')
            ->name('admin.datauser.store');

        Route::get('/export-template-user', 'export')
            ->name('admin.export.usertemplate');

        Route::post('/import-datauser', 'import')
            ->name('admin.import.datauser');

        Route::delete('/bulk-delete', 'bulkDelete')
            ->name('admin.datauser.bulk-delete');

        Route::get('/datauseredit/{id}/edit', 'edit')
            ->name('admin.datauser.edit');

        Route::put('/admin/datauser/{id}', 'update')
            ->name('admin.datauser.update');

        Route::get(
            '/datauseredit/{id}/editpassword',
            'edit_password'
        )->name('admin.datauser.editpassword');

        Route::put(
            '/datauser/{id}/updatepassword',
            'update_password'
        )->name('admin.datauser.updatepassword');

        Route::delete(
            '/datauser/{id}/resetjawaban',
            'resetjawaban'
        )->name('admin.datauser.resetjawaban');

        Route::post(
            '/datauser/{id}/resetprofile',
            'resetAccount'
        )->name('admin.datauser.resetaccount');

        Route::get('/datauser/{id}/show', 'show')
            ->name('admin.datauser.show');

        Route::delete('/datauser/{id}', 'destroy')
            ->name('admin.datauser.destroy');
    });


    /*
    |--------------------------------------------------------------------------
    | Activity
    |--------------------------------------------------------------------------
    */

    Route::controller(ActivityController::class)->group(function () {
        Route::get('/dataactivity', 'index')
            ->name('admin.activity');

        Route::get('/masterdataactivity', 'masterdata')
            ->name('admin.masterdata.activity');

        Route::post('/activity/store', 'store')
            ->name('admin.storeactivity');

        Route::get('/export-activity', 'export')
            ->name('admin.export.activity');

        Route::post('/import-activity', 'import')
            ->name('admin.import.activity');

        Route::delete('/activities/bulk-delete', 'bulkDelete')
            ->name('activities.bulkDelete');

        Route::get('/activities/{id}/edit', 'edit')
            ->name('activities.edit');

        Route::put('/activities/{id}', 'update')
            ->name('activities.update');

        Route::delete('/activities/{id}', 'destroy')
            ->name('activities.destroy');
    });


    /*
    |--------------------------------------------------------------------------
    | Group
    |--------------------------------------------------------------------------
    */

    Route::controller(GroupController::class)->group(function () {
        Route::get('/masterdatagroup', 'masterdata')
            ->name('admin.masterdata.groups');

        Route::get('/groups/download-template', 'downloadTemplate')
            ->name('groups.downloadTemplate');

        Route::post('/groups/import', 'import')
            ->name('groups.import');

        Route::post('/groups/store', 'store')
            ->name('groups.store');

        Route::delete('/groups/bulkDelete', 'bulkDelete')
            ->name('groups.bulkDelete');

        Route::get('/groups/{id}/edit', 'edit')
            ->name('groups.edit');

        Route::put('/groups/{id}', 'update')
            ->name('groups.update');

        Route::delete('/groups/{id}', 'destroy')
            ->name('groups.destroy');

        Route::get('/groups/{id}', 'index')
            ->name('admin.groups');
    });


    /*
    |--------------------------------------------------------------------------
    | Complete Profile
    |--------------------------------------------------------------------------
    */

    Route::controller(CompleteProfileController::class)
        ->group(function () {
            Route::post('/cprofile/store', 'store')
                ->name('cprofile.store');

            Route::put('/cprofile/{id}', 'update')
                ->name('cprofile.update');

            Route::delete('/cprofile/{id}', 'destroy')
                ->name('cprofile.destroy');
        });


    /*
    |--------------------------------------------------------------------------
    | Unit
    |--------------------------------------------------------------------------
    |
    | Controller group sebelumnya salah menggunakan GroupController.
    |--------------------------------------------------------------------------
    */

    Route::controller(UnitController::class)->group(function () {
        Route::get('/masterdataunit', 'masterdata')
            ->name('admin.masterdata.unit');

        Route::post('/units/store', 'store')
            ->name('units.store');

        Route::delete('/units/bulkDelete', 'bulkDelete')
            ->name('units.bulkDelete');

        Route::get('/export-units', 'downloadTemplate')
            ->name('units.export');

        Route::post('/unit/import-unit', 'import')
            ->name('units.import');

        Route::get('/units/{id}/edit', 'edit')
            ->name('units.edit');

        Route::put('/unit/{id}', 'update')
            ->name('units.update');

        Route::delete('/units/{id}', 'destroy')
            ->name('units.destroy');

        Route::get('/units/{id}', 'index')
            ->name('admin.units');
    });


    /*
    |--------------------------------------------------------------------------
    | Form
    |--------------------------------------------------------------------------
    */

    Route::controller(FormController::class)->group(function () {
        Route::get('/masterdataform', 'masterdata')
            ->name('forms.masterdata');

        Route::post('/forms/storeforms', 'store')
            ->name('forms.storeforms');

        Route::post('/forms/import-form', 'import')
            ->name('forms.import');

        Route::get('/export-forms', 'export')
            ->name('forms.export');

        Route::delete('/forms/bulkDelete', 'bulkDelete')
            ->name('forms.bulkDelete');

        Route::post('/forms/{id}/copy', 'copy')
            ->name('forms.copy');

        Route::get('/forms/{id}/edit', 'edit')
            ->name('forms.edit');

        Route::put('/forms/{id}', 'update')
            ->name('forms.update');

        Route::delete('/forms/{id}', 'destroy')
            ->name('forms.destroy');

        Route::get('/forms/{id}', 'index')
            ->name('admin.forms');
    });


    /*
    |--------------------------------------------------------------------------
    | Role
    |--------------------------------------------------------------------------
    */

    Route::controller(RoleController::class)->group(function () {
        Route::get('/roles', 'index')
            ->name('admin.roles');

        Route::post('/roles/storeroles', 'store')
            ->name('roles.storeroles');

        Route::get('/export-roles', 'export')
            ->name('roles.export');

        Route::post('/import-roles', 'import')
            ->name('roles.import');

        Route::delete('/roles.bulkDelete', 'bulkDelete')
            ->name('roles.bulkDelete');

        Route::get('/roles/{id}/edit', 'edit')
            ->name('roles.edit');

        Route::put('/roles/{id}', 'update')
            ->name('roles.update');

        Route::delete('/roles/{id}', 'destroy')
            ->name('roles.destroy');
    });


    /*
    |--------------------------------------------------------------------------
    | Form Type
    |--------------------------------------------------------------------------
    */

    Route::controller(FormTypeController::class)
        ->group(function () {
            Route::get('/formtype', 'index')
                ->name('admin.formtype');

            Route::post('/formtype/store', 'store')
                ->name('formtype.store');

            Route::get('/export-formtype', 'export')
                ->name('formtype.export');

            Route::post('/formtype-import', 'import')
                ->name('formtype.import');

            Route::delete('/formtype/bulkDelete', 'bulkDelete')
                ->name('formtype.blukDelete');

            Route::get('/formtype/{id}/edit', 'edit')
                ->name('formtype.edit');

            Route::put('/formtype/{id}', 'update')
                ->name('formtype.update');

            Route::delete('/formtype/{id}', 'destroy')
                ->name('formtype.destroy');
        });


    /*
    |--------------------------------------------------------------------------
    | Question Type
    |--------------------------------------------------------------------------
    */

    Route::controller(QuestionTypeController::class)
        ->group(function () {
            Route::get('/questtype', 'index')
                ->name('admin.questtype');

            Route::post('/questtype/store', 'store')
                ->name('questtype.store');

            Route::get('/export-questtype', 'export')
                ->name('questtype.export');

            Route::post('/questtype-import', 'import')
                ->name('questtype.import');

            Route::delete('/questtype/bulkDelete', 'bulkDelete')
                ->name('questtype.bulkDelete');

            Route::get('/questtype/{id}/edit', 'edit')
                ->name('questtype.edit');

            Route::put('/questtype/{id}', 'update')
                ->name('questtype.update');

            Route::delete('/questtype/{id}', 'destroy')
                ->name('questtype.destroy');
        });


    /*
    |--------------------------------------------------------------------------
    | Question
    |--------------------------------------------------------------------------
    */

    Route::controller(QuestionController::class)
        ->group(function () {
            Route::get('/masterdataquestion', 'masterdata')
                ->name('question.masterdata');

            Route::post('/question/store', 'store')
                ->name('question.store');

            Route::get(
                '/question/template/{formId}',
                'downloadTemplate'
            )->name('question.template');

            Route::post(
                '/question/import/{formId}',
                'import'
            )->name('question.import');

            Route::get('/question/{id}/edit', 'edit')
                ->name('question.edit');

            Route::put('/question/{id}', 'update')
                ->name('question.update');

            Route::delete('/question/{id}', 'destroy')
                ->name('question.destroy');
        });


    /*
    |--------------------------------------------------------------------------
    | Option
    |--------------------------------------------------------------------------
    */

    Route::controller(OptionController::class)
        ->group(function () {
            Route::post('/options/store', 'store')
                ->name('options.store');

            Route::get('/options/{id}/edit', 'edit')
                ->name('options.edit');

            Route::put('/options/{id}', 'update')
                ->name('options.update');

            Route::delete('/options/{id}', 'destroy')
                ->name('options.destroy');
        });


    /*
    |--------------------------------------------------------------------------
    | Sub Unit
    |--------------------------------------------------------------------------
    */

    Route::controller(SubUnitController::class)
        ->group(function () {
            Route::post('/subunits/store', 'store')
                ->name('subunits.store');

            Route::post(
                '/subunits/import-subunits',
                'import'
            )->name('subunits.import');

            Route::get(
                '/subunits/template/{unitId}',
                'downloadTemplate'
            )->name('subunits.template');

            Route::get(
                '/subunits/export/{unitId}',
                'export'
            )->name('subunits.export');

            Route::delete(
                '/subunits/bulk-delete',
                'bulkDelete'
            )->name('subunits.bulk-delete');

            Route::put('/subunits/{id}', 'update')
                ->name('subunits.update');

            Route::delete('/subunits/{id}', 'destroy')
                ->name('subunits.destroy');

            Route::get('/subunits/{id}', 'index')
                ->name('admin.subunit');
        });


    /*
    |--------------------------------------------------------------------------
    | Sub Unit Question
    |--------------------------------------------------------------------------
    */

    Route::prefix('subunit-questions')->group(function () {
        Route::post(
            '/toggle',
            [SubUnitQuestionController::class, 'toggle']
        )->name('subunit-question.toggle');
    });


    /*
    |--------------------------------------------------------------------------
    | Competitor
    |--------------------------------------------------------------------------
    */

    Route::controller(CompetitorController::class)
        ->group(function () {
            Route::post('/competitor/store', 'store')
                ->name('competitor.store');

            Route::put('/competitor/{id}', 'update')
                ->name('competitor.update');

            Route::delete('/competitor/{id}', 'destroy')
                ->name('competitor.destroy');
        });


    /*
    |--------------------------------------------------------------------------
    | Description
    |--------------------------------------------------------------------------
    */

    Route::controller(DescriptionController::class)
        ->group(function () {
            Route::post('/description/store', 'store')
                ->name('description.store');

            Route::put('/description/{id}', 'update')
                ->name('description.update');

            Route::delete('/description/{id}', 'destroy')
                ->name('description.destroy');
        });

        Route::get(
            '/get-groups/{activityID}',
            [GroupController::class, 'getGroups']
        )
            ->whereNumber('activityID')
            ->name('get-groups');
    });


    /*
    |--------------------------------------------------------------------------
    | Profile
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:user')->group(function () {
        Route::get('/user/dashboard', function () {
            return view('user.dashboard');
        })->name('user.dashboard');

        Route::controller(ProfileController::class)
            ->group(function () {
            Route::get(
                '/user/complete-profile',
                'complete'
            )->name('profile.complete');

            Route::post(
                '/user/complete-profile',
                'store'
            )->name('profile.store');

            Route::get('/user/profile', 'show')
                ->name('profile.show');

            Route::get('/user/profile/edit', 'edit')
                ->name('profile.edit');

            Route::put('/user/profile', 'update')
                ->name('profile.update');

            Route::get(
                '/user/profile/groups/{group}/units',
                'getUnitsByGroup'
            )->name('profile.units');
            });


    /*
    |--------------------------------------------------------------------------
    | Survey
    |--------------------------------------------------------------------------
    */

        Route::middleware('check.profile')->group(function () {
            Route::get(
                '/user/survey',
                [SurveyController::class, 'index']
            )->name('survey.index');

            Route::get(
                '/survey/form/{form}',
                [SurveyController::class, 'show']
            )->name('survey.show');

            Route::post(
                '/survey/form/{form}/save',
                [AnswerController::class, 'store']
            )->name('survey.save');

            Route::put(
                '/survey/form/{form}',
                [AnswerController::class, 'update']
            )->name('survey.update');

            Route::get(
                '/survey/finish',
                [SurveyController::class, 'finishPage']
            )->name('survey.finish.page');

            Route::post(
                '/survey/finish',
                [SurveyController::class, 'finish']
            )->name('survey.finish');
        });
    });
});


/*
|--------------------------------------------------------------------------
| Redirect Halaman Awal
|--------------------------------------------------------------------------
|
| Sementara semua user yang sudah login diarahkan ke dashboard yang sama.
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    if (! auth()->check()) {
        return redirect()->route('login');
    }

    $role = strtolower(trim((string) auth()->user()->role?->name));

    return match ($role) {
        'admin' => redirect()->route('admin.dashboard'),
        'pm' => redirect()->route('pm.dashboard'),
        'surveyor' => redirect()->route('surveyor.dashboard'),
        'monitoring' => redirect()->route('admin.dashboard'),
        'user' => redirect()->route('user.dashboard'),
        default => abort(403, 'Role tidak dikenali.'),
    };
});
