<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Seeding Users first, as other tables may depend on User records
        $this->call(UserSeeder::class);

        // Properties are seeded next, followed by related tables
        $this->call(PropertySeeder::class);
        
        // Seeding other related data
        $this->call(SponsorSeeder::class);
        $this->call(ServiceSeeder::class);

        // ImageSeeder to populate the images for each property
        $this->call(ImageSeeder::class);

        // MessageSeeder to populate any initial messages
        $this->call(MessageSeeder::class);
    }    
}
