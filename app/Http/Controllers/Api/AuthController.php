<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\PasswordChangedMail;
use App\Models\Customer;
use App\Models\Rep;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\ChangePasswordRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserRegisteredMail;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $request->validate([
                'first_name'    => 'required|string|max:255',
                'last_name'     => 'required|string|max:255',
                'email'         => 'required|string|email|max:255|unique:users,email',
                'password'      => 'required|string|min:6',
                'mobile'        => 'required|string|max:15|unique:users,mobile',
                'rep_code'      => 'nullable|string|max:255',
                'company_name'  => 'required|string|max:255',
                'address1'      => 'required|string|max:255',
                'address2'      => 'nullable|string|max:255',
                'city'          => 'required|string|max:255',
                'country'       => 'required|string|max:255',
                'postcode'      => 'required|string|max:255',
            ]);
        } catch (ValidationException $e) {
            $errors = $e->validator->errors();

            if ($errors->has('email')) {
                return response()->json([
                    'status'  => false,
                    'message' => 'This email is already registered.',
                ], 422);
            }

            return response()->json([
                'status' => false,
                'message' => $errors->first(),
            ], 422);
        }

        $rep = null;
        if ($request->filled('rep_code')) {
            $rep = Rep::where('rep_code', strtoupper($request->rep_code))->first();
        }

        $user = User::create([
            'name'          => $request->first_name . ' ' . $request->last_name,
            'email'         => $request->email,
            'password'      => bcrypt($request->password),
            'mobile'        => $request->mobile,
            'rep_id'        => $rep?->rep_id,
            'company_name'  => $request->company_name,
            'address1'      => $request->address1,
            'address2'      => $request->address2,
            'city'          => $request->city,
            'country'       => $request->country,
            'postcode'      => $request->postcode,
        ]);

        Mail::to($user->email)->send(new UserRegisteredMail($user));

        return response()->json([
            'status'  => true,
            'message' => 'User registered successfully.',
            'user_id' => $user->id,
        ]);
    }

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

        if ($user->admin_approval === 'Pending') {
            return response()->json([
                'status'      => true,
                'message'     => 'Your account is awaiting admin approval.',
                'token'       => $token,
                'token_type'  => 'bearer',
                'user_detail' => $user,
                'expires_in'  => auth('api')->factory()->getTTL() * 21900
            ], 200);
        }

        if ($user->admin_approval === 'Declined') {
            return response()->json([
                'status'  => false,
                'message' => 'Your account has been declined by the admin.',
            ], 403);
        }

        $repDetails = null;
        if ($user->rep_id) {
            $repDetails = Customer::select('rep_id','name','email','mobile','rep_code','commission_percent',)
                ->find($user->rep_id);
        }

        return response()->json([
            'status'      => true,
            'message'     => 'Login successful.',
            'token'       => $token,
            'token_type'  => 'bearer',
            'user_detail' => $user,
            'rep_details' => $repDetails,
            'expires_in'  => auth('api')->factory()->getTTL() * 21900
        ],200);
    }

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

    public function userProfile()
    {
        $user = Auth::guard('api')->user();

        $repDetails = null;
        if ($user->rep_id) {
            $repDetails = Customer::select('rep_id','name','email','mobile','rep_code','commission_percent',)
                ->find($user->rep_id);
        }

        return response()->json([
            'status' => true,
            'message'     => 'User Profile Fetch Successfully.',
            'user_detail' => $user,
            'rep_details' => $repDetails,
        ]);
    }

    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $user = $request->user();
        $user->password = Hash::make($request->input('new_password'));
        $user->save();

        Mail::to($user->email)->send(new PasswordChangedMail($user));

        return response()->json([
            'status'  => true,
            'message' => 'Password changed successfully.',
        ], 200);
    }

    public function deleteUserAccount(Request $request)
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    'status'  => false,
                    'message' => 'User not found.',
                ], 404);
            }

            $user->forceDelete();

            return response()->json([
                'status'  => true,
                'message' => 'Account deleted permanently.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user(); 

        $data = $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'email'          => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'mobile'         => ['required', 'string', 'max:20'],
            'company_name'   => ['required', 'string', 'max:255'],
            'address1'       => ['required', 'string', 'max:255'],
            'address2'       => ['nullable', 'string', 'max:255'],
            'city'           => ['required', 'string', 'max:255'],
            'country'        => ['required', 'string', 'max:255'],
            'postcode'       => ['required', 'string', 'max:255'],
        ]);

        $user->update($data);

        $repDetails = null;
        if ($user->rep_id) {
            $repDetails = Customer::select('rep_id','name','email','mobile','rep_code','commission_percent',)
                ->find($user->rep_id);
        }

        return response()->json([
            'status'      => true,
            'message' => 'User Profile Updated.',
            'user_detail' => $user,
            'rep_details' => $repDetails,
        ], 200);
    }

}
