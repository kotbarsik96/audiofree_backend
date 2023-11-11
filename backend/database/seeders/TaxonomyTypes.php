<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class TaxonomyTypes extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = DB::raw('NOW()');

        DB::table('taxonomies_types')->insert([
            'type' => 'brand',
            'title' => 'Бренд',
            'created_at' => $now,
            'updated_at' => $now
        ]);
        DB::table('taxonomies_types')->insert([
            'type' => 'category',
            'title' => 'Категория',
            'created_at' => $now,
            'updated_at' => $now
        ]);
        DB::table('taxonomies_types')->insert([
            'type' => 'type',
            'title' => 'Тип',
            'created_at' => $now,
            'updated_at' => $now
        ]);
        DB::table('taxonomies_types')->insert([
            'type' => 'product_status',
            'title' => 'Статус товара',
            'created_at' => $now,
            'updated_at' => $now
        ]);
    }
}
