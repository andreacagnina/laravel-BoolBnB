<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Property;

class ViewController extends Controller
{
    public function show(Property $property)
    {
        // Recupera la proprietà specifica con il conteggio delle visualizzazioni
        $property->loadCount('views');

        return view('admin.views.show', compact('property'));
    }
}
