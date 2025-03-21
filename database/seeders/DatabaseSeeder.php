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
        $this->call(UserSeeder::class);
        $this->call(PropertySeeder::class);
        $this->call(ServiceSeeder::class);
        $this->call(SponsorSeeder::class);
        $this->call(ImageSeeder::class);
        $this->call(PropertyServiceSeeder::class);
        $this->call(PropertySponsorSeeder::class);
        $this->call(MessageSeeder::class);
        $this->call(ViewSeeder::class);
        $this->call(FavoriteSeeder::class);
    }
}
