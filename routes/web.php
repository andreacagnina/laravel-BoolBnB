<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\PropertyController as PropertyController;
use App\Http\Controllers\HomePageController;
use App\Http\Controllers\Admin\SponsorController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\ViewController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [HomePageController::class, 'index'])->name('homepage');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->name('admin.')->prefix('admin')->group(function () {
    Route::resource('properties', PropertyController::class);
    Route::resource('sponsors', SponsorController::class);
    Route::resource('services', ServiceController::class);
    Route::get('/views/{property}', [ViewController::class, 'show'])->name('views.show');
});

require __DIR__ . '/auth.php';
