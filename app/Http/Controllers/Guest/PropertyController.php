<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Property;

class PropertyController extends Controller
{
    public function show($slug)
    {
        $property = Property::where('slug', $slug)
            ->where('available', 1)
            ->firstOrFail();

        return view('guest.properties.show', compact('property'));
    }
}
