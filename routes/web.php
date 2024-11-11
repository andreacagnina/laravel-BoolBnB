<?php

use App\Http\Controllers\Admin\BraintreeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Guest\HomeController;
use App\Http\Controllers\Admin\PropertyController as AdminPropertyController;
use App\Http\Controllers\Admin\SponsorController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\ViewController;
use App\Http\Controllers\Admin\MessageController;
use App\Http\Controllers\ProfileController;

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

// Route per la home page, utilizzando HomeController nella sezione Guest
Route::get('/', [HomeController::class, 'index'])->name('homepage');

// Route per la lista delle proprietà disponibili, accettando parametri di ricerca opzionali (supporto AJAX)
Route::get('/properties', [HomeController::class, 'index'])->name('properties.index');

// Route per visualizzare i dettagli di una singola proprietà, utilizzando HomeController in Guest
Route::get('/properties/{slug}', [HomeController::class, 'show'])->name('properties.show');

// Route per la dashboard, accessibile solo agli utenti autenticati e verificati
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Route per l'area amministrativa, con prefisso e middleware
Route::middleware(['auth', 'verified'])->name('admin.')->prefix('admin')->group(function () {
    Route::resource('properties', AdminPropertyController::class)->parameters([
        'properties' => 'property:slug'
    ]);

    Route::resource('sponsors', SponsorController::class);
    Route::resource('services', ServiceController::class);
    Route::get('/views/{property:slug}', [ViewController::class, 'show'])->name('views.show');
    Route::get('/messages', [MessageController::class, 'index']);
    Route::post('/properties/assign-sponsor', [AdminPropertyController::class, 'assignSponsor'])->name('properties.assignSponsor');

    // Rotta per la visualizzazione di uno sponsor specifico per un appartamento
    Route::get('/sponsors/{property:slug}/show', [SponsorController::class, 'show'])->name('sponsors.show');

    Route::get('/braintree/token', [BraintreeController::class, 'token'])->name('braintree.token');
    Route::post('/braintree/checkout', [BraintreeController::class, 'checkout'])->name('braintree.checkout');
});

require __DIR__ . '/auth.php';
