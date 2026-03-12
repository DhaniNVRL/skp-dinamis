<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\DataUserController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\FormTypeController;
use App\Http\Controllers\QuestionTypeController;

// Login Routes
Route::controller(LoginController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login');
    Route::post('/login', 'login');
    Route::post('/logout', 'logout')->name('logout');
});

// Register Routes
Route::controller(RegisterController::class)->group(function () {
    Route::get('/register', 'showRegistrationForm')->name('register');
    Route::post('/register', 'register');
});

Route::middleware('auth')->group(function () {
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
    Route::get('/admin/data_user', function (){
        return view('admin.datauser');
    })->name('admin.datauser');
    Route::get('/surveyor/dashboard', function () {
        return view('surveyor.dashboard');
    })->name('surveyor.dashboard');
    Route::get('/pm/dashboard', function () {
        return view('pm.dashboard');
    })->name('pm.dashboard');
    Route::get('/home', function () {
        return view('user.dashboard');
    })->name('user.dashboard');
});

Route::controller(DataUserController::class)->group(function () {
    Route::get('/datauser', 'index')->name('admin.datauser');
    Route::post('/datauser/store', [DataUserController::class , 'store'])
        ->name('admin.datauser.store');
    Route::get('/export-template-user', [DataUserController::class, 'export'])
        ->name('admin.export.usertemplate');
    Route::post('/import-datauser', [DataUserController::class, 'import'])
        ->name('admin.import.datauser');
    Route::delete('/datauser/{id}', [DataUserController::class, 'destroy'])
        ->name('admin.datauser.destroy');
    Route::get('/datauseredit/{id}/edit', [DataUserController::class, 'edit'])
        ->name('admin.datauser.edit');
    Route::put('/datauser/{id}', [DataUserController::class, 'update'])
        ->name('admin.datauser.update');
    Route::get('/datauseredit/{id}/editpassword', [DataUserController::class, 'edit_password'])
        ->name('admin.datauser.editpassword');
    Route::put('datauser/{id}/updatepassword', [DataUserController::class, 'update_password'])
        ->name('admin.datauser.updatepassword');
    Route::get('/datauser/{id}/detail', [DataUserController::class, 'show'])
        ->name('admin.datauser.show');
    Route::delete('/datauser/{id}/resetjawaban', [DataUserController::class, 'resetjawaban'])
        ->name('admin.datauser.resetjawaban');
    Route::get('/datauser/{id}/show', [DataUserController::class, 'show'])
        ->name('admin.datauser.show');
    Route::post('/datauser/{id}/resetprofile', [DataUserController::class, 'resetAccount'])
        ->name('admin.datauser.resetaccount');
});

Route::controller(ActivityController::class)->group(function(){
    Route::get('/dataactivity', 'index')
        ->name('admin.activity');
    Route::get('/masterdataactivity', 'masterdata')
        ->name('admin.masterdata.activity');
    Route::post('/activity/store', [ActivityController::class, 'store'])
        ->name('admin.storeactivity');
    Route::get('/export-activity', [ActivityController::class, 'export'])
        ->name('admin.export.activity');
    Route::post('/import-activity', [ActivityController::class, 'import'])
        ->name('admin.import.activity');
    Route::delete('/activities/bulk-delete', [ActivityController::class, 'bulkDelete'])
        ->name('activities.bulkDelete');
    Route::delete('/activities/{id}', [ActivityController::class, 'destroy'])
        ->name('activities.destroy');
    Route::get('/activities/{id}/edit', [ActivityController::class, 'edit'])
        ->name('activities.edit');
    Route::put('/activities/{id}', [ActivityController::class, 'update'])
        ->name('activities.update');
});

Route::controller(GroupController::class)->group(function(){
    Route::get('/masterdatagroup', 'masterdata')
        ->name('admin.masterdata.groups');
    Route::delete('/groups/bulk-delete', [GroupController::class, 'bulkDelete'])
         ->name('groups.bulkDelete');
    Route::post('/groups/storegroup', [GroupController::class, 'store'])
        ->name('groups.storegroup');
    Route::get('/export-groups', [GroupController::class, 'export'])
        ->name('groups.export');
    Route::post('import-groups', [GroupController::class, 'import'])
        ->name('groups.import');
    Route::get('/groups/{id}/edit', [GroupController::class, 'edit'])
        ->name('groups.edit');
    Route::put('/groups/{id}', [GroupController::class, 'update'])
        ->name('groups.update');
    Route::delete('/groups/{id}', [GroupController::class, 'destroy'])
        ->name('groups.destroy');
    Route::get('/groups/{id}', 'index')
        ->name('admin.groups');
});

