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

        $properties = Property::with('images', 'services')
            ->when($request->type, function($query) use ($request) {
                return $query->where('type', $request->type);
            })
            ->paginate(24);

        return response()->json([
            'success' => true,
            'results' => $properties,
            'types' => $types,
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