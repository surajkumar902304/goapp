<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Models\Order;
use App\Models\Payment;
use Stripe\Stripe;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;
class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sig = $request->header('Stripe-Signature');
        $secret = config('services.stripe.webhook_secret'); // STRIPE_WEBHOOK_SECRET

        try {
            $event = Webhook::constructEvent($payload, $sig, $secret);
        } catch (\Throwable $e) {
            Log::warning('Stripe webhook signature verification failed', ['err' => $e->getMessage()]);
            return response()->json(['error' => 'Bad signature'], 400);
        }

        switch ($event->type) {
            case 'payment_intent.succeeded':
            case 'payment_intent.payment_failed':
            case 'payment_intent.processing':
            case 'payment_intent.canceled':
            case 'payment_intent.requires_action':
                $pi = $event->data->object;

                // Metadata we set on creation
                $orderId = data_get($pi, 'metadata.orderId');
                $userId  = data_get($pi, 'metadata.userId');

                // Find order
                $order = Order::where('order_id', $orderId)->first();
                if (!$order) {
                    Log::warning('Order not found for PI', ['orderId' => $orderId, 'pi' => $pi->id]);
                    return response()->json(['ok' => true]); // Don't retry
                }

                // Store or update payment row
                Payment::updateOrCreate(
                    ['payment_intent_id' => $pi->id],
                    [
                        'order_id'         => $order->order_id,
                        'user_id'          => $userId ?: $order->user_id,
                        'provider'         => 'stripe',
                        'payment_method_id'=> data_get($pi, 'payment_method'),
                        'customer_id'      => data_get($pi, 'customer'),
                        'currency'         => data_get($pi, 'currency'),
                        'amount'           => (int) data_get($pi, 'amount'), // already in smallest unit
                        'status'           => data_get($pi, 'status'),
                        'receipt_email'    => data_get($pi, 'receipt_email'),
                        'description'      => data_get($pi, 'description'),
                        'metadata'         => $pi->metadata ? $pi->metadata->toArray() : null,
                        'raw_payload'      => json_decode(json_encode($pi), true),
                    ]
                );

                // Update order flags
                $order->payment_provider  = 'stripe';
                $order->payment_reference = $pi->id;

                if ($pi->status === 'succeeded') {
                    $order->payment_status = 'paid';
                    // If you also use your own 'status' column for order lifecycle:
                    // $order->status = 'paid';
                } elseif ($pi->status === 'processing') {
                    $order->payment_status = 'processing';
                } elseif ($pi->status === 'requires_payment_method' || $pi->status === 'requires_action') {
                    $order->payment_status = 'unpaid';
                } elseif ($pi->status === 'canceled') {
                    $order->payment_status = 'failed';
                } elseif ($pi->status === 'requires_capture') {
                    // if you do manual capture flows
                    $order->payment_status = 'authorized';
                } else {
                    $order->payment_status = $pi->status; // fallback
                }

                $order->save();
                break;

            default:
                // ignore other event types
                break;
        }

        return response()->json(['ok' => true]);
    }
}
