<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RoleController;
use App\Http\Controllers\DataUserController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\CatagoriesQuestions;

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
        return view('dashboard.user');
    })->name('user.dashboard');
});

Route::controller(DataUserController::class)->group(function () {
    Route::get('/datauser', 'index')->name('admin.datauser');
    Route::get('/export-template-user', [DataUserController::class, 'export'])
        ->name('admin.export.usertemplate');
});

Route::controller(ActivityController::class)->group(function(){
    Route::get('/dataactivity', 'index')
        ->name('admin.activity');
    Route::post('/store-activity', [ActivityController::class, 'store'])
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
    Route::post('/units/storeunit', [UnitController::class, 'store'])
        ->name('units.storegroup');
    Route::delete('/units/bulkDelete', [UnitController::class , 'bulkDelete'])
        ->name('units.bulkDelete');
    Route::get('/export-units', [UnitController::class, 'export'])
        ->name('units.export');
    Route::post('/unit/{id}/import-unit', [UnitController::class, 'import'])
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
    Route::post('/catagoriesquestions/storecatagoriesquestions', [CatagoriesQuestions::class, 'store'])
        ->name('catagoriesquestions.storecatagoriesquestions');
});



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
