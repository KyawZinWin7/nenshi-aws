<?php


use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\AuthenticateController;
use App\Http\Controllers\MachineTypeController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\TaskController;





Route::middleware('guest')->group(function(){
    //----------------Register----------------
    Route::get('/register', [RegisterController::class,'create'])->name('register');
    Route::post('/register', [RegisterController::class,'store']);



    //----------------Login----------------

    Route::get('/login',[AuthenticateController::class,'create'])->name('login');
    Route::post('/login',[AuthenticateController::class,'store']);
});

Route::middleware('auth')->group(function(){
    //----------------Logout----------------

    Route::post('/logout',[AuthenticateController::class,'destory'])->name('logout');
    Route::resource('machinetypes',MachineTypeController::class);
    Route::resource('employees',EmployeeController::class);
    Route::resource('tasks',TaskController::class);


});