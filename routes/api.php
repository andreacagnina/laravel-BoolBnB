<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PropertyController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\ViewController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Rotta per ottenere i dettagli dell'utente autenticato
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Rotte per le proprietà
Route::get('/properties', [PropertyController::class, 'index'])->name('properties');
Route::get('/property/{slug}', [PropertyController::class, 'show'])->name('property');

// Rotte per i servizi
Route::get('/services', [ServiceController::class, 'index']);

// Rotte per i messaggi
Route::apiResource('messages', MessageController::class);

// Rotta per la registrazione
Route::post('/register', [AuthController::class, 'register']);

// Rotta per il login
Route::post('/login', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (!Auth::attempt($request->only('email', 'password'))) {
        return response()->json(['message' => 'Credenziali di accesso non valide'], 401);
    }

    $user = Auth::user();
    // Creazione del token usando Laravel Sanctum
    // $token = $user->createToken('authToken')->plainTextToken;

    // return response()->json(['user' => $user, 'token' => $token]);
});

// Rotta per il logout
Route::middleware('auth:sanctum')->post('/logout', function (Request $request) {
    $request->user()->tokens()->delete(); // Revoca tutti i token dell'utente
    return response()->json(['message' => 'Logout effettuato con successo']);
});

Route::post('/properties/{property}/favorite', [PropertyController::class, 'toggleFavorite'])->name('properties.toggleFavorite');
