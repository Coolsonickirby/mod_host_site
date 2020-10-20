<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers;
use App\Http\Controllers\MainController;

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

Route::get('/', [MainController::class, 'showItems']);

Route::get('/item/{id}', [MainController::class, 'showItem']);

Route::get('/get_toml/{id}', [MainController::class, 'generateUserTOML']);

Route::get('/logout', [MainController::class, 'logout']);

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', [MainController::class, "ownedItems"])->name('dashboard');

Route::middleware(['auth:sanctum', 'verified'])->get('/upload', function () {
    return view('upload');
})->name('upload');

Route::post('/upload/submit', [MainController::class, 'SubmitItem']);
