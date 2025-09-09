<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $totalOrders = Order::count();
        $revenue = Order::sum('total_amount');
        return response()->json([
            'status' => true,
            'total_users' => $totalUsers,
            'total_orders' => $totalOrders,
            'revenue' => $revenue,
        ],200);
    }
}
