<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => "Alex",
            'email' => 'alex@gmail.com',
            'password' => Hash::make('pass'),
        ]);

        DB::table('users')->insert([
            'name' => "Jane",
            'email' => 'jane@gmail.com',
            'password' => Hash::make('pass'),
        ]);

        DB::table('users')->insert([
            'name' => "Rick",
            'email' => 'rick@gmail.com',
            'password' => Hash::make('pass'),
        ]);

        DB::table('users')->insert([
            'name' => "Morty",
            'email' => 'morty@gmail.com',
            'password' => Hash::make('pass'),
        ]);
    }
}
