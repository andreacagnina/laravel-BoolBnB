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
        $properties = config('db_properties');

        foreach ($properties as $propertyConfig) {
            $property = Property::where('title', $propertyConfig['title'])->first();

            if ($property && isset($propertyConfig['sponsors'])) {
                $existingSponsors = DB::table('property_sponsor')
                    ->where('property_id', $property->id)
                    ->orderBy('created_at', 'asc')
                    ->get();

                $lastEndDate = null;

                foreach ($propertyConfig['sponsors'] as $sponsorId) {
                    $sponsor = Sponsor::find($sponsorId);

                    if ($lastEndDate) {
                        $startDate = $lastEndDate;
                    } else {
                        $randomStartDays = rand(0, 180);
                        $startDate = Carbon::parse($property->created_at)->addDays($randomStartDays);
                    }

                    $endDate = $startDate->copy()->addHours($sponsor->duration);

                    DB::table('property_sponsor')->insert([
                        'property_id' => $property->id,
                        'sponsor_id' => $sponsorId,
                        'created_at' => $startDate,
                        'updated_at' => $startDate,
                        'end_date' => $endDate,
                    ]);

                    $lastEndDate = $endDate;
                }
            }
        }
    }
}
