<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Property;
use App\Models\Sponsor;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PropertySponsorSeeder extends Seeder
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

            if ($property && isset($propertyConfig['sponsors'])) {
                $startDate = Carbon::now();

                foreach ($propertyConfig['sponsors'] as $sponsorId) {
                    $sponsor = Sponsor::find($sponsorId);

                    $lastSponsor = DB::table('property_sponsor')
                        ->where('property_id', $property->id)
                        ->orderBy('end_date', 'desc')
                        ->first();

                    if ($lastSponsor) {
                        $startDate = Carbon::parse($lastSponsor->end_date);
                    }

                    $endDate = $startDate->copy()->addHours($sponsor->duration);

                    DB::table('property_sponsor')->insert([
                        'property_id' => $property->id,
                        'sponsor_id' => $sponsorId,
                        'created_at' => $startDate,
                        'updated_at' => $startDate,
                        'end_date' => $endDate,
                    ]);

                    $startDate = $endDate;
                }
            }
        }
    }
}
