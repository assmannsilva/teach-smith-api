<?php

use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\Users\Invites\InviteStudentsController;
use App\Http\Controllers\Users\Invites\InviteTeachersController;
use Illuminate\Support\Facades\Route;

Route::post('create-organization', [OrganizationController::class, 'store'])->name('organizations.store');

Route::middleware("auth:sanctum")->get('/me', function () {
    return response()->json(auth()->user());
})->name('auth.user');

Route::middleware("auth:sanctum")->prefix('teachers')->group(function () {
    Route::post('invite',[InviteTeachersController::class, "store"])->name('teachers.invite');
    Route::post('bulk-invite',[InviteTeachersController::class, "import"])->name('teachers.bulk-invite');
});

Route::middleware("auth:sanctum")->prefix('students')->group(function () {
    Route::post('invite',[InviteStudentsController::class, "store"])->name('students.invite');
    Route::post('bulk-invite',[InviteStudentsController::class, "import"])->name('students.bulk-invite');
});


