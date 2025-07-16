<?php

use App\Http\Controllers\OrganizationController;
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
});

Route::middleware("auth:sanctum")->prefix('students')->group(function () {
    Route::post('invite',[InviteStudentsController::class, "store"])->name('students.invite');
    Route::post('bulk-invite',[InviteStudentsController::class, "import"])->name('students.bulk-invite');
});


