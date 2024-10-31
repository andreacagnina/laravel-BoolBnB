<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Property;

class PropertySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $properties = config('db_properties.properties');

        foreach ($properties as $property) {
            $NewProperty = new Property();
            $NewProperty->title = $property['title'];
            $NewProperty->slug = Str::slug($property['title'], '-');
            $NewProperty->description = $property['description'];
            $NewProperty->num_rooms = $property['num_rooms'];
            $NewProperty->num_beds = $property['num_beds'];
            $NewProperty->num_baths = $property['num_baths'];
            $NewProperty->mq = $property['mq'];
            $NewProperty->zip = $property['zip'];
            $NewProperty->city = $property['city'];
            $NewProperty->address = $property['address'];
            $NewProperty->lat = $property['lat'];
            $NewProperty->long = $property['long'];
            $NewProperty->price = $property['price'];
            $NewProperty->type = $property['type'];
            $NewProperty->floor = $property['floor'];
            $NewProperty->available = $property['available'];
            $NewProperty->user_id = $property['user_id'];
            $NewProperty->save();
        }
    }
}
