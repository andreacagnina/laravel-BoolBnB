<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Models\Favorite;

class ViewController extends Controller
{
    /**
     * Aggiunge una proprietà ai preferiti dell'utente autenticato.
     */
    public function toggleFavorite(Request $request, $propertyId)
    {
        $ipAddress = $request->ip();

        // Cerca se l'IP ha già aggiunto la proprietà ai preferiti
        $favorite = Favorite::where('property_id', $propertyId)
            ->where('ip_address', $ipAddress)
            ->first();

        if ($favorite) {
            // Se esiste, rimuovilo
            $favorite->delete();
            return response()->json(['success' => true, 'isFavorite' => false]);
        } else {
            // Altrimenti, aggiungilo ai preferiti
            Favorite::create([
                'property_id' => $propertyId,
                'ip_address' => $ipAddress
            ]);
            return response()->json(['success' => true, 'isFavorite' => true]);
        }
    }
}
