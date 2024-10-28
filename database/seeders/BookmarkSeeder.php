<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BookmarkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('bookmarks')->insert([
        [
            'user_id' => 1,
            'name' => 'Laravel Documentation',
            'url' => 'https://laravel.com/docs',
            'category' => 'Documentation',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'user_id' => 1,
            'name' => 'PHP Official Site',
            'url' => 'https://www.php.net/',
            'category' => 'Documentation',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'user_id' => 1,
            'name' => 'Youtube',
            'url' => 'https://www.youtube.com/',
            'category' => 'Entertaining',
            'created_at' => now(),
            'updated_at' => now(),
        ],
    ]);
    }
}
