<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\Image;
use App\Models\Service;


class PropertyController extends Controller
{
    public function index()
    {
        // Recupera le proprietà con le immagini e i servizi associati
        $properties = Property::with('images', 'services')->paginate(24);
    
        return response()->json([
            'success' => true,
            'results' => $properties,
        ]);
    }
    
    

    public function show($slug)
    {
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
