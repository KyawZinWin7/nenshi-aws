<?php

use App\Http\Controllers\Auth\AuthenticateController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\MachineNumberController;
use App\Http\Controllers\MachineTypeController;
use App\Http\Controllers\MainOperationController;
use App\Http\Controllers\PlantController;
use App\Http\Controllers\SizingOperationController;
use App\Http\Controllers\SmallTaskController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\SizingLogController;
use Illuminate\Support\Facades\Route;

Route::get('/machines/by-type', [MainOperationController::class, 'getMachineNumbersByType']);
Route::get('/tasks/by-machine-type', [TaskController::class, 'getTasksByMachineType']);
Route::get('/machines/by-plant/{plant}', [MainOperationController::class, 'getMachinesByPlant']);
Route::get('/sizingoperations', [SizingOperationController::class, 'index'])->name('sizingoperation');
Route::post('/sizingoperations', [SizingOperationController::class, 'store'])->name('sizingoperations.store');
Route::post('/{id}/sizingcomplete', [SizingOperationController::class, 'complete'])->name('sizingoperations.complete');
Route::get('/sizingcompletelist', [SizingOperationController::class, 'completelist'])->name('sizingoperations.completelist');
Route::put('/sizingoperations/{sizingoperations}', [SizingOperationController::class, 'update'])->name('sizingoperations.update');
Route::post(
    '/sizingoperations/{id}/add-employees',
    [SizingOperationController::class, 'addEmployees']
)->name('sizingoperations.addEmployees');


Route::post(
    '/sizingoperations/{operation}/stop',
    [SizingOperationController::class, 'stop']
)->name('sizingoperations.stop');


Route::post(
    '/sizingoperations/{operation}/stop',
    [SizingOperationController::class, 'stop']
)->name('sizingoperations.stop');

Route::post(
    '/sizingoperations/{operation}/resume',
    [SizingOperationController::class, 'resume']
)->name('sizingoperations.resume');


Route::post(
    '/sizing-logs/{log}/complete',
    [SizingLogController::class, 'complete']
)->name('sizinglogs.complete');


Route::middleware('guest')->group(function () {
    //----------------Register----------------
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);

    Route::get('/', [AuthenticateController::class, 'create'])->name('login');
    Route::post('/', [AuthenticateController::class, 'store']);

});

Route::middleware('auth')->group(function () {
    //----------------Logout---------------- and authenticated

    Route::post('/logout', [AuthenticateController::class, 'destory'])->name('logout');
    Route::get('/mainoperations', [MainOperationController::class, 'index'])->name('home');
    Route::post('/mainoperations', [MainOperationController::class, 'store'])->name('mainoperations.store');
    Route::put('/mainoperations/{mainoperation}', [MainOperationController::class, 'update'])->name('mainoperations.update');
    Route::post('/{id}/complete', [MainOperationController::class, 'complete'])->name('mainoperations.complete');
    Route::get('/completelist', [MainOperationController::class, 'completelist'])->name('mainoperations.completelist');
    Route::post('/{id}/uncomplete', [MainOperationController::class, 'uncomplete'])->name('mainoperations.uncomplete');
    // Route::get('/exportstore', [MainOperationController::class, 'exportstore'])->name('mainoperations.exportstore');

    Route::post('/exportstore', [MainOperationController::class, 'exportStore'])->name('mainoperations.exportstore');

    Route::get('/smalltasks/by-machine-type', [SmallTaskController::class, 'getSmallTasksByMachineType']);
    Route::delete('/{id}', [MainOperationController::class, 'destroy'])->name('mainoperations.destroy');

    //----------------Admin Routes----------------
    Route::middleware('admin')->group(function () {
        Route::resource('plants', PlantController::class);
        Route::resource('departments', DepartmentController::class);
        Route::resource('employees', EmployeeController::class);
        Route::resource('machinetypes', MachineTypeController::class);
        Route::resource('tasks', TaskController::class)->except(['show']);
        Route::resource('smalltasks', SmallTaskController::class);
        Route::resource('machinenumbers', MachineNumberController::class);
        Route::get('/export', [MainOperationController::class, 'export'])->name('mainoperations.export');
        Route::get('/admincompletelist', [MainOperationController::class, 'admincompletelist'])->name('mainoperations.admincompletelist');
    });

});
