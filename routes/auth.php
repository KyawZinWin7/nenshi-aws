<?php


use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\AuthenticateController;
use App\Http\Controllers\MachineTypeController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\PlantController;
use App\Http\Controllers\MachineNumberController;
use App\Http\Controllers\MainOperationController;






Route::middleware('guest')->group(function(){
    //----------------Register----------------
    Route::get('/register', [RegisterController::class,'create'])->name('register');
    Route::post('/register', [RegisterController::class,'store']);



    //----------------Login----------------

    Route::get('/',[AuthenticateController::class,'create'])->name('login');
    Route::post('/',[AuthenticateController::class,'store']);
});

Route::middleware('auth')->group(function(){
    //----------------Logout---------------- and authenticated 

    Route::post('/logout',[AuthenticateController::class,'destory'])->name('logout');
    Route::get('/mainoperations', [MainOperationController::class, 'index'])->name('home');
    Route::post('/mainoperations', [MainOperationController::class, 'store'])->name('mainoperations.store');
    Route::post('/{id}/complete', [MainOperationController::class, 'complete'])->name('mainoperations.complete');
    Route::get('/completelist', [MainOperationController::class, 'completelist'])->name('mainoperations.completelist');
    Route::post('/{id}/uncomplete', [MainOperationController::class, 'uncomplete'])->name('mainoperations.uncomplete');
    // Route::get('/exportstore', [MainOperationController::class, 'exportstore'])->name('mainoperations.exportstore');


    Route::post('/exportstore', [MainOperationController::class, 'exportStore'])->name('mainoperations.exportstore');


    
    Route::get('/machines/by-plant/{plant}', [MainOperationController::class, 'getMachinesByPlant']);
    Route::get('/machines/by-type', [MainOperationController::class, 'getMachineNumbersByType']);
    Route::get('/tasks/by-machine-type', [TaskController::class, 'getTasksByMachineType']);
    Route::delete('/{id}', [MainOperationController::class, 'destroy'])->name('mainoperations.destroy');


    //----------------Admin Routes----------------
    Route::middleware('admin')->group(function(){
        Route::resource('plants',PlantController::class);
        Route::resource('employees',EmployeeController::class);
        Route::resource('machinetypes',MachineTypeController::class);
        Route::resource('tasks', TaskController::class)->except(['show']);
        Route::resource('machinenumbers',MachineNumberController::class);
        Route::get('/export', [MainOperationController::class, 'export'])->name('mainoperations.export');
        Route::get('/admincompletelist', [MainOperationController::class, 'admincompletelist'])->name('mainoperations.admincompletelist');
    });



    


   



});