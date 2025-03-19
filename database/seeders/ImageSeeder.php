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
        $properties = Property::all();

        foreach ($properties as $property) {
            $propertyConfig = config('db_properties');
            $currentConfig = collect($propertyConfig)->firstWhere('title', $property->title);

            if (isset($currentConfig['images']) && count($currentConfig['images']) > 0) {
                foreach ($currentConfig['images'] as $imagePath) {
                    Image::create([
                        'path' => $imagePath,
                        'property_id' => $property->id,
                    ]);
                }
            }
        }
    }
}