Route::controller(UnitController::class)->group(function(){
    Route::get('/masterdataunit', 'masterdata')
        ->name('admin.masterdata.unit');
    Route::post('/units/storeunit', [UnitController::class, 'store'])
        ->name('units.storeunit');
    Route::delete('/units/bulkDelete', [UnitController::class , 'bulkDelete'])
        ->name('units.bulkDelete');
    Route::get('/export-units', [UnitController::class, 'export'])
        ->name('units.export');
    Route::post('/unit/import-unit', [UnitController::class, 'import'])
        ->name('units.import');
    Route::get('/units/{id}/edit', [UnitController::class, 'edit'])
        ->name('units.edit');
    Route::put('/unit/{id}/', [UnitController::class, 'update'])
        ->name('units.update');
    Route::delete('/units/{id}', [UnitController::class, 'destroy'])
        ->name('units.destroy');
    Route::get('/units/{id}', 'index')->name('admin.units');
});

Route::controller(QuestionTypeController::class)->group(function(){
    Route::post('/forms/storeforms', [FormController::class, 'store'])
        ->name('forms.storeforms');
    Route::post('/forms/import-form', [FormController::class, 'import'])
        ->name('forms.import');
    Route::get('/export-forms', [FormController::class, 'export'])
        ->name('forms.export');
    Route::delete('/forms/bulkDelete', [FormController::class , 'bulkDelete'])
        ->name('forms.bulkDelete');
    Route::get('/forms/{id}/edit', [FormController::class, 'edit'])
        ->name('forms.edit');
    Route::put('/forms/{id}/', [FormController::class, 'update'])
        ->name('forms.update');
    Route::delete('/forms/{id}', [FormController::class, 'destroy'])
        ->name('forms.destroy');
    Route::get('/forms/{id}', 'index')->name('admin.forms');
});

// Roles
    Route::get('/roles', [RoleController::class, 'index'])
        ->name('admin.roles');
    Route::post('/roles/storeroles', [RoleController::class, 'store'])
        ->name('roles.storeroles');
    Route::get('/export-roles', [RoleController::class, 'export'])
        ->name('roles.export');
    Route::post('/import-roles', [RoleController::class, 'import'])
        ->name('roles.import');
    Route::delete('/roles.bulkDelete', [RoleController::class, 'bulkDelete'])
        ->name('roles.bulkDelete');
    Route::get('/roles/{id}/edit', [RoleController::class, 'edit'])
        ->name('roles.edit');
    Route::put('/roles/{id}', [RoleController::class, 'update'])
        ->name('roles.update');
    Route::delete('/roles/{id}', [RoleController::class, 'destroy'])
        ->name('roles.destroy');

// Form Type
    Route::get('/formtype', [FormTypeController::class, 'index'])
        ->name('admin.formtype');
    Route::post('/formtype/store', [FormTypeController::class, 'store'])
        ->name('formtype.store');
    Route::get('/export-formtype', [FormTypeController::class, 'export'])
        ->name('formtype.export');
    Route::post('/formtype-import', [FormTypeController::class, 'import'])
        ->name('formtype.import');
    Route::delete('/formtype/bulkDelete', [FormTypeController::class, 'bulkDelete'])
        ->name('formtype.blukDelete');
    Route::get('/formtype/{id}/edit', [FormTypeController::class, 'edit'])
        ->name('formtype.edit');
    Route::put('/formtype/{id}', [FormTypeController::class, 'update'])
        ->name('formtype.update');
    Route::delete('/formtype/{id}', [FormTypeController::class, 'destroy'])
        ->name('formtype.destroy');

// Questation Type
    Route::get('questtype', [QuestionTypeController::class, 'index'])
        ->name('admin.questtype');
    Route::post('/questtype/store', [QuestionTypeController::class, 'store'])
        ->name('questtype.store');
    Route::get('/export-questtype', [QuestionTypeController::class, 'export'])
        ->name('questtype.export');
    Route::post('/questtype-import', [QuestionTypeController::class, 'import'])
        ->name('questtype.import');
    Route::delete('/questtype/bulkDelete', [QuestionTypeController::class, 'bulkDelete'])
        ->name('questtype.bulkDelete');
    Route::get('/questtype/{id}', [QuestionTypeController::class, 'edit'])
        ->name('questtype.edit');
    Route::put('/questtype/{id}', [QuestionTypeController::class, 'update'])
        ->name('questtype.update');
    Route::delete('/questtype/{id}', [QuestionTypeController::class, 'destroy'])
        ->name('questtype.destroy');


// Redirect root (/) to login or dashboard
Route::get('/', function () {
    if (auth()->check()) {
        if (auth()->user()->hasRole('admin')) return redirect()->route('admin.dashboard');
        if (auth()->user()->hasRole('surveyor')) return redirect()->route('surveyor.dashboard');
        if (auth()->user()->hasRole('pm')) return redirect()->route('pm.dashboard');
        return redirect()->route('home');
    }
    return redirect()->route('login');
});

Route::get('/check-email', function (Illuminate\Http\Request $request) {
    $exists = \App\Models\User::where('email', $request->email)->exists();
    return response()->json(['exists' => $exists]);
});
    Route::get('/dropdown', function(){
        $activities = \App\Models\Activity::all();
        return view('example', compact('activities'));
    });
    // Routes AJAX
    Route::get('/get-groups/{activityID}', [GroupController::class, 'getGroups'])->name('get-groups');
    Route::get('/get-units/{groupID}', [UnitController::class, 'getUnits'])->name('get-units');
