<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class TypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = DB::raw('NOW()');

        DB::table('types')->insert([
            'name' => 'Проводные',
            'created_at' => $now,
            'updated_at' => $now
        ]);
        DB::table('types')->insert([
            'name' => 'Беспроводные',
            'created_at' => $now,
            'updated_at' => $now
        ]);
    }
}
