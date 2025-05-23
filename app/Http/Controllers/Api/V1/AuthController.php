<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * @group Authentication
 *
 * APIs for user authentication
 */
class AuthController extends Controller
{

    /**
     * Register a new user
     *
     * @bodyParam name string required The user's name. Example: John Doe
     * @bodyParam email string required The user's email. Example: john@example.com
     * @bodyParam password string required The user's password. Example: password123
     * @bodyParam password_confirmation string required The password confirmation. Example: password123
     *
     * @response 201 {
     *   "success": true,
     *   "message": "User registered successfully",
     *   "token": "1|abc123..."
     * }
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $validated['password'] = bcrypt($validated['password']);
        $user = User::create($validated);
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'User registered successfully',
            'token' => $token,
        ], 201);
    }
  
    /**
     * Log in a user
     *
     * @bodyParam email string required The user's email. Example: john@example.com
     * @bodyParam password string required The user's password. Example: password123
     *
     * @response 200 {
     *   "success": true,
     *   "message": "User logged in successfully",
     *   "token": "1|abc123..."
     * }
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (!auth()->attempt($validated)) {
            return response()->json(['success' => false, 'message' => 'Invalid credentials'], 401);
        }

        $user = auth()->user();

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'User logged in successfully',
            'token' => $token,
        ], 200);
    }

    /**
     * Get the authenticated user's info
     *
     * @authenticated
     * @header Authorization Bearer {YOUR ACCESS TOKEN}
     *
     * @response 200 {
     *   "success": true,
     *   "user": { "id": 1, "name": "John Doe", ... }
     * }
     */
    public function me(Request $request)
    {
        return response()->json(['success' => true, 'user' => $request->user()], 200);
    }
    /**
     * Log out the authenticated user
     *
     * @authenticated
     * @header Authorization Bearer {YOUR ACCESS TOKEN}
     *
     * @response 200 {
     *   "success": true,
     *   "message": "User logged out successfully"
     * }
     */
    public function logout()
    {
        auth()->user()->tokens()->delete();

        return response()->json(['success' => true, 'message' => 'User logged out successfully'], 200);
    }
}
