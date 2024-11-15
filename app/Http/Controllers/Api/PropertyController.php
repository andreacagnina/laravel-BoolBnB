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

        // Costruzione query con filtri dinamici
        $propertiesQuery = Property::with('images', 'services')
            ->when($request->type, fn($query) => $query->where('type', $request->type))
            ->when($request->search, function($query) use ($request) {
                if (!$request->filled(['latitude', 'longitude'])) {
                    $searchTerm = $request->search;
                    $query->where(function($q) use ($searchTerm) {
                        $q->where('title', 'like', "%{$searchTerm}%")
                          ->orWhere('address', 'like', "%{$searchTerm}%");
                    });
                }
            })
            ->when($request->num_rooms, fn($query) => $query->where('num_rooms', '>=', $request->num_rooms))
            ->when($request->num_beds, fn($query) => $query->where('num_beds', '>=', $request->num_beds))
            ->when($request->num_baths, fn($query) => $query->where('num_baths', '>=', $request->num_baths))
            ->when($request->mq, fn($query) => $query->where('mq', '>=', $request->mq))
            ->when($request->price, fn($query) => $query->where('price', '<=', $request->price))
            ->when($request->filled('selectedServices') && is_array($request->selectedServices), function ($query) use ($request) {
                foreach ($request->selectedServices as $serviceId) {
                    $query->whereHas('services', fn($q) => $q->where('services.id', $serviceId));
                }
            })
            ->orderByDesc('sponsored');

        // Filtro per distanza (latitudine, longitudine e raggio)
        if ($request->filled(['latitude', 'longitude', 'radius'])) {
            $latitude = $request->latitude;
            $longitude = $request->longitude;
            $radius = $request->radius;

            $propertiesQuery->selectRaw(
                "*, (6371 * acos(cos(radians(?)) * cos(radians(lat)) * cos(radians(`long`) - radians(?)) + sin(radians(?)) * sin(radians(lat)))) AS distance",
                [$latitude, $longitude, $latitude]
            )
            ->having('distance', '<=', $radius)
            ->orderBy('distance', 'asc');
        }

        // Clona la query per calcolare minimi e massimi
        $propertiesForMinMax = clone $propertiesQuery;
        $properties = $propertiesQuery->paginate(24);

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

        return response()->json([
            'success' => true,
            'results' => $properties,
            'types' => $types,
            'minMaxValues' => $minMaxValues,
        ]);
    }

    // Metodo per autocompletamento
    public function autocomplete(Request $request)
    {
        $query = $request->input('query', '');

        if (strlen($query) > 2) {
            $suggestions = Property::where('title', 'like', "%{$query}%")
                ->orWhere('address', 'like', "%{$query}%")
                ->limit(10)
                ->pluck('title');

            return response()->json([
                'success' => true,
                'suggestions' => $suggestions,
            ]);
        }

        return response()->json(['success' => false, 'suggestions' => []]);
    }

    public function show($slug)
    {
        $property = Property::with('images', 'services')
            ->where('slug', $slug)
            ->first();

        if ($property) {
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

            $isFavorite = Favorite::where('property_id', $property->id)
                ->where('ip_address', request()->ip())
                ->exists();

            return response()->json([
                'success' => true,
                'results' => $property->toArray() + [
                    'is_favorite' => $isFavorite,
                    'view_count' => View::where('property_id', $property->id)->count(),
                ]
            ]);
        }

        return response()->json(['success' => false]);
    }

    public function toggleFavorite(Request $request, Property $property)
    {
        $ip = $request->ip();
        $favorite = Favorite::where('property_id', $property->id)
            ->where('ip_address', $ip)
            ->first();

        if ($favorite) {
            $favorite->delete();
            return response()->json(['success' => true, 'favorited' => false]);
        } else {
            Favorite::create([
                'property_id' => $property->id,
                'ip_address' => $ip
            ]);
            return response()->json(['success' => true, 'favorited' => true]);
        }
    }
}
