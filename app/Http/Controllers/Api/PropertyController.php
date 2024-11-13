<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\Service;
use App\Models\View;
use App\Models\Favorite;

class PropertyController extends Controller
{
    public function index(Request $request)
    {
        $types = Property::select('type')->distinct()->get();

        // Inizializza la query
        $properties = Property::with('images', 'services')
            ->when($request->type, function ($query) use ($request) {
                return $query->where('type', $request->type); // type
            })
            ->when($request->search, function ($query) use ($request) {
                $searchTerm = $request->search;
                return $query->where('title', 'like', "%{$searchTerm}%")
                    ->orWhere('address', 'like', "%{$searchTerm}%"); // searchbar
            })
            // Filtro per numero di stanze
            ->when($request->num_rooms, function ($query) use ($request) {
                return $query->where('num_rooms', $request->num_rooms);
            })
            // Filtro per numero di letti
            ->when($request->num_beds, function ($query) use ($request) {
                return $query->where('num_beds', $request->num_beds);
            })
            // Filtro per numero di bagni
            ->when($request->num_baths, function ($query) use ($request) {
                return $query->where('num_baths', $request->num_baths);
            })
            // Filtro per metri quadrati
            ->when($request->mq, function ($query) use ($request) {
                return $query->where('mq', '<=', $request->mq);
            })
            // Filtro per prezzo
            ->when($request->price, function ($query) use ($request) {
                return $query->where('price', '<=', $request->price);
            })
            // Filtro per servizi selezionati
            ->when($request->filled('selectedServices') && is_array($request->selectedServices), function ($query) use ($request) {
                return $query->whereHas('services', function ($query) use ($request) {
                    $query->whereIn('services.id', $request->selectedServices);  // Filtra per servizi selezionati
                });
            })
            ->orderByDesc('sponsored') // Ordina per sponsor
            ->paginate(24);

        // Restituisci la risposta in formato JSON
        return response()->json([
            'success' => true,
            'results' => $properties,
            'types' => $types,
        ]);
    }

    public function show($slug)
    {
        $property = Property::with('images', 'services')
            ->where('slug', $slug)
            ->first();

        if ($property) {
            // Registra la visualizzazione solo se l'IP non ha già visto questa proprietà oggi
            $existingView = View::where('property_id', $property->id)
                ->where('ip_address', request()->ip())
                ->whereDate('created_at', today())
                ->exists();

            if (!$existingView) {
                View::create([
                    'property_id' => $property->id,
                    'ip_address' => request()->ip(),
                ]);
            }

            // Verifica se è tra i preferiti
            $isFavorite = Favorite::where('property_id', $property->id)
                ->where('ip_address', request()->ip())
                ->exists();

            return response()->json([
                'success' => true,
                'results' => $property->toArray() + [
                    'is_favorite' => $isFavorite,
                    'view_count' => View::where('property_id', $property->id)->count(), // Numero di visualizzazioni
                ]
            ]);
        }

        return response()->json(['success' => false]);
    }
    public function toggleFavorite(Request $request, Property $property)
    {
        $ip = $request->ip();

        // Controlla se l'IP ha già aggiunto la proprietà ai preferiti
        $favorite = Favorite::where('property_id', $property->id)
            ->where('ip_address', $ip)
            ->first();

        if ($favorite) {
            // Se esiste, rimuovilo
            $favorite->delete();
            return response()->json(['success' => true, 'favorited' => false]);
        } else {
            // Altrimenti, aggiungi ai preferiti
            Favorite::create([
                'property_id' => $property->id,
                'ip_address' => $ip
            ]);
            return response()->json(['success' => true, 'favorited' => true]);
        }
    }
}
