<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\BrandsSeeder;
use Database\Seeders\CategoriesSeeder;
use Database\Seeders\TypesSeeder;
use Database\Seeders\RolesSeeder;
use Database\Seeders\PaymentTypesSeeder;
use Database\Seeders\DeliveryTypesSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            BrandsSeeder::class,
            CategoriesSeeder::class,
            TypesSeeder::class,
            RolesSeeder::class,
            DeliveryTypesSeeder::class,
            PaymentTypesSeeder::class,
        ]);
    }
}
