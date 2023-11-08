<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class OrderStatusesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = DB::raw('NOW()');

        DB::table('order_statuses')->insert([
            'name' => 'waiting_userdata',
            'title' => 'Оформляется',
            'created_at' => $now,
            'updated_at' => $now
        ]);
        DB::table('order_statuses')->insert([
            'name' => 'waiting_payment',
            'title' => 'Ожидает оплаты',
            'created_at' => $now,
            'updated_at' => $now
        ]);
        DB::table('order_statuses')->insert([
            'name' => 'in_delivery_paid',
            'title' => 'Оплачен, в доставке',
            'created_at' => $now,
            'updated_at' => $now
        ]);
        DB::table('order_statuses')->insert([
            'name' => 'in_delivery_not_paid',
            'title' => 'Не оплачен, в доставке',
            'created_at' => $now,
            'updated_at' => $now
        ]);
        DB::table('order_statuses')->insert([
            'name' => 'recieved',
            'title' => 'Получен',
            'created_at' => $now,
            'updated_at' => $now
        ]);
        DB::table('order_statuses')->insert([
            'name' => 'paid',
            'title' => 'Оплачен',
            'created_at' => $now,
            'updated_at' => $now
        ]);
        DB::table('order_statuses')->insert([
            'name' => 'rejected',
            'title' => 'Отклонен',
            'created_at' => $now,
            'updated_at' => $now
        ]);
        DB::table('order_statuses')->insert([
            'name' => 'waiting_return',
            'title' => 'Ожидает возврат',
            'created_at' => $now,
            'updated_at' => $now
        ]);
        DB::table('order_statuses')->insert([
            'name' => 'returned',
            'title' => 'Возвращен',
            'created_at' => $now,
            'updated_at' => $now
        ]);
    }
}
