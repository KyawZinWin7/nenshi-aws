<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\MainOperationController;
// use App\Http\Controllers\Auth\AuthenticateController;

// Route::inertia('/', 'Home')->name('home');



// routes/web.php












use Inertia\Inertia;

Route::fallback(function () {
    return Inertia::render('404');
});


require __DIR__. '/auth.php';