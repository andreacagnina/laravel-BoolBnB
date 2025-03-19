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

        // Recupera tutti i servizi per visualizzarli nella vista
        $services = Service::all();

        // Costruisci la query di base
        $properties = Property::query()
            ->where('deleted_at', null) // Escludi le proprietà eliminate
            ->where('available', 1)
            ->where('num_rooms', '>=', $minRooms)
            ->where('num_beds', '>=', $minBeds);

        // Filtra per servizi selezionati
        if (!empty($selectedServices)) {
            $properties = $properties->whereHas('services', function ($query) use ($selectedServices) {
                $query->whereIn('services.id', $selectedServices);
            }, '=', count($selectedServices));
        }

        // Se sono fornite latitudine e longitudine, calcola la distanza
        if ($latitude && $longitude) {
            $properties = $properties->selectRaw(
                "properties.*, (6371 * acos(cos(radians(?)) * cos(radians(lat)) * cos(radians(`long`) - radians(?)) + sin(radians(?)) * sin(radians(lat)))) AS distance",
                [$latitude, $longitude, $latitude]
            )
            ->having('distance', '<=', $radius)
            ->orderBy('sponsored', 'desc')
            ->orderBy('distance', 'asc');
        } else {
            // Ordina per proprietà sponsorizzate
            $properties = $properties->orderBy('sponsored', 'desc');
        }

        $properties = $properties->get();

        // Aggiungi URL completo dell'immagine di copertina e arrotonda la distanza
        $properties->map(function ($property) {
            $property->cover_image_url = Str::startsWith($property->cover_image, 'http') ? $property->cover_image : asset('storage/' . $property->cover_image);
            if (isset($property->distance)) {
                $property->distance = round($property->distance, 2);
            }
            return $property;
        });

        // Se la richiesta è AJAX, restituisci la risposta in JSON
        if ($request->ajax()) {
            return response()->json(['properties' => $properties]);
        }

        // Restituisci la vista con le proprietà e i servizi
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
