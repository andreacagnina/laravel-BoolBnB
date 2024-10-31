<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class PropertySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $jsonPath = config_path('updated_user_id_properties_data.json');
        $jsonData = json_decode(file_get_contents($jsonPath), true);


        $currentTimestamp = Carbon::now();
        foreach ($jsonData as &$property) {
            $property['created_at'] = $currentTimestamp;
            $property['updated_at'] = $currentTimestamp;
        }

        DB::table('properties')->insert($jsonData);
    }
}
