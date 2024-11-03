<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Property;

class PropertyController extends Controller
{
    public function index()
    {
        $properties = Property::where('available', 1)
                            ->orderByDesc('sponsored')
                            ->get();

        return view('guest.properties.index', compact('properties'));
    }

    public function show($slug)
    {
        $property = Property::where('slug', $slug)
                            ->where('available', 1)
                            ->firstOrFail();

        return view('guest.properties.show', compact('property'));
    }
}
