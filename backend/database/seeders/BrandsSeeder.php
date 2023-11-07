<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class BrandsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('brands')->insert([
            'name' => 'Apple',
        ]);
        DB::table('brands')->insert([
            'name' => 'Samsung',
        ]);
        DB::table('brands')->insert([
            'name' => 'Xiaomi',
        ]);
        DB::table('brands')->insert([
            'name' => 'Honor',
        ]);
    }
}
