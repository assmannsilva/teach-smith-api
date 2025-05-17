<?php

use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\Users\RegisterAdminUserController;
use Illuminate\Support\Facades\Route;

Route::post('create-organization', [OrganizationController::class, 'store'])->name('organizations.store');
Route::post('register-admin',RegisterAdminUserController::class)->name("register-admin");

Route::middleware(['auth', 'verified'])->group(function () {

    

});
