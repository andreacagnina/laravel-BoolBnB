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
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        $properties = Property::where('user_id', $userId)->get();

        return view('admin.properties.index', compact('properties'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $images = Image::all();
        $services = Service::all();
        $sponsors = Sponsor::all();

        return view('admin.properties.create', compact('images', 'services', 'sponsors'));
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

        if ($request->hasFile('cover_image') && $request->file('cover_image')->isValid()) {
            $form_data['cover_image'] = Storage::put('cover_image', $request->file('cover_image'));
        } else {
            $form_data['cover_image'] = 'https://placehold.co/600x400?text=Cover+Image';
        }

        $property = Property::create($form_data);

        if ($request->has('sponsors')) {
            $sponsors = $request->sponsors;
            $property->sponsors()->attach($sponsors);
        }

        if ($request->has('services')) {
            $services = $request->services;
            $property->services()->attach($services);
        }

        return redirect()->route('admin.properties.index')->with("success", "Annuncio creato");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Property  $property
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Property $property)
    {
        // $userId = Auth::id();
        // $properties = Property::where('user_id', $userId)->get();
        // Ottieni l'indirizzo IP del visitatore
        $ipAddress = $request->ip();
        // Cerca una vista esistente per questa combinazione di IP e proprietà
        $view = DB::table('views')
            ->where('ip_address', $ipAddress)
            ->where('property_id', $property->id)
            ->first();

        if (!$view || Carbon::parse($view->updated_at)->diffInMinutes(now()) >= 5) {
            if ($view) {
                // Aggiorna solo il timestamp di updated_at se esiste già
                DB::table('views')
                    ->where('id', $view->id)
                    ->update(['updated_at' => now()]);
            } else {
                // Se la vista non esiste, crea una nuova riga
                DB::table('views')->insert([
                    'ip_address' => $ipAddress,
                    'property_id' => $property->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $sponsors = Sponsor::all();
        $services = Service::all();
        $images = Image::all();

        return view('admin.properties.show', compact('property', 'sponsors', 'services', 'images'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Property  $property
     * @return \Illuminate\Http\Response
     */
    public function edit(Property $property)
    {
        $sponsors = Sponsor::all();
        $services = Service::all();
        $images = Image::all();

        return view('admin.properties.edit', compact('property', 'sponsors', 'services', 'images'));
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
        $form_data = $request->validated();
        $form_data['slug'] = Property::generateSlug($form_data['title']);

        if ($request->hasFile('cover_image') && $request->file('cover_image')->isValid()) {

            if (Str::startsWith($property->cover_image, 'https') === false) {
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

        return redirect()->route('admin.properties.show', ['property' => $property->id])->with("success", "Annuncio Modificato");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Property  $property
     * @return \Illuminate\Http\Response
     */
    public function destroy(Property $property)
    {
        if (Str::startsWith($property->cover_image, 'https') === false) {
            Storage::delete($property->cover_image);
        }
        $property->delete();
        return redirect()->route("admin.properties.index")->with("success", "Annuncio cancellato");
    }
}
