<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Property;
use App\Models\Service;
use Illuminate\Support\Facades\DB;

class PropertyServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $properties = config('db_properties');

        foreach ($properties as $propertyConfig) {
            $property = Property::where('title', $propertyConfig['title'])->first();

            if ($property && isset($propertyConfig['services'])) {
                $property->services()->attach($propertyConfig['services']);
            }
        }
    }
}
