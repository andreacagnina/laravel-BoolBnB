<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\View;
use App\Models\Property;

class ViewController extends Controller
{public function index()
    {
        $userProperties = Property::where('user_id', auth()->id())->with(['sponsors', 'messages'])->get();
    
        // Calcola le statistiche sommarie
        $stats = [
            'total_properties' => $userProperties->count(),
            'total_sponsorships' => $userProperties->sum(fn($property) => $property->sponsors->count()),
            'total_sponsorship_cost' => $userProperties->sum(fn($property) => $property->sponsors->sum('price')),
            'total_views' => $userProperties->sum('views_count'),
            'total_favorites' => $userProperties->sum('favorites_count'),
            'total_messages' => $userProperties->sum(fn($property) => $property->messages()->withTrashed()->count()),
            'average_price' => $userProperties->avg('price'),
        ];
    
        return view('admin.views.index', compact('stats', 'userProperties'));
    }
    public function show(Property $property)
    {
        $property->loadCount('views');
        $property->loadCount('favorites');

        return view('admin.views.show', compact('property'));
    }
}
