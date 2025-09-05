<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\CustomerCommission;
use App\Models\OrderCommission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Customer;

class RepCustomerController extends Controller
{
    public function index()
    {
        $customer = Auth::guard('customer')->user();

        if (!$customer) {
            return response()->json([
                'status'  => false,
                'message' => 'Unauthenticated',
            ], 401);
        }

        $repId = $customer->rep_id;

        $totalUsers = User::where('rep_id', $repId)->count();
        $totalOrders = OrderCommission::where('rep_id', $repId)->count();
        $totalCommission = CustomerCommission::where('rep_id', $repId)->sum('total_commission');
        return response()->json([
            'status' => true,
            'total_users' => $totalUsers,
            'total_orders' => $totalOrders,
            'total_commission' => $totalCommission,
        ],200);
    }

    public function customerRepVlist()
    {
        $customer = Auth::guard('customer')->user();

        if (!$customer) {
            return response()->json([
                'status'  => false,
                'message' => 'Unauthenticated',
            ], 401);
        }

        $repId = $customer->rep_id;

        if (!$repId) {                         
            return response()->json([
                'status' => true,
                'rep_id' => null,
                'users'  => [],
            ]);
        }

        $referredUsers = User::where('rep_id', $repId)
            ->select('id', 'name', 'email', 'mobile', 'company_name')
            ->latest()
            ->get();

        return response()->json([
            'status'             => true,
            'rep_id'             => $repId,
            'rep_code'           => $customer->rep_code,
            'commission_percent' => $customer->commission_percent,
            'total_referrals'    => $referredUsers->count(),
            'users'              => $referredUsers,
        ]);
    }

    public function customerRepCommission()
    {
        $customer = Auth::guard('customer')->user();

        if (!$customer) {
            return response()->json([
                'status'  => false,
                'message' => 'Unauthenticated',
            ], 401);
        }

        $repId = $customer->rep_id;

        $orderCommissions = OrderCommission::where('rep_id', $repId)->get();

        $orderCommissions->transform(function ($commission) {
            $user = User::find($commission->user_id);
            $commission->name = $user ? $user->name : 'N/A';
            return $commission;
        });

        return response()->json([
            'status'      => true,
            'message'     => 'Rep Commission fetched',
            'commissions' => $orderCommissions,
        ]);
    }
}
