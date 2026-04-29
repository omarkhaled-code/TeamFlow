<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sig = $request->header('Stripe-Signature');

        $event = \Stripe\Webhook::constructEvent(
            $payload,
            $sig,
            config('services.stripe.webhook_secret')
        );

        if ($event->type === 'payment_intent.succeeded') {
            $intent = $event->data->object;

            $purchase = Purchase::where('stripe_session_id', $intent->id)->first();

            if ($purchase && $purchase->status !== 'completed') {

                DB::transaction(function () use ($purchase, $intent) {

                    $purchase->update([
                        'status' => 'completed',
                        'stripe_payment_id' => $intent->id,
                    ]);

                    $purchase->user->update([
                        'plan_id' => $purchase->plan_id,
                    ]);
                });
            }
        }

        return response()->json(['status' => 'ok']);
    }
}
