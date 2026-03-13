<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $fields = $request->validate([
            'fio' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|min:6'
        ]);

        $user = User::create([
            'fio' => $fields['fio'],
            'email' => $fields['email'],
            'password' => Hash::make($fields['password']),
            'role_id' => 1,
            'api_token' => Str::random(80),
        ]);

        return response(['token' => $user->api_token, 'message' => 'Успех!'], 201);
    }

    public function login(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        $user = User::where('email', $fields['email'])->first();

        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response(['message' => 'Неверные данные'], 401);
        }

        $user->update(['api_token' => Str::random(80)]);

        return response(['token' => $user->api_token]);
    }
}