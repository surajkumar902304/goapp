<?php

namespace App\Http\Controllers\Customer\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('customer.auth.login');
    }

    public function customerLogin(Request $request)
    {
        $this->validate($request, [
            'email'    => 'required|email',
            'password' => 'required|min:6',
        ]);

        if (Auth::guard('customer')->attempt(
            ['email' => $request->email, 'password' => $request->password],
            $request->remember
        )) {
            return redirect('/rep/dashboard');
        }
        else{

            return redirect()->back()->withInput($request->only('email', 'remember'))->withErrors([
                'email' => 'Credentials do not match our records.',
            ]);
        }
    }

    public function customerLogout()
    {
        Auth::guard('customer')->logout();
        return redirect()->route('customer.login');
    }
}



