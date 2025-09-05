<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{
    public function balance()
    {
        $user = JWTAuth::parseToken()->authenticate();

        $wallet = Wallet::firstOrCreate(['user_id' => $user->id], ['balance' => 0]);

        return response()->json([
            'success' => true,
            'message' => 'Fetch Wallet balance Successfully',
            'balance' => $wallet->balance
        ]);
    }

    // Credit wallet
    public function credit(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $request->validate([
            'amount'      => 'required|numeric|min:0.01',
            'reference'   => 'nullable|string',
            'description' => 'nullable|string'
        ]);

        return DB::transaction(function () use ($request, $user) {
            $wallet = Wallet::firstOrCreate(['user_id' => $user->id], ['balance' => 0]);

            $wallet->balance += $request->amount;
            $wallet->save();

            WalletTransaction::create([
                'wallet_id'   => $wallet->id,
                'type'        => 'credit',
                'amount'      => $request->amount,
                'reference'   => $request->reference,
                'description' => $request->description,
            ]);

            return response()->json(['success' => true, 'message' => 'Wallet credited']);
        });
    }

    // Debit wallet
    public function debit(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $request->validate([
            'amount'      => 'required|numeric|min:0.01',
            'reference'   => 'nullable|string',
            'description' => 'nullable|string'
        ]);

        return DB::transaction(function () use ($request, $user) {
            $wallet = Wallet::firstOrCreate(['user_id' => $user->id], ['balance' => 0]);

            if ($wallet->balance < $request->amount) {
                return response()->json(['success' => false, 'message' => 'Insufficient balance'], 400);
            }

            $wallet->balance -= $request->amount;
            $wallet->save();

            WalletTransaction::create([
                'wallet_id'   => $wallet->id,
                'type'        => 'debit',
                'amount'      => $request->amount,
                'reference'   => $request->reference,
                'description' => $request->description,
            ]);

            return response()->json(['success' => true, 'message' => 'Wallet debited']);
        });
    }

    // Get transaction history
    public function transactions()
    {
        $user = JWTAuth::parseToken()->authenticate();

        $wallet = Wallet::where('user_id', $user->id)->first();

        if (! $wallet) {
            return response()->json(['success' => true, 'transactions' => []]);
        }

        $transactions = $wallet->transactions()->latest()->get();

        return response()->json([
            'success' => true, 
            'message' => 'Fetch Wallet transaction history',
            'transactions' => $transactions
        ]);
    }
}
