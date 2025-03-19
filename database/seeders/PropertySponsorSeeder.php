<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Property;
use App\Models\Sponsor;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PropertySponsorSeeder extends Seeder
{
    public function run()
    {
        // Configurazione delle proprietà
        $properties = config('db_properties');

        foreach ($properties as $propertyConfig) {
            // Trova la proprietà in base al titolo
            $property = Property::where('title', $propertyConfig['title'])->first();

            if ($property && isset($propertyConfig['sponsors'])) {
                $lastEndDate = null; // Inizializza l'ultima data di fine

                foreach ($propertyConfig['sponsors'] as $sponsorId) {
                    $sponsor = Sponsor::find($sponsorId);

                    if ($lastEndDate) {
                        // La data di inizio è la data di fine dell'ultimo sponsor
                        $startDate = $lastEndDate;
                    } else {
                        // Calcola una data di inizio casuale partendo dalla data di creazione della proprietà
                        $randomStartDays = rand(0, 120);
                        $startDate = Carbon::parse($property->created_at)->addDays($randomStartDays);

                        // Se la data di inizio è nel futuro, correggila a oggi
                        if ($startDate > Carbon::now()) {
                            $startDate = Carbon::now();
                        }
                    }

                    // Calcola la data di fine basandosi sulla durata dello sponsor
                    $endDate = $startDate->copy()->addHours($sponsor->duration);

                    // Inserisci i dati nella tabella pivot
                    DB::table('property_sponsor')->insert([
                        'property_id' => $property->id,
                        'sponsor_id' => $sponsorId,
                        'created_at' => $startDate,
                        'updated_at' => $startDate,
                        'end_date' => $endDate,
                    ]);

                    // Aggiorna la data di fine dell'ultimo sponsor
                    $lastEndDate = $endDate;
                }
            }
        }
    }
}
