<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => 'Nadun Hettiarachchi',
                'email' => 'nadhun97@gmail.com',
                'password' => bcrypt('brcom7ys2b7'), // Make sure to hash the password
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
