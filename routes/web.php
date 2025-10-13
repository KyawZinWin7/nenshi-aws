<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\MainOperationController;
// use App\Http\Controllers\Auth\AuthenticateController;

// Route::inertia('/', 'Home')->name('home');


Route::get('/mainoperations', [MainOperationController::class, 'index'])->name('home');
Route::post('/mainoperations', [MainOperationController::class, 'store'])->name('mainoperations.store');
Route::post('/{id}/complete', [MainOperationController::class, 'complete'])->name('mainoperations.complete');
Route::get('/completelist', [MainOperationController::class, 'completelist'])->name('mainoperatons.completelist');
Route::get('/export', [MainOperationController::class, 'export'])->name('mainoperations.export');
Route::post('/{id}/uncomplete', [MainOperationController::class, 'uncomplete'])->name('mainoperations.uncomplete');
Route::get('/exportstore', [MainOperationController::class, 'exportstore'])->name('mainoperations.exportstore');

Route::delete('/{id}', [MainOperationController::class, 'destroy'])->name('mainoperations.destroy');






require __DIR__. '/auth.php';