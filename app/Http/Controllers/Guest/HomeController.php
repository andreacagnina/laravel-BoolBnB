<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Property;

class HomeController extends Controller
{
    public function index()
    {
        $properties = Property::where('available', 1)
            ->orderByDesc('sponsored')
            ->get();

        foreach ($properties as $property) {
            $property->checkSponsorshipStatus();
        }

        return view('guest.homepage', compact('properties'));
    }
}
