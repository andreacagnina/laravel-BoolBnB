<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Property;

class ViewController extends Controller
{
    public function index()
    {
        $userProperties = Property::where('user_id', auth()->id())->with(['sponsors', 'messages', 'views', 'favorites'])->get();
    
        // Calcola le statistiche sommarie
        $stats = [
            'total_properties' => $userProperties->count(),
            'total_sponsorships' => $userProperties->sum(fn($property) => $property->sponsors->count()),
            'total_sponsorship_cost' => $userProperties->sum(fn($property) => $property->sponsors->sum('price')),
            'total_views' => $userProperties->sum(fn($property) => $property->views->count()),
            'total_favorites' => $userProperties->sum(fn($property) => $property->favorites->count()),
            'total_messages' => $userProperties->sum(fn($property) => $property->messages->count()),
            'average_price' => $userProperties->avg('price'),
        ];
    
        // Aggrega i dati mensili per i grafici
        $monthlyData = [
            'views' => $this->getMonthlyCountsForProperties($userProperties, 'views'),
            'messages' => $this->getMonthlyCountsForProperties($userProperties, 'messages'),
            'favorites' => $this->getMonthlyCountsForProperties($userProperties, 'favorites'),
            'sponsors' => $this->getMonthlyCountsForProperties($userProperties, 'sponsors'),
        ];
    
        // Calcola la distribuzione dei tipi di proprietà
        $propertyTypes = $userProperties->groupBy('type')
            ->map(fn($properties) => $properties->count())
            ->toArray();
    
        // Calcola le interazioni totali
        $totalInteractions = [
            array_sum($monthlyData['views']),
            array_sum($monthlyData['messages']),
            array_sum($monthlyData['favorites']),
            array_sum($monthlyData['sponsors']),
        ];
    
        // Passa le variabili alla view
        return view('admin.views.index', compact('stats', 'monthlyData', 'propertyTypes', 'totalInteractions'));
    }       

    public function show(Property $property)
    {
        $property->loadCount(['views', 'favorites', 'messages', 'sponsors']);

        // Raggruppa i dati mensili per ciascuna metrica
        $monthlyData = [
            'views' => $this->getMonthlyCounts($property->views(), 'views'),
            'messages' => $this->getMonthlyCounts($property->messages(), 'messages'),
            'favorites' => $this->getMonthlyCounts($property->favorites(), 'favorites'),
            'sponsors' => $this->getMonthlyCounts($property->sponsors(), 'property_sponsor'),
        ];

        return view('admin.views.show', [
            'property' => $property,
            'monthlyData' => $monthlyData,
        ]);
    }

    /**
     * Recupera i dati mensili normalizzati (1-12 mesi) per una relazione.
     */
    private function getMonthlyCounts($relation, $table)
    {
        $data = $relation->selectRaw("MONTH($table.created_at) as month, COUNT(*) as count")
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();

        // Normalizza i dati per includere tutti i 12 mesi
        return array_replace(array_fill(1, 12, 0), $data);
    }

    /**
     * Calcola i conteggi mensili aggregati per tutte le proprietà di un utente.
     */
    private function getMonthlyCountsForProperties($properties, $relation)
    {
        $monthlyCounts = array_fill(1, 12, 0);
    
        foreach ($properties as $property) {
            // Controlla se la relazione è `sponsors` per qualificare `property_sponsor.created_at`
            if ($relation === 'sponsors') {
                $counts = $property->$relation()
                    ->selectRaw('MONTH(property_sponsor.created_at) as month, COUNT(*) as count') // Usa property_sponsor.created_at
                    ->groupBy('month')
                    ->pluck('count', 'month')
                    ->toArray();
            } else {
                $counts = $property->$relation()
                    ->selectRaw('MONTH(' . $relation . '.created_at) as month, COUNT(*) as count') // Qualifica il created_at per altre relazioni
                    ->groupBy('month')
                    ->pluck('count', 'month')
                    ->toArray();
            }
    
            foreach ($counts as $month => $count) {
                $monthlyCounts[$month] += $count;
            }
        }
    
        return $monthlyCounts;
    }        
}
