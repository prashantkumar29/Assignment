<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function __construct()
    {
        // No auth middleware for register/login
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['message' => 'User registered successfully'], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Invalidate previous tokens
        $user->tokens()->delete();

        // Generate a new API token
        $token = $user->createToken('api-token')->plainTextToken;

        // Generate a new encryption key and store it (for demo, store in user meta or cache)
        $encryptionKey = Str::random(32);
        \Cache::put('encryption_key_' . $user->id, $encryptionKey, 3600);

        return response()->json([
            'token' => $token,
            'encryption_key' => $encryptionKey,
        ]);
    }
}
