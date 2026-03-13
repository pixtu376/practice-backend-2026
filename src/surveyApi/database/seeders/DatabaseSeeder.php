<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
{
    \App\Models\Role::insert([
        ['name' => 'author'],
        ['name' => 'respondent'],
    ]);

    \App\Models\AnswerType::insert([
        ['name' => 'radio'],
        ['name' => 'checkbox'],
        ['name' => 'text'],
    ]);

    \App\Models\User::create([
        'fio' => 'Создатель опросника',
        'email' => 'opros@survey.ru',
        'password' => bcrypt('password'),
        'role_id' => 1,
        'api_token' => \Illuminate\Support\Str::random(80),
    ]);
}
}
