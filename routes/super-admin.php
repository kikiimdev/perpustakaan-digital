<?php

use App\Http\Controllers\SuperAdmin\AdminController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'peran:super_admin'])->prefix('super-admin')->name('super-admin.')->group(function () {
    Route::resource('kelola-admin', AdminController::class)->except(['show'])->parameters(['kelola-admin' => 'user']);
});
