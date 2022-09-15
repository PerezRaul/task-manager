<?php

use App\Http\Controllers\Tasks\TaskDeleteController;
use App\Http\Controllers\Tasks\TaskGetController;
use App\Http\Controllers\Tasks\TaskPutController;
use App\Http\Controllers\Tasks\TasksGetController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::name('tasks')->prefix('tasks')->group(function () {
    Route::get('/', TasksGetController::class);

    Route::name('.task')->prefix('{taskId}')->group(function () {
        Route::get('/', TaskGetController::class);

        Route::put('/', TaskPutController::class);

        Route::delete('/', TaskDeleteController::class);
    });
});
