<?php

use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\Users\Invites\InviteStudentsController;
use App\Http\Controllers\Users\Invites\InviteTeachersController;
use App\Http\Controllers\Users\ProfileController;
use Illuminate\Support\Facades\Route;

Route::post('create-organization', [OrganizationController::class, 'store'])->name('organizations.store');


Route::middleware("auth:sanctum")->prefix('profile')->controller(ProfileController::class)->group(function () {
    Route::get('/', 'show')->name('profile.show');
    Route::delete('/', 'destroy')->name('profile.logout');
});

Route::middleware("auth:sanctum")->prefix('teachers')->group(function () {
    Route::post('invite',[InviteTeachersController::class, "store"])->name('teachers.invite');
    Route::post('bulk-invite',[InviteTeachersController::class, "import"])->name('teachers.bulk-invite');
    Route::get('search', [\App\Http\Controllers\TeacherController::class, 'search'])->name('teachers.search');
    //Route::get('', [\App\Http\Controllers\TeacherController::class, 'index'])->name('teachers.index');
});

Route::middleware("auth:sanctum")->prefix('students')->group(function () {
    Route::post('invite',[InviteStudentsController::class, "store"])->name('students.invite');
    Route::post('bulk-invite',[InviteStudentsController::class, "import"])->name('students.bulk-invite');
});

Route::middleware("auth:sanctum")->prefix('classrooms')->controller(ClassroomController::class)->group(function () {
    Route::get('/', 'index')->name('classrooms.index');
    Route::post('/', 'store')->name('classrooms.store');
    Route::get('/{id}', 'show')->name('classrooms.show');
    Route::put('/{id}', 'update')->name('classrooms.update');
    Route::delete('/{id}', 'destroy')->name('classrooms.destroy');
});

Route::middleware("auth:sanctum")->prefix('subjects')->controller(SubjectController::class)->group(function () {
    Route::get('/', 'index')->name('subjects.index');
    Route::post('/', 'store')->name('subjects.store');
    Route::get('/{id}', 'show')->name('subjects.show');
    Route::put('/{id}', 'update')->name('subjects.update');
    Route::delete('/{id}', 'destroy')->name('subjects.destroy');
});


