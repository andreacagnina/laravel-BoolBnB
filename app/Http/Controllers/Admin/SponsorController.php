<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSponsorRequest;
use App\Http\Requests\UpdateSponsorRequest;
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
        // Recupera la proprietà con gli sponsor attivi
        $property = Property::with('sponsors')->where('slug', $propertySlug)->where('user_id', Auth::id())->firstOrFail();
        $sponsors = Sponsor::all();
    
        return view('admin.sponsors.show', compact('property', 'sponsors'));
    }         

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreSponsorRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSponsorRequest $request)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Sponsor  $sponsor
     * @return \Illuminate\Http\Response
     */
    public function edit(Sponsor $sponsor)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateSponsorRequest  $request
     * @param  \App\Models\Sponsor  $sponsor
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSponsorRequest $request, Sponsor $sponsor)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Sponsor  $sponsor
     * @return \Illuminate\Http\Response
     */
    public function destroy(Sponsor $sponsor)
    {
        //
    }
}
