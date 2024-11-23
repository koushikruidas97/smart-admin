<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MasterController;
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

Route::get('/admin-login', [MasterController::class, 'login']);
Route::post('/login-submit', [MasterController::class, 'adminlogin']);
Route::middleware('admin')->group(function () {
    Route::get('/dashboard', [MasterController::class, 'dashboard']);
    Route::get('/logout', [MasterController::class, 'logout']);
    Route::post('/update', [MasterController::class, 'update']);
    Route::post('/delete', [MasterController::class, 'delete']);
    Route::post('/upload-image', [MasterController::class, 'uploadImage']);
    Route::post('/update-nested-menu-order', [MasterController::class, 'updateNestedOrder']);
    Route::get('/menu', [MasterController::class, 'showMenu']);
    Route::get('/{pagefrom}', [MasterController::class, 'fetch']);
});
