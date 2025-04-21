<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use App\Models\Product_type;
use App\Models\Brand;
use App\Models\Tag;
use App\Models\Shop_user;
use App\Models\Shop;
use Auth;

class HomeController extends Controller
{
    public $selectedShopId;
    public function __construct()
    {
        $this->middleware(['auth','check.user.shop'], ['except' => ['logout','approval']]);
    }

    public function index(Request $request)
    {
        // session create
        $this->selectedShopId = session('selected_shop_id', Shop_user::where('user_id')->value('shop_id'));
  
        if (!$this->selectedShopId) {
            $this->selectedShopId = Shop_user::where('user_id', auth()->id())->value('shop_id');
            session()->put('selected_shop_id', $this->selectedShopId);
        }

        // Fetch the selected shop details
        $selectedShop = Shop::find($this->selectedShopId);
        $shopName = $selectedShop->shop_name ?? 'Unknown Shop';
        $shopStatus = $selectedShop->shop_status ?? 0;

        $totalProductTypes = Product_type::where('shop_id', $this->selectedShopId)->count();
        $totalBrands = Brand::where('shop_id', $this->selectedShopId)->count();
        $totalTags = Tag::where('shop_id', $this->selectedShopId)->count();

        return view('home', compact('totalProductTypes', 'totalBrands', 'totalTags', 'shopName', 'shopStatus'));
    }

    public function selectShop(Request $request)
    {
        $shopId = $request->input('shopId');
        session()->put('selected_shop_id', $shopId);

        return redirect()->route('home');
    }


    public function logout()
    {
        // session destroy
        session()->forget(['selected_shop_id']);
        Auth::guard('web')->logout();
        return redirect()->route('login');
    }

    public function approval()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        $userShop = Shop_user::where('user_id', $user->id)->exists();

        if ($userShop) {
            return redirect()->route('home');
        }
    
        return view('user.approval');
    }


    public function mangeuser()
    {
        $selectedShopId = session('selected_shop_id');
        
        $isOwner = Shop_user::where('user_id', auth()->id())
                            ->where('shop_id', $selectedShopId)
                            ->where('user_role', 'owner')
                            ->exists();
                            
        if (!$isOwner) {
            abort(403, 'You do not have permission to access this page.');  
        }
        $mangeuser = User::paginate(10); 
        return view('user.mange_users', compact('mangeuser'));
    }
}
