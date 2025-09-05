<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BankDetail;
use Illuminate\Http\Request;

class BankDetailController extends Controller
{
    public function index()
    {
        $bank_detail = BankDetail::where('is_active', 1)->first();

        return response()->json([
            'status'  => true,
            'message' => 'Bank Detail fetched successfully',
            'bank_detail' => $bank_detail,
        ], 200);
    }
}
