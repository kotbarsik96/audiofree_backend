<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\TaxonomyTypes;
use Database\Seeders\TaxonomiesSeeder;
use Database\Seeders\TaxonomiesMetaSeeder;
use Database\Seeders\RolesSeeder;
use Database\Seeders\PaymentTypesSeeder;
use Database\Seeders\DeliveryTypesSeeder;
use Database\Seeders\OrderStatusesSeeder;
use Database\Seeders\UsersSeeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // TaxonomyTypes::class,
            // TaxonomiesSeeder::class,
            // TaxonomiesMetaSeeder::class,
            // RolesSeeder::class,
            // DeliveryTypesSeeder::class,
            // PaymentTypesSeeder::class,
            // OrderStatusesSeeder::class,
            // UsersSeeder::class
        ]);
    }
}
