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

        // 3. Создаем Автора (для создания опросов)
        DB::table('users')->insertOrIgnore([
            'id' => 1,
            'fio' => 'Иван Иванов',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'),
            'role_id' => 1, // author
            'api_token' => 'token_author_123', // Фиксированный токен для удобства теста
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 4. Создаем Респондента (для прохождения опросов)
        DB::table('users')->insertOrIgnore([
            'id' => 2,
            'fio' => 'Петр Петров',
            'email' => 'user@user.com',
            'password' => Hash::make('password'),
            'role_id' => 2, // respondent
            'api_token' => 'token_user_123',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}