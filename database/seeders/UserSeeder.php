<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        $users = [
            ['name' => 'Mario', 'surname' => 'Rossi', 'birth_date' => '1990-01-15'],
            ['name' => 'Luigi', 'surname' => 'Verdi', 'birth_date' => '1985-07-20'],
            ['name' => 'Giulia', 'surname' => 'Bianchi', 'birth_date' => '1992-03-10'],
            ['name' => 'Francesca', 'surname' => 'Neri', 'birth_date' => '1988-11-05'],
        ];

        foreach ($users as $user) {
            $createdAt = $faker->dateTimeBetween('-1 year', '-6 month');
            $verifiedAt = (clone $createdAt)->modify('+1 hour');

            User::create([
                'name' => $user['name'],
                'surname' => $user['surname'],
                'email' => strtolower($user['name'] . '.' . $user['surname']) . '@example.com',
                'birth_date' => $user['birth_date'],
                'email_verified_at' => $verifiedAt,
                'password' => Hash::make('password123'),
                'remember_token' => Str::random(10),
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        }
    }
}
