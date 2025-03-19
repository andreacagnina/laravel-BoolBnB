<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Carbon\Carbon;

class ViewSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        $properties = DB::table('properties')->select('id', 'created_at')->get();

        if ($properties->isEmpty()) {
            $this->command->info('No properties found in the properties table.');
            return;
        }

        foreach (range(1, 1000) as $index) {
            $property = $faker->randomElement($properties);
            $sponsorships = DB::table('property_sponsor')
                ->where('property_id', $property->id)
                ->get();

            $viewDate = null;

            if ($sponsorships->isNotEmpty() && rand(1, 100) <= 50) {
                $randomSponsorship = $faker->randomElement($sponsorships->toArray());
                $viewDate = $this->generateValidDate(
                    $faker,
                    $randomSponsorship->created_at,
                    $randomSponsorship->end_date
                );
            } else {
                $viewDate = $this->generateValidDate(
                    $faker,
                    $property->created_at,
                    now()
                );
            }

            DB::table('views')->insert([
                'property_id' => $property->id,
                'ip_address' => $faker->ipv4,
                'created_at' => $viewDate,
                'updated_at' => $viewDate,
            ]);
        }
    }

    private function generateValidDate($faker, $start, $end)
    {
        do {
            $date = $faker->dateTimeBetween($start, $end)->format('Y-m-d H:i:s');
        } while (!$this->isValidDate($date));

        return $date;
    }

    private function isValidDate($date)
    {
        try {
            Carbon::parse($date);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
