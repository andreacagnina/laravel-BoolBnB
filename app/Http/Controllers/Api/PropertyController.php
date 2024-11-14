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
    
        // Inizializza la query di ricerca delle proprietà
        $propertiesQuery = Property::with('images', 'services')
            ->when($request->type, function ($query) use ($request) {
                return $query->where('type', $request->type); // Filtro per tipo di proprietà
            })
            ->when($request->search, function ($query) use ($request) {
                $searchTerm = $request->search;
                return $query->where('title', 'like', "%{$searchTerm}%")
                    ->orWhere('address', 'like', "%{$searchTerm}%"); // Filtro per termini di ricerca
            })
            // Filtro per numero di stanze (>=)
            ->when($request->num_rooms, function ($query) use ($request) {
                return $query->where('num_rooms', '>=', $request->num_rooms);
            })
            // Filtro per numero di letti (>=)
            ->when($request->num_beds, function ($query) use ($request) {
                return $query->where('num_beds', '>=', $request->num_beds);
            })
            // Filtro per numero di bagni (>=)
            ->when($request->num_baths, function ($query) use ($request) {
                return $query->where('num_baths', '>=', $request->num_baths);
            })
            // Filtro per metri quadrati (>=)
            ->when($request->mq, function ($query) use ($request) {
                return $query->where('mq', '>=', $request->mq);
            })
            // Filtro per prezzo (<=)
            ->when($request->price, function ($query) use ($request) {
                return $query->where('price', '<=', $request->price);
            })
            // Filtro per servizi selezionati (richiede che la proprietà abbia tutti i servizi selezionati)
            ->when($request->filled('selectedServices') && is_array($request->selectedServices), function ($query) use ($request) {
                foreach ($request->selectedServices as $serviceId) {
                    $query->whereHas('services', function ($q) use ($serviceId) {
                        $q->where('services.id', $serviceId);
                    });
                }
            })
            ->orderByDesc('sponsored'); // Ordina per sponsor

        // Clona la query per calcolare i valori min e max sui risultati filtrati
        $propertiesForMinMax = clone $propertiesQuery;

        // Esegui la paginazione sui risultati della query
        $properties = $propertiesQuery->paginate(24);

        // Calcola i valori min e max, impostando valori predefiniti in caso di assenza di dati
        $minMaxValues = [
            'min_rooms' => $propertiesForMinMax->min('num_rooms') ?? 1,
            'max_rooms' => $propertiesForMinMax->max('num_rooms') ?? 10,
            'min_beds' => $propertiesForMinMax->min('num_beds') ?? 1,
            'max_beds' => $propertiesForMinMax->max('num_beds') ?? 10,
            'min_baths' => $propertiesForMinMax->min('num_baths') ?? 1,
            'max_baths' => $propertiesForMinMax->max('num_baths') ?? 5,
            'min_mq' => $propertiesForMinMax->min('mq') ?? 20,
            'max_mq' => $propertiesForMinMax->max('mq') ?? 500,
            'min_price' => $propertiesForMinMax->min('price') ?? 50,
            'max_price' => $propertiesForMinMax->max('price') ?? 1000,
        ];

        // Restituisci la risposta in formato JSON
        return response()->json([
            'success' => true,
            'results' => $properties,
            'types' => $types,
            'minMaxValues' => $minMaxValues,
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
