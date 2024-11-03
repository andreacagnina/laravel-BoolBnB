<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\PropertyController as AdminPropertyController;
use App\Http\Controllers\Guest\PropertyController as GuestPropertyController;
use App\Http\Controllers\Guest\HomeController;
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

// Route per la home page utilizzando HomeController in Guest
Route::get('/', [HomeController::class, 'index'])->name('homepage');

// Route per la lista delle proprietà disponibili, utilizzando il PropertyController di Guest
Route::get('/properties', [GuestPropertyController::class, 'index'])->name('properties.index');

// Route per visualizzare i dettagli di una singola proprietà, utilizzando il PropertyController di Guest
Route::get('/properties/{slug}', [GuestPropertyController::class, 'show'])->name('properties.show');

// Route per la dashboard, accessibile solo agli utenti autenticati e verificati
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Route per l'area amministrativa con prefisso e middleware
Route::middleware(['auth', 'verified'])->name('admin.')->prefix('admin')->group(function () {
    Route::resource('properties', AdminPropertyController::class)->parameters([
        'properties' => 'property:slug'
    ]);
    Route::resource('sponsors', SponsorController::class);
    Route::resource('services', ServiceController::class);
    Route::get('/views/{property:slug}', [ViewController::class, 'show'])->name('views.show');
    Route::post('/properties/assign-sponsor', [AdminPropertyController::class, 'assignSponsor'])->name('properties.assignSponsor');

    // Rotta per la pagina show di uno sponsor per un appartamento specifico
    Route::get('/sponsors/{property:slug}/show', [SponsorController::class, 'show'])->name('sponsors.show');
});

require __DIR__ . '/auth.php';
