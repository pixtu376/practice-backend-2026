<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Заполняем роли
        DB::table('role')->insertOrIgnore([
            ['id_role' => 1, 'name' => 'author'],
            ['id_role' => 2, 'name' => 'respondent'],
        ]);

        // 2. Заполняем типы ответов
        DB::table('answer_type')->insertOrIgnore([
            ['id_type' => 1, 'name' => 'radio'],
            ['id_type' => 2, 'name' => 'checkbox'],
            ['id_type' => 3, 'name' => 'text'],
        ]);

        // 3. Создаем тестового пользователя (опционально)
        DB::table('users')->insertOrIgnore([
            'fio' => 'Иван Иванов',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'),
            'role_id' => 1,
            'api_token' => Str::random(80),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}