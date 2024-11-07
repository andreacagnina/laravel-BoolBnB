<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Property;
use App\Models\Image;

class ImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Recuperiamo tutte le proprietà dal database
        $properties = Property::all();

        // Link di default per le immagini
        $defaultImages = [
            'https://placehold.co/600x400?text=Cover+Image',
            'https://placehold.co/600x400?text=Cover+Image',
            'https://placehold.co/600x400?text=Cover+Image',
        ];

        // Iteriamo su ogni proprietà
        foreach ($properties as $property) {
            // Recuperiamo le immagini dalla configurazione db_properties
            $propertyConfig = config('db_properties');

            // Troviamo la proprietà corrente nella configurazione db_properties
            $currentConfig = collect($propertyConfig)->firstWhere('title', $property->title);

            // Se ci sono immagini specificate nella configurazione
            if (isset($currentConfig['images']) && count($currentConfig['images']) > 0) {
                foreach ($currentConfig['images'] as $imagePath) {
                    Image::create([
                        'path' => $imagePath,
                        'property_id' => $property->id,
                    ]);
                }
            } else {
                // Se non ci sono immagini specificate, usiamo quelle di default
                foreach ($defaultImages as $imagePath) {
                    Image::create([
                        'path' => $imagePath,
                        'property_id' => $property->id,
                    ]);
                }
            }
        }
    }
}
