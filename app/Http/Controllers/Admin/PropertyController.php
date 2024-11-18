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
        
        // Include all properties, both active and deleted
        $properties = Property::withTrashed()
            ->where('user_id', $userId)
            ->orderBy('deleted_at', 'asc') // Non-deleted properties come first
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
    
        // Salva la cover_image
        $form_data['cover_image'] = $request->hasFile('cover_image')
            ? Storage::put('cover_image', $request->file('cover_image'))
            : 'https://reviveyouthandfamily.org/wp-content/uploads/2016/11/house-placeholder.jpg';
    
        // Crea la proprietà
        $property = Property::create($form_data);
    
        // Associa sponsor (se presenti)
        if ($request->has('sponsors')) {
            $property->sponsors()->attach($request->sponsors);
        }
    
        // Associa servizi (se presenti)
        if ($request->has('services')) {
            $property->services()->attach($request->services);
        }
    
        // Gestisci immagini aggiuntive (se presenti)
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = Storage::put('property_images', $image); // Salva ogni immagine nella directory 'property_images'
                Image::create([
                    'property_id' => $property->id, // Collega l'immagine alla proprietà
                    'path' => $path, // Percorso dell'immagine salvata
                ]);
            }
        }

        // Gestisci immagini cancellate
        if ($request->has('deleted_images')) {
            $deletedImages = json_decode($request->input('deleted_images'), true); // Decodifica il valore JSON passato dal form
            foreach ($deletedImages as $imageId) {
                $image = Image::find($imageId);
                if ($image) {
                    Storage::delete($image->path); // Rimuove il file fisico dallo storage
                    $image->delete(); // Rimuove il record dal database
                }
            }
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
        // Block access if property is soft deleted
        if ($property->trashed() || $property->user_id !== Auth::id()) {
            return abort(404, 'Property not found');
        }

        // $ipAddress = $request->ip();
        // $view = View::where('ip_address', $ipAddress)
        //     ->where('property_id', $property->id)
        //     ->latest('updated_at')
        //     ->first();

        // if (!$view || $view->updated_at->diffInMinutes(now()) >= 1) {
        //     View::create([
        //         'ip_address' => $ipAddress,
        //         'property_id' => $property->id,
        //     ]);
        // }

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
        // Block access if property is soft deleted
        if ($property->trashed() || $property->user_id !== Auth::id()) {
            return abort(404, 'Property not found');
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
        // Block access if property is soft deleted
        if ($property->trashed() || $property->user_id !== Auth::id()) {
            return abort(404, 'Property not found');
        }
    
        $form_data = $request->validated();
        $form_data['slug'] = Property::generateSlug($form_data['title']);
    
        // Aggiorna l'immagine di copertina
        if ($request->hasFile('cover_image') && $request->file('cover_image')->isValid()) {
            if (!Str::startsWith($property->cover_image, 'https')) {
                Storage::delete($property->cover_image); // Rimuove la vecchia immagine se presente
            }
            $form_data['cover_image'] = Storage::put('cover_image', $request->file('cover_image'));
        }
    
        // Aggiorna la proprietà
        $property->update($form_data);
    
        // Aggiorna sponsor
        if ($request->has('sponsors')) {
            $property->sponsors()->sync($request->sponsors);
        } else {
            $property->sponsors()->sync([]);
        }
    
        // Aggiorna servizi
        if ($request->has('services')) {
            $property->services()->sync($request->services);
        } else {
            $property->services()->sync([]);
        }
    
        // Aggiungi nuove immagini
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = Storage::put('property_images', $image);
                Image::create([
                    'property_id' => $property->id,
                    'path' => $path,
                ]);
            }
        }
    
        // Elimina immagini rimosse
        if ($request->has('deleted_images')) {
            $deletedImagesRaw = $request->input('deleted_images');
        
            // Converti in array
            $deletedImages = is_array($deletedImagesRaw) ? $deletedImagesRaw : explode(',', $deletedImagesRaw);
        
            foreach ($deletedImages as $imageId) {
                $image = Image::find($imageId);
        
                if ($image) {
                    // Elimina il file fisico solo se non è un URL remoto
                    if (!Str::startsWith($image->path, 'http')) {
                        Storage::delete($image->path);
                    }
                    $image->delete(); // Rimuovi il record dal database
                }
            }
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

        // Block access if property is soft deleted
        if ($property->trashed() || $property->user_id !== Auth::id()) {
            return abort(404, 'Not Found');
        }

        $lastSponsorPivot = $property->sponsors()
            ->withPivot('end_date')
            ->orderByPivot('created_at', 'desc')
            ->first();

        $startDate = $lastSponsorPivot && $lastSponsorPivot->pivot->end_date
            ? Carbon::parse($lastSponsorPivot->pivot->end_date)
            : now();

        $sponsor = Sponsor::findOrFail($sponsorId);
        $endDate = $startDate->copy()->addHours($sponsor->duration);

        $property->sponsors()->attach($sponsorId, [
            'created_at' => $startDate,
            'updated_at' => now(),
            'end_date' => $endDate
        ]);

        $property->checkSponsorshipStatus();

        return redirect()->back()->with('success', 'Sponsor successfully assigned to the property!');
    }

    public function restore($id)
    {
        $property = Property::onlyTrashed()->where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $property->restore();

        return redirect()->route("admin.properties.index")->with("success", 'Announcement restored successfully.');
    }
}
