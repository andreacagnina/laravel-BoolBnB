<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $latitude = $request->query('latitude');
        $longitude = $request->query('longitude');
        $radius = $request->query('radius', 20);
        $minRooms = $request->query('rooms', 1);
        $minBeds = $request->query('beds', 1);
        $selectedServices = $request->query('services', []);

        // Fetch all services to display in the view
        $services = Service::all();

        // Check if latitude and longitude are provided
        if ($latitude && $longitude) {
            $properties = Property::selectRaw(
                "properties.*, (6371 * acos(cos(radians(?)) * cos(radians(lat)) * cos(radians(`long`) - radians(?)) + sin(radians(?)) * sin(radians(lat)))) AS distance",
                [$latitude, $longitude, $latitude]
            )
                ->having('distance', '<=', $radius)
                ->where('available', 1)
                ->where('num_rooms', '>=', $minRooms)
                ->where('num_beds', '>=', $minBeds);

            // Apply service filters if any
            if (!empty($selectedServices)) {
                $properties = $properties->whereHas('services', function ($query) use ($selectedServices) {
                    $query->whereIn('services.id', $selectedServices);
                }, '=', count($selectedServices));
            }

            // Ensure sponsored properties come first, then order by distance
            $properties = $properties->orderBy('sponsored', 'desc')
                ->orderBy('distance', 'asc')
                ->get();
        } else {
            // If no coordinates provided, fetch all available properties
            $properties = Property::where('available', 1)
                ->where('num_rooms', '>=', $minRooms)
                ->where('num_beds', '>=', $minBeds);

            // Apply service filters if any
            if (!empty($selectedServices)) {
                $properties = $properties->whereHas('services', function ($query) use ($selectedServices) {
                    $query->whereIn('services.id', $selectedServices);
                }, '=', count($selectedServices));
            }

            // Ensure sponsored properties come first
            $properties = $properties->orderBy('sponsored', 'desc')->get();
        }

        // Add full cover image URL and round distance
        $properties->map(function ($property) {
            $property->cover_image_url = Str::startsWith($property->cover_image, 'http') ? $property->cover_image : asset('storage/' . $property->cover_image);
            if (isset($property->distance)) {
                $property->distance = round($property->distance, 2);
            }
            return $property;
        });

        // If the request is AJAX, return JSON response
        if ($request->ajax()) {
            return response()->json(['properties' => $properties]);
        }

        // Return the view with properties and services
        return view('guest.homepage', compact('properties', 'services'));
    }

    public function show($slug)
    {
        $property = Property::where('slug', $slug)
            ->where('available', 1)
            ->firstOrFail();

        return view('guest.properties.show', compact('property'));
    }
}
