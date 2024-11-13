<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ViewController extends Controller
{
    /**
     * Aggiunge una proprietà ai preferiti dell'utente autenticato.
     */
    public function addToFavorites(Request $request)
    {
        Log::info('Richiesta ricevuta:', $request->all());

        $request->validate([
            'property_id' => 'required|exists:properties,id',
        ]);

        $propertyId = $request->property_id;

        // Genera la chiave di cache per i preferiti
        $cacheKey = $this->getFavoriteCacheKey($propertyId);

        // Controlla se la proprietà è già nei preferiti
        if (Cache::has($cacheKey)) {
            return response()->json([
                'success' => false,
                'message' => 'Property is already in favorites.',
            ], 400);
        }

        // Aggiungi la proprietà ai preferiti per 1 giorno
        Cache::put($cacheKey, true, now()->addDay());

        return response()->json([
            'success' => true,
            'message' => 'Property added to favorites.',
        ]);
    }

    /**
     * Restituisce la chiave di cache per una proprietà e un utente.
     */
    private function getFavoriteCacheKey($propertyId)
    {
        return "user_" . auth()->id() . "_property_{$propertyId}_favorite";
    }
}
