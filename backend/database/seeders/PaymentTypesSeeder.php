<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = DB::raw('NOW()');

        DB::table('payment_types')->insert([
            'name' => 'cash',
            'title' => 'Наличными',
            'created_at' => $now,
            'updated_at' => $now
        ]);
        DB::table('payment_types')->insert([
            'name' => 'bank_card',
            'title' => 'Банковской картой',
            'created_at' => $now,
            'updated_at' => $now
        ]);
    }
}
