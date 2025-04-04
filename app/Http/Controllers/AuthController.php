<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // Logic to handle user login
        // Validate the request data
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            // Generate a new token for the authenticated user
            /** @var \App\Models\User */
            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'token' => $token,
            ]);
        }

        return response()->json(['message' => 'invalid credentials'], 401);
    }

    public function logout(Request $request)
    {
        /** @var \App\Models\User */
        $user = Auth::user();
        $user->tokens()->delete();

        return response()->json(['message' => 'logout successful']);
    }
}
