<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = DB::raw('NOW()');

        DB::table('roles')->insert([
            'name' => 'SUPER_ADMINISTRATOR',
            'priority' => 1,
            'created_at' => $now,
            'updated_at' => $now
        ]);
        DB::table('roles')->insert([
            'name' => 'ADMINISTRATOR',
            'priority' => 2,
            'created_at' => $now,
            'updated_at' => $now
        ]);
        DB::table('roles')->insert([
            'name' => 'USER',
            'priority' => 3,
            'created_at' => $now,
            'updated_at' => $now
        ]);
    }
}
