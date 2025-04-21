<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','check.user.shop']);
    }
    public function index()
    {
        $brands = Brand::paginate(10);
        return view('user.brands.index', compact('brands'));
    }
}
