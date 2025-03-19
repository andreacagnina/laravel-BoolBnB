<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Carbon\Carbon;

class FavoriteSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        $properties = DB::table('properties')->select('id', 'created_at')->get();

        if ($properties->isEmpty()) {
            $this->command->info('No properties found in the properties table.');
            return;
        }

        foreach (range(1, 500) as $index) {
            $property = $faker->randomElement($properties);

            $viewsCount = DB::table('views')
                ->where('property_id', $property->id)
                ->count();

            $favoritesCount = DB::table('favorites')
                ->where('property_id', $property->id)
                ->count();

            if ($favoritesCount >= $viewsCount) {
                continue;
            }

            $sponsorships = DB::table('property_sponsor')
                ->where('property_id', $property->id)
                ->get();

            $favoriteDate = null;

            if ($sponsorships->isNotEmpty() && rand(1, 100) <= 50) {
                $randomSponsorship = $faker->randomElement($sponsorships->toArray());
                $favoriteDate = $faker->dateTimeBetween(
                    $randomSponsorship->created_at,
                    $randomSponsorship->end_date
                );
            } else {
                $favoriteDate = $faker->dateTimeBetween($property->created_at, now());
            }

            $favoriteDate = $this->fixInvalidDate($favoriteDate);

            DB::table('favorites')->insert([
                'property_id' => $property->id,
                'ip_address' => $faker->ipv4,
                'created_at' => $favoriteDate,
                'updated_at' => $favoriteDate,
            ]);
        }
    }

    private function fixInvalidDate($date)
    {
        try {
            Carbon::parse($date->format('Y-m-d H:i:s'));
            return $date;
        } catch (\Exception $e) {
            $date->modify('+1 hour');
            return $this->fixInvalidDate($date);
        }
    }
}
