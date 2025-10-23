<?php

namespace App\Http\Controllers;

use App\Models\StripeIntegration;
use App\Models\Payment;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Webhook;
use Stripe\Stripe;

class StripeWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $stripeAccount = StripeIntegration::where('provider', 'stripe')
            ->where('is_active', true)
            ->first();

        if (!$stripeAccount) {
            Log::error("Stripe Webhook Error: No active Stripe account found");
            return response()->json(['error' => 'No active Stripe account configured'], 400);
        }

        $webhookSecret = $stripeAccount->webhook_secret; 

        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        try {
            Stripe::setApiKey($stripeAccount->secret_key);

            $event = Webhook::constructEvent(
                $payload,
                $sigHeader,
                $webhookSecret
            );

            Log::info('Stripe Webhook Received', ['type' => $event->type]);

            switch ($event->type) {
                case 'payment_intent.succeeded':
                    $this->updatePaymentStatus($event->data->object, 'succeeded');
                    break;

                case 'payment_intent.payment_failed':
                    $this->updatePaymentStatus($event->data->object, 'failed');
                    break;
            }

            return response()->json(['status' => 'success'], 200);

        } catch (\Exception $e) {
            Log::error("Stripe Webhook Error: " . $e->getMessage());
            return response()->json(['error' => 'Webhook error'], 400);
        }
    }

    private function updatePaymentStatus($paymentIntent, $status)
    {
        $payment = Payment::where('payment_intent_id', $paymentIntent->id)->first();

        if ($payment) {
            $payment->status = $status;
            $payment->save();

            Order::where('order_id', $payment->order_id)
                ->update(['payment_status' => $status]);

            Log::info("Payment & Order Updated", [
                'order_id' => $payment->order_id,
                'status' => $status
            ]);
        }
    }
}
