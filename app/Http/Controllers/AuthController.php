<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{

    // POST /api/register
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        $token = $user->createToken('registrar-token')->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully',
            'token' => $token,
            'user' => $user
        ], 201);
    }


    // POST /api/login

public function login(Request $request)
{
    $request->validate([
        'email'    => 'required|email',
        'password' => 'required|string',
    ]);

    // Call external API instead of local Auth::attempt()
    $response = Http::post('https://admission-api-production.up.railway.app/api/auth/login', [
        'email'    => $request->email,
        'password' => $request->password,
    ]);

    // If API rejects login
    if ($response->failed()) {
        return response()->json([
            'message' => 'Invalid credentials (API)',
            'error'   => $response->json()
        ], 401);
    }

    // API success response
    $data = $response->json();

    return response()->json([
        'message' => 'Login successful (via API)',
        'token'   => $data['token'] ?? null,
        'user'    => $data['user'] ?? null,
    ]);
}

    // GET /api/user
    public function user(Request $request)
    {
        return response()->json([
            'user' => $request->user()
        ]);
    }


    // POST /api/logout
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }
}