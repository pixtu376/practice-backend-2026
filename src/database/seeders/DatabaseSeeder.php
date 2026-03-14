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
        DB::table('role')->insertOrIgnore([
            ['id_role' => 1, 'name' => 'author'],
            ['id_role' => 2, 'name' => 'respondent'],
        ]);

        DB::table('answer_type')->insertOrIgnore([
            ['id_type' => 1, 'name' => 'radio'],
            ['id_type' => 2, 'name' => 'checkbox'],
            ['id_type' => 3, 'name' => 'text'],
        ]);

        DB::table('users')->insertOrIgnore([
            'id' => 1,
            'fio' => 'Иван Иванов',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'),
            'role_id' => 1, // author
            'api_token' => 'token_author_123',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

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