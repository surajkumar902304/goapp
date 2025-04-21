<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    /**
     * Register user.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
    
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => bcrypt($request->password),
        ]);
     
        return response()->json([
            'status'     => true,
            'message'    => 'User registered successfully.',
            'user'       => $user->id,
        ]);
    }
     
    /**
     * Login user via JWT.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string|min:6',
        ]);
    
        $credentials = $request->only('email', 'password');
        $user = User::where('email', $credentials['email'])->first();
    
        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid email or password.',
            ], 401);
        }
    
        try {
            $token = JWTAuth::fromUser($user);
        } catch (JWTException $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong generating token.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    
        return response()->json([
            'status'     => true,
            'message'    => 'Login successful.',
            'token'      => $token,
            'token_type' => 'bearer',
            'user'       => $user->id,
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }

    /**
     * Logout the user (invalidate the token).
     */
    public function logout()
    {
        try {
            Auth::guard('api')->logout();

            return response()->json([
                'status'  => true,
                'message' => 'Logged out successfully.',
            ]);
        } catch (JWTException $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Failed to logout.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get the authenticated user's profile.
     */
    public function userProfile()
    {
        return response()->json([
            'status' => true,
            'user'   => Auth::guard('api')->user(),
        ]);
    }
}
