<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register user.
     */
    public function register(Request $request)
    {
        try {
            $request->validate([
                'first_name'    => 'required|string|max:50',
                'last_name'     => 'required|string|max:50',
                'email'         => 'required|string|email|max:50|unique:users,email',
                'password'      => 'required|string|min:6',
                'mobile'        => 'required|string|max:15|unique:users,mobile',
                'rep_code'      => 'nullable|string|max:50',
                'company_name'  => 'required|string|max:50',
                'address1'      => 'required|string|max:255',
                'address2'      => 'nullable|string|max:50',
                'city'          => 'required|string|max:50',
                'country'       => 'required|string|max:50',
                'postcode'      => 'required|string|max:50',
            ]);
        } catch (ValidationException $e) {
            $errors = $e->validator->errors();

            // Custom message if email already taken
            if ($errors->has('email')) {
                return response()->json([
                    'status'  => false,
                    'message' => 'This email is already registered.',
                ], 422);
            }

            // For other validation errors
            return response()->json([
                'status' => false,
                'message' => $errors->first(),
            ], 422);
        }

        // Create user
        $user = User::create([
            'name'          => $request->first_name . ' ' . $request->last_name,
            'email'         => $request->email,
            'password'      => bcrypt($request->password),
            'mobile'        => $request->mobile,
            'rep_code'      => $request->rep_code,
            'company_name'  => $request->company_name,
            'address1'      => $request->address1,
            'address2'      => $request->address2,
            'city'          => $request->city,
            'country'       => $request->country,
            'postcode'      => $request->postcode,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'User registered successfully.',
            'user_id' => $user->id,
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

        if ($user->admin_approval == false) {
            return response()->json([
                'status'     => true,
                'message'    => 'Your account is awaiting admin approval.',
                'token'      => $token,
                'token_type' => 'bearer',
                'user'       => $user,
                'expires_in' => auth('api')->factory()->getTTL() * 21900
            ], 200);
        }

        return response()->json([
            'status'     => true,
            'message'    => 'Login successful.',
            'token'      => $token,
            'token_type' => 'bearer',
            'user'       => $user,
            'expires_in' => auth('api')->factory()->getTTL() * 21900
        ],200);
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
