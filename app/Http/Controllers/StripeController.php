<?php

namespace App\Http\Controllers;

use App\Models\StripeIntegration;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Exception\ApiErrorException;

class StripeController extends Controller
{
    public function index()
    {
        $stripe = StripeIntegration::where('provider', 'stripe')
            ->where('is_active', true)
            ->first(['provider', 'test_mode', 'publishable_key', 'secret_key']);

        if (!$stripe) {
            return response()->json([
                'status' => false,
                'message' => 'Stripe configuration not found',
                'data' => null
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Stripe configuration fetched successfully',
            'data' => $stripe,
        ], 200);
    }


    public function createPaymentIntent(Request $request)
    {
        $stripe = StripeIntegration::where('provider', 'stripe')
            ->where('is_active', true)
            ->first();

        if (!$stripe) {
            return response()->json([
                'status' => false,
                'message' => 'Stripe configuration not found',
            ], 500);
        }

        $data = $request->validate([
            'amount' => 'required|numeric|min:0.5',
            'currency' => 'required|string',
            'metadata.orderId' => 'required|string',
            'metadata.userId' => 'nullable|string',
            'description' => 'nullable|string',
            'customerId' => 'nullable|string',
            'receipt_email' => 'nullable|email',
        ]);

        $amountMinor = (int) round($data['amount'] * 100);

        Stripe::setApiKey($stripe->secret_key);

        $payload = [
            'amount' => $amountMinor,
            'currency' => $data['currency'],
            'automatic_payment_methods' => ['enabled' => true],
            'metadata' => [
                'orderId' => data_get($data, 'metadata.orderId'),
                'userId' => data_get($data, 'metadata.userId'),
                'stripe_account_id' => $stripe->id 
            ],
            'description' => data_get($data, 'description'),
            'receipt_email' => data_get($data, 'receipt_email'),
        ];

        if (!empty($data['customerId'])) {
            $payload['customer'] = $data['customerId'];
        }

        $pi = PaymentIntent::create($payload);

        return response()->json([
            'status' => true,
            'client_secret' => $pi->client_secret,
            'payment_intent_id' => $pi->id
        ]);
    }
}
