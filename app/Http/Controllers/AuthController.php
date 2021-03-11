<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string|min:4'
        ]);

        if (!auth()->attempt($validated)) {
            return response([
                'error' => 'Email or password does not match our records'
            ], 401);
        }

        return response([
            'token' => $request->user()
                ->createToken($validated['email'])
                ->plainTextToken
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response([
            'message' => 'Your tokens have been revoked'
        ]);
    }
}
