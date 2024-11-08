<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\Image;
use App\Models\Service;

class PropertyController extends Controller
{
    public function index(Request $request)
    {   
        $types = Property::select('type')->distinct()->get();

        // Inizializza la query
        $properties = Property::with('images', 'services')
            ->when($request->type, function($query) use ($request) {
                return $query->where('type', $request->type); // type
            })
            ->when($request->search, function($query) use ($request) {
                $searchTerm = $request->search;
                return $query->where('title', 'like', "%{$searchTerm}%")
                             ->orWhere('address', 'like', "%{$searchTerm}%"); // searchbar
            })
            // Filtro per numero di stanze
            ->when($request->num_rooms, function($query) use ($request) {
                if ($request->num_rooms == 7) {
                    return $query->where('num_rooms', '>=', 7); // 7 or more rooms
                }
                return $query->where('num_rooms', $request->num_rooms);
            })
            ->orderByDesc('sponsored') // sponsored first
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
        // Trova la proprietà con il 'slug'
        $property = Property::with('images', 'services')->where('slug', $slug)->first();
        if ($property) {
            return response()->json([
                'success' => true,
                'results' => $property
            ]);
        }
        return response()->json([
            'success' => false
        ]);
    }
}
