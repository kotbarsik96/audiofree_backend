<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class DeliveryTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = DB::raw('NOW()');

        DB::table('delivery_types')->insert([
            'name' => 'inside_location',
            'title' => 'Доставка в пределах города',
            'created_at' => $now,
            'updated_at' => $now
        ]);
        DB::table('delivery_types')->insert([
            'name' => 'outside_location',
            'title' => 'Доставка за пределы города',
            'created_at' => $now,
            'updated_at' => $now
        ]);
        DB::table('delivery_types')->insert([
            'name' => 'pickup',
            'title' => 'Самовывоз',
            'created_at' => $now,
            'updated_at' => $now
        ]);
        DB::table('delivery_types')->insert([
            'name' => 'express_delivery',
            'title' => 'Экспресс-доставка',
            'created_at' => $now,
            'updated_at' => $now
        ]);
    }
}
