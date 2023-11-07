<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = DB::raw('NOW()');

        DB::table('categories')->insert([
            'name' => 'Наушники',
            'created_at' => $now,
            'updated_at' => $now
        ]);
    }
}
