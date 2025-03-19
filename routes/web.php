<?php

use App\Http\Controllers\Admin\BraintreeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Guest\HomeController;
use App\Http\Controllers\Admin\PropertyController as AdminPropertyController;
use App\Http\Controllers\Admin\SponsorController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\ViewController;
use App\Http\Controllers\Admin\MessageController;

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

// Route per l'area amministrativa, con prefisso e middleware
Route::middleware(['auth', 'verified'])->name('admin.')->prefix('admin')->group(function () {
    Route::resource('properties', AdminPropertyController::class)->parameters([
        'properties' => 'property:slug'
    ]);

    Route::patch('/properties/{id}/restore', [AdminPropertyController::class, 'restore'])->name('properties.restore');

    Route::resource('sponsors', SponsorController::class);
    Route::resource('services', ServiceController::class);
    Route::get('/views/{property:slug}', [ViewController::class, 'show'])->name('views.show');
    Route::get('/views', [ViewController::class, 'index'])->name('views.index');

    Route::post('/properties/assign-sponsor', [AdminPropertyController::class, 'assignSponsor'])->name('properties.assignSponsor');

    // Rotta per la visualizzazione di uno sponsor specifico per un appartamento, con nome univoco (ex '/sponsors/{property:slug}/show')
    Route::get('/sponsors/{property:slug}/property_show', [SponsorController::class, 'show'])->name('sponsors.property_show');

    Route::get('/braintree/token', [BraintreeController::class, 'token'])->name('braintree.token');
    Route::post('/braintree/checkout', [BraintreeController::class, 'checkout'])->name('braintree.checkout');

    Route::resource('messages', MessageController::class);

    // Rotta per ripristinare un messaggio eliminato
    Route::patch('/messages/{id}/restore', [MessageController::class, 'restore'])->name('messages.restore');

    Route::delete('/messages/{id}/hard-destroy', [MessageController::class, 'hardDestroy'])->name('messages.hardDestroy');
});

require __DIR__ . '/auth.php';
