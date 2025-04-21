<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product_type;

class ProductTypeController extends Controller  
{
    public function __construct()
    {
        $this->middleware(['auth','check.user.shop']);
    }
    public function index()
    {
        $productTypes = Product_type::paginate(10); 
        return view('user.product_types.index', compact('productTypes'));
    }
}
