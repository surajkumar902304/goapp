<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_THROTTLED) {
            return response()->json([
                'status'  => false,
                'message' => 'Please wait and try again after one hour.'
            ], 429);
        }

        return $status === Password::RESET_LINK_SENT
            ? response()->json([
                'status'  => true,  
                'message' => 'Reset link sent to your email.'
            ], 200)
            : response()->json([
                'status'  => false, 
                'message' => __($status)
            ], 400);
    }
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;
}
