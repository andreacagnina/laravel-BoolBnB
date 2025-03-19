<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Property;
use Carbon\Carbon;

class PropertySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $properties = config('db_properties');

        foreach ($properties as $property) {
            $userCreatedAt = DB::table('users')->where('id', $property['user_id'])->value('created_at');
            
            if ($userCreatedAt) {
                $userCreatedAt = Carbon::parse($userCreatedAt);
                $sixMonthsAgo = Carbon::now()->subMonths(6);

                $propertyCreatedAt = Carbon::createFromTimestamp(
                    rand($userCreatedAt->timestamp, $sixMonthsAgo->timestamp)
                );

                $NewProperty = new Property();
                $NewProperty->title = $property['title'];
                $NewProperty->slug = Str::slug($property['title'], '-');
                $NewProperty->cover_image = $property['cover_image'];
                $NewProperty->description = $property['description'];
                $NewProperty->num_rooms = $property['num_rooms'];
                $NewProperty->num_beds = $property['num_beds'];
                $NewProperty->num_baths = $property['num_baths'];
                $NewProperty->mq = $property['mq'];
                $NewProperty->address = $property['address'];
                $NewProperty->lat = $property['lat'];
                $NewProperty->long = $property['long'];
                $NewProperty->price = $property['price'];
                $NewProperty->type = $property['type'];
                $NewProperty->floor = $property['floor'];
                $NewProperty->available = $property['available'];
                $NewProperty->sponsored = $property['sponsored'];
                $NewProperty->user_id = $property['user_id'];
                $NewProperty->created_at = $propertyCreatedAt;
                $NewProperty->updated_at = $propertyCreatedAt;
                $NewProperty->save();
            }
        }
    }
}
