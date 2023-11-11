<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class TaxonomiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = DB::raw('NOW()');

        // бренды
        DB::table('taxonomies')->insert([
            'taxonomy_type' => 'brand',
            'name' => 'Apple',
            'created_at' => $now,
            'updated_at' => $now
        ]);
        DB::table('taxonomies')->insert([
            'taxonomy_type' => 'brand',
            'name' => 'Samsung',
            'created_at' => $now,
            'updated_at' => $now
        ]);
        DB::table('taxonomies')->insert([
            'taxonomy_type' => 'brand',
            'name' => 'Huawei',
            'created_at' => $now,
            'updated_at' => $now
        ]);
        DB::table('taxonomies')->insert([
            'taxonomy_type' => 'brand',
            'name' => 'Xiaomi',
            'created_at' => $now,
            'updated_at' => $now
        ]);
        DB::table('taxonomies')->insert([
            'taxonomy_type' => 'brand',
            'name' => 'JBL',
            'created_at' => $now,
            'updated_at' => $now
        ]);

        // категории
        DB::table('taxonomies')->insert([
            'taxonomy_type' => 'category',
            'name' => 'Наушники',
            'created_at' => $now,
            'updated_at' => $now
        ]);

        // типы
        DB::table('taxonomies')->insert([
            'taxonomy_type' => 'type',
            'name' => 'Проводные',
            'created_at' => $now,
            'updated_at' => $now
        ]);
        DB::table('taxonomies')->insert([
            'taxonomy_type' => 'type',
            'name' => 'Беспроводные',
            'created_at' => $now,
            'updated_at' => $now
        ]);

        // статусы товаров
        DB::table('taxonomies')->insert([
            'taxonomy_type' => 'product_status',
            'name' => 'Неактивен',
            'created_at' => $now,
            'updated_at' => $now
        ]);
        DB::table('taxonomies')->insert([
            'taxonomy_type' => 'product_status',
            'name' => 'Активен',
            'created_at' => $now,
            'updated_at' => $now
        ]);
        DB::table('taxonomies')->insert([
            'taxonomy_type' => 'product_status',
            'name' => 'На модерации',
            'created_at' => $now,
            'updated_at' => $now
        ]);
    }
}
