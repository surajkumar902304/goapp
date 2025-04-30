<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class HomeController extends Controller
{
    public $selectedShopId;
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index(Request $request)
    {
        return view('home');
    }

    public function logout()
    {
        // session destroy
        Auth::guard('web')->logout();
        return redirect()->route('login');
    }
}
