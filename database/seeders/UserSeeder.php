<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Mario',
            'surname' => 'Rossi',
            'email' => 'mario.rossi@example.com',
            'birth_date' => '1990-01-15',
            'email_verified_at' => now(),
            'password' => Hash::make('password123'),
            'remember_token' => Str::random(10),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        User::create([
            'name' => 'Luigi',
            'surname' => 'Verdi',
            'email' => 'luigi.verdi@example.com',
            'birth_date' => '1985-07-20',
            'email_verified_at' => now(),
            'password' => Hash::make('password123'),
            'remember_token' => Str::random(10),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        User::create([
            'name' => 'Giulia',
            'surname' => 'Bianchi',
            'email' => 'giulia.bianchi@example.com',
            'birth_date' => '1992-03-10',
            'email_verified_at' => now(),
            'password' => Hash::make('password123'),
            'remember_token' => Str::random(10),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        User::create([
            'name' => 'Francesca',
            'surname' => 'Neri',
            'email' => 'francesca.neri@example.com',
            'birth_date' => '1988-11-05',
            'email_verified_at' => now(),
            'password' => Hash::make('password123'),
            'remember_token' => Str::random(10),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
