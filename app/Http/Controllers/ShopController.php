<?php

namespace App\Http\Controllers;

use App\Models\Option_name;
use App\Models\Shop;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function __construct()
    {
        $this->middleware(['admin.auth']);
    }

    public function index()
    {
        $shops = Shop::paginate(10); // Fetch shops data
        return view('admin.shops.index', compact('shops')); // Pass the variable correctly
    }

    public function optioname()
    {
        $optionames = Option_name::paginate(10); 
        return view('admin.option_name', compact('optionames'));
    }

}
