<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\View;
use App\Models\Property;

class ViewController extends Controller
{
    public function show(Property $property)
    {
        $property->loadCount('views');
        $property->loadCount('favorites');

        return view('admin.views.show', compact('property'));
    }
}
