<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Sponsor;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SponsorController extends Controller
{
    /**
     * Display a listing of sponsors and properties.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sponsors = Sponsor::all();
        $properties = Property::where('user_id', Auth::id())->get();

        return view('admin.sponsors.index', compact('sponsors', 'properties'));
    }
    /**
     * Display the sponsorship details for a specific property.
     *
     * @param  int  $propertyId
     * @return \Illuminate\Http\Response
     */
    public function show($propertySlug)
    {
        // Recupera la proprietà tramite slug e verifica che appartenga all'utente
        $property = Property::with('sponsors')
            ->where('slug', $propertySlug)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Recupera tutti gli sponsor disponibili (se necessario per una lista di scelta)
        $sponsors = Sponsor::all();

        return view('admin.sponsors.show', compact('property', 'sponsors'));
    }
}
