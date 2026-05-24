<?php

use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

require __DIR__.'/settings.php';
require __DIR__.'/super-admin.php';
require __DIR__.'/admin.php';
require __DIR__.'/app.php';
