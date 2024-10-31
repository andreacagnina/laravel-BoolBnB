<?php

namespace Database\Seeders;

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
        $jsonFiles = ['user_id_1.json', 'user_id_2.json'];
        $currentTimestamp = Carbon::now();

        foreach ($jsonFiles as $file) {
            $jsonPath = config_path($file);
            $jsonData = json_decode(file_get_contents($jsonPath), true);

            // Aggiunge timestamp
            foreach ($jsonData as &$property) {
                $property['created_at'] = $currentTimestamp;
                $property['updated_at'] = $currentTimestamp;
            }

            // Inserisce i dati nella tabella
            DB::table('properties')->insert($jsonData);
        }
    }
}
