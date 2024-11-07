<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Service;
use Illuminate\Support\Str;


class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $services = config('db_services');

        foreach ($services as $service) {
            $newService = new Service();
            $newService->name = $service['name'];
            $newService->slug = Str::slug($service['name'], '-');
            $newService->icon = $service['icon'];
            $newService->save();
        }
    }
}
