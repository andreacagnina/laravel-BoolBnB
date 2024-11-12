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
                return $query->where('num_rooms', $request->num_rooms);
            })
            // Filtro per numero di letti
            ->when($request->num_beds, function($query) use ($request) {
                return $query->where('num_beds', $request->num_beds);
            })
            // Filtro per numero di bagni
            ->when($request->num_baths, function($query) use ($request) {
                return $query->where('num_baths', $request->num_baths);
            })
            ->when($request->mq, function($query) use ($request) {
                return $query->where('mq', '<=', $request->mq);
            })
            ->when($request->price, function($query) use ($request) {
                return $query->where('price', '<=', $request->price);
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