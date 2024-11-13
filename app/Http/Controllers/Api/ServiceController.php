<?php

namespace App\Http\Controllers\Api;

use App\Models\Service; // Modello per la tabella services
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ServiceController extends Controller
{
    public function index()
    {
        // Recupera tutti i servizi
        $services = Service::all(); 

        // Restituisci i servizi come risposta JSON
        return response()->json([
            'success' => true,
            'results' => $services
        ]);

    }
}