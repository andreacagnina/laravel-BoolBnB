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
        // Validazione degli input
        $validated = $request->validate([
            'type' => 'nullable|string',
            'search' => 'nullable|string',
            'num_rooms' => 'nullable|integer|min:1',
            'num_beds' => 'nullable|integer|min:1',
            'num_baths' => 'nullable|integer|min:1',
            'mq' => 'nullable|integer|min:20',
            'price' => 'nullable|numeric|min:0',
            'selectedServices' => 'nullable|array',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'radius' => 'nullable|numeric|min:1|max:200',
        ]);

        $types = Property::select('type')->distinct()->get();

        // Costruzione della query dinamica
        $propertiesQuery = Property::with('images', 'services')
            ->when($request->type, fn($query) => $query->where('type', $request->type))
            ->when($request->search, function ($query) use ($request) {
                if (!$request->filled(['latitude', 'longitude'])) {
                    $searchTerm = $request->search;
                    $query->where(function ($q) use ($searchTerm) {
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

        // Filtro per distanza
        if ($request->filled(['latitude', 'longitude', 'radius'])) {
            $latitude = $request->latitude;
            $longitude = $request->longitude;
            $radius = $request->radius;

            $propertiesQuery->select('properties.*')->selectRaw(
                "(6371 * acos(cos(radians(?)) * cos(radians(properties.lat)) * cos(radians(properties.long) - radians(?)) + sin(radians(?)) * sin(radians(properties.lat)))) AS distance",
                [$latitude, $longitude, $latitude]
            )
            ->having('distance', '<=', $radius)
            ->orderBy('distance', 'asc');
        }

        // Clonazione della query per calcoli di min/max
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
            'message' => 'Properties retrieved successfully.',
            'results' => $properties,
            'types' => $types,
            'minMaxValues' => $minMaxValues,
        ]);
    }

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
                'message' => 'Suggestions retrieved successfully.',
                'suggestions' => $suggestions,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Query too short.',
            'suggestions' => []
        ]);
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
                'message' => 'Property retrieved successfully.',
                'results' => $property->toArray() + [
                    'is_favorite' => $isFavorite,
                    'view_count' => View::where('property_id', $property->id)->count(),
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Property not found.'
        ]);
    }

    public function toggleFavorite(Request $request, Property $property)
    {
        $ip = $request->ip();

        $favorite = Favorite::updateOrCreate(
            ['property_id' => $property->id, 'ip_address' => $ip],
            []
        );

        $wasFavorited = $favorite->wasRecentlyCreated;

        if (!$wasFavorited) {
            $favorite->delete();
        }

        return response()->json([
            'success' => true,
            'favorited' => $wasFavorited
        ]);
    }
}
