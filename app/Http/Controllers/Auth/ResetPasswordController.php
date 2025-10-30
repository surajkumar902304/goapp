<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/';
    protected function resetPassword($user, $password)
    {
        $user->password = Hash::make($password);
        $user->save();
    }

     /**
     * Redirect after password reset success.
     */
    protected function sendResetResponse($response)
    {
        try {
            $message = trans($response);
        } catch (\Throwable $e) {
            $message = __('Your password has been successfully reset.');
        }

        return redirect($this->redirectTo)->with('status', $message);
    }
}
