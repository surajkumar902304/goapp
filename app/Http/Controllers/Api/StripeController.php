<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Exception\ApiErrorException;

class StripeController extends Controller
{
    public function createPaymentIntent(Request $request)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:0.5',  // amount as a major unit (e.g. GBP pounds)
            'currency' => 'required|string',
            'automatic_payment_methods.enabled' => 'required|boolean',
            'metadata.orderId' => 'required|string',
            'metadata.userId' => 'nullable|string',
            'description' => 'nullable|string',
            'customerId' => 'nullable|string',
            'receipt_email' => 'nullable|email',
        ]);

        // Convert major units to the smallest unit (e.g. pounds -> pence)
        $amountMinor = (int) round($data['amount'] * 100);

        Stripe::setApiKey(config('services.stripe.secret'));

        $payload = [
            'amount' => $amountMinor,
            'currency' => $data['currency'],
            'automatic_payment_methods' => [
                'enabled' => (bool) data_get($data, 'automatic_payment_methods.enabled'),
            ],
            'metadata' => [
                'orderId' => data_get($data, 'metadata.orderId'),
                'userId'  => data_get($data, 'metadata.userId'),
            ],
            'description' => data_get($data, 'description'),
            'receipt_email' => data_get($data, 'receipt_email'),
        ];

        if (!empty($data['customerId'])) {
            $payload['customer'] = $data['customerId'];
        }

        $pi = PaymentIntent::create($payload);

        return response()->json([
            'client_secret' => $pi->client_secret,
        ]);
    }
}
