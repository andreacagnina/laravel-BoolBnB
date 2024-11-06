<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $latitude = $request->query('latitude');
        $longitude = $request->query('longitude');
        $radius = 20; // Raggio di ricerca in km

        if ($latitude && $longitude) {
            // Calcolo della variazione per il bounding box
            $latDelta = $radius / 111; // 1 grado di latitudine è circa 111 km
            $longDelta = $radius / (111 * cos(deg2rad($latitude)));

            // Limiti del bounding box
            $minLat = $latitude - $latDelta;
            $maxLat = $latitude + $latDelta;
            $minLong = $longitude - $longDelta;
            $maxLong = $longitude + $longDelta;

            // Query per trovare proprietà nel bounding box, con ordinamento per distanza approssimativa
            $properties = Property::selectRaw(
                "*, POWER(lat - ?, 2) + POWER(`long` - ?, 2) AS distance",
                [$latitude, $longitude]
            )
                ->whereBetween('lat', [$minLat, $maxLat])
                ->whereBetween('long', [$minLong, $maxLong])
                ->where('available', 1)
                ->orderBy('sponsored', 'desc') // Ordina per sponsorizzazione secondaria
                ->orderBy('distance', 'asc') // Ordina per distanza approssimativa
                ->get();
        } else {
            // Mostra tutte le proprietà disponibili se non sono presenti coordinate
            $properties = Property::where('available', 1)
                ->orderByDesc('sponsored')
                ->get();
        }

        return view('guest.homepage', compact('properties'));
    }

    public function show($slug)
    {
        $property = Property::where('slug', $slug)
            ->where('available', 1)
            ->firstOrFail();

        return view('guest.properties.show', compact('property'));
    }
}
