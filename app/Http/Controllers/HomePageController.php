<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Property;

class HomePageController extends Controller
{
    public function index()
    {
        $properties = Property::Where('available', 1)->get();

        return view('homepage', compact('properties'));
    }
}
