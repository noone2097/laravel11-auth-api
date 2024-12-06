<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller
{
    public function register(Request $request)
    {
        try {
            $validateUser = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required',
            ]);

            if ($validateUser->fails()) {
                return response()->json([
                    'error' => $validateUser->errors()], 401);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);

            return response()->json([
                'message' => 'User registered successfully',
                'token' => $user->createToken('API_TOKEN')->plainTextToken,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => 'Registration failed',
                'message' => $th->getMessage()], 500);
        }
    }

    public function login(Request $request)
    {
        try {
            $validateUser = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if ($validateUser->fails()) {
                return response()->json([
                    'error' => $validateUser->errors()], 401);
            }

            if (!auth()->attempt($request->only('email', 'password'))) {
                return response()->json([
                    'error' => 'Invalid credentials'], 401);
            }

            $user = User::where('email', $request->email)->first();

            return response()->json([
                'message' => 'User logged in successfully',
                'token' => $user->createToken('API_TOKEN')->plainTextToken,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => 'Login failed',
                'message' => $th->getMessage()], 500);
        }
    }

    public function profile(Request $request)
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    'error' => 'User not found'], 404);
            }

            return response()->json([
                'user' => $user], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => 'Failed to retrieve profile',
                'message' => $th->getMessage()], 500);
        }
    }
    public function logout(Request $request)
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    'error' => 'User not found'], 404);
            }

            $user->tokens()->delete();

            return response()->json([
                'message' => 'User logged out successfully'], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => 'Logout failed',
                'message' => $th->getMessage()], 500);
        }
    }

}