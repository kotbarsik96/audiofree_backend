<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = DB::raw('NOW()');

        DB::table('users')->insert([
            'email' => 'kotbarsik96@gmail.com',
            'email_verified_at' => $now,
            'password' => '$2y$10$BeKb50td8uP0up5haFDeyuW7pxHarmjaus4I.MU3G3ugpS0Bhl8ve',
            'name' => 'Алексей',
            'surname' => 'Иванов',
            'patronymic' => 'Петрович',
            'location' => 'Москва',
            'street' => 'Тургенева',
            'house' => '67',
            'role_id' => '1',
            'created_at' => $now,
            'updated_at' => $now
        ]);
    }
}
