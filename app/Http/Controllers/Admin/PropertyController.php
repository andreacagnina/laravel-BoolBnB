<?php

namespace App\Http\Controllers\Admin;

use App\Models\Property;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePropertyRequest;
use App\Http\Requests\UpdatePropertyRequest;
use App\Models\Image;
use App\Models\Service;
use App\Models\Sponsor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\View;
use Carbon\Carbon;

class PropertyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $userId = Auth::id();
        $properties = Property::where('user_id', $userId)
            ->orderBy('sponsored', 'desc')
            ->orderBy('available', 'desc')
            ->orderBy('title', 'asc')
            ->get();

        foreach ($properties as $property) {
            $property->checkSponsorshipStatus();
        }

        return view('admin.properties.index', compact('properties'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $propertyTypes = ['mansion', 'ski-in/out', 'tree-house', 'apartment', 'dome', 'cave', 'cabin', 'lake', 'beach', 'castle'];
        $images = Image::all();
        $services = Service::all();
        $sponsors = Sponsor::all();

        return view('admin.properties.create', compact('images', 'services', 'sponsors', 'propertyTypes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StorePropertyRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePropertyRequest $request)
    {
        $form_data = $request->validated();
        $form_data['slug'] = Property::generateSlug($form_data['title']);
        $form_data['user_id'] = Auth::id();

        if ($request->hasFile('cover_image')) {
            $form_data['cover_image'] = Storage::put('cover_image', $form_data['cover_image']);
        } else {
            $form_data['cover_image'] = 'https://placehold.co/600x400?text=Cover+Image';
        }

        $property = Property::create($form_data);

        if ($request->has('sponsors')) {
            $property->sponsors()->attach($request->sponsors);
        }

        if ($request->has('services')) {
            $property->services()->attach($request->services);
        }

        return redirect()->route('admin.properties.index')->with("success", "Announcement Created");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Property  $property
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Property $property)
    {
        // Verifica che l'utente sia il proprietario
        if ($property->user_id !== Auth::id()) {
            return abort(404, 'Not Found');
        }

        $ipAddress = $request->ip();
        $view = View::where('ip_address', $ipAddress)
            ->where('property_id', $property->id)
            ->latest('updated_at')
            ->first();

        if (!$view || $view->updated_at->diffInMinutes(now()) >= 1) {
            View::create([
                'ip_address' => $ipAddress,
                'property_id' => $property->id,
            ]);
        }

        $sponsors = Sponsor::all();
        $services = Service::all();
        $images = Image::all();
        $latitude = $property->lat;
        $longitude = $property->long;

        return view('admin.properties.show', compact('property', 'sponsors', 'services', 'images', 'latitude', 'longitude'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Property  $property
     * @return \Illuminate\Http\Response
     */
    public function edit(Property $property)
    {
        if ($property->user_id !== Auth::id()) {
            return abort(404, 'Not Found');
        }

        $propertyTypes = ['mansion', 'ski-in/out', 'tree-house', 'apartment', 'dome', 'cave', 'cabin', 'lake', 'beach', 'castle'];
        $sponsors = Sponsor::all();
        $services = Service::all();
        $images = Image::all();

        return view('admin.properties.edit', compact('property', 'sponsors', 'services', 'images', 'propertyTypes'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatePropertyRequest  $request
     * @param  \App\Models\Property  $property
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePropertyRequest $request, Property $property)
    {
        if ($property->user_id !== Auth::id()) {
            return abort(404, 'Not Found');
        }

        $form_data = $request->validated();
        $form_data['slug'] = Property::generateSlug($form_data['title']);

        if ($request->hasFile('cover_image') && $request->file('cover_image')->isValid()) {
            if (!Str::startsWith($property->cover_image, 'https')) {
                Storage::delete($property->cover_image);
            }
            $form_data['cover_image'] = Storage::put('cover_image', $request->file('cover_image'));
        }

        $property->update($form_data);

        if ($request->has('sponsors')) {
            $property->sponsors()->sync($request->sponsors);
        } else {
            $property->sponsors()->sync([]);
        }

        if ($request->has('services')) {
            $property->services()->sync($request->services);
        } else {
            $property->services()->sync([]);
        }

        return redirect()->route('admin.properties.index')->with("success", 'Announcement Modified');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Property  $property
     * @return \Illuminate\Http\Response
     */
    public function destroy(Property $property)
    {
        if ($property->user_id !== Auth::id()) {
            return abort(404, 'Not Found');
        }

        if (!Str::startsWith($property->cover_image, 'https')) {
            Storage::delete($property->cover_image);
        }
        $property->delete();

        return redirect()->route("admin.properties.index")->with("success", 'Announcement canceled');
    }

    public function assignSponsor(Request $request)
    {
        $propertySlug = $request->input('property_slug');
        $sponsorId = $request->input('sponsor_id');

        $property = Property::where('slug', $propertySlug)->firstOrFail();

        // Verifica che l'utente sia il proprietario
        if ($property->user_id !== Auth::id()) {
            return abort(404, 'Not Found');
        }

        $lastSponsorPivot = $property->sponsors()
            ->withPivot('end_date')
            ->orderByPivot('created_at', 'desc')
            ->first();

        if ($lastSponsorPivot && $lastSponsorPivot->pivot->end_date) {
            $startDate = Carbon::parse($lastSponsorPivot->pivot->end_date);
        } else {
            $startDate = now();
        }

        $sponsor = Sponsor::findOrFail($sponsorId);
        $endDate = $startDate->copy()->addHours($sponsor->duration); // Usa copy() per evitare di modificare startDate

        $property->sponsors()->attach($sponsorId, [
            'created_at' => $startDate,
            'updated_at' => now(),
            'end_date' => $endDate
        ]);

        $property->checkSponsorshipStatus();

        return redirect()->back()->with('success', 'Sponsor successfully assigned to the property!');
    }
}
