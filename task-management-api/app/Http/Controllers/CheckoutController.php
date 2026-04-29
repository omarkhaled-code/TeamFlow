<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function createPaymentIntent(Request $request)
    {
        $user = $request->user();

        if ($user->plan_id == 2) {
            return response()->json(['message' => 'أنت بالفعل على خطة Pro'], 400);
        }

        Stripe::setApiKey(config('services.stripe.secret')); // ✅ use key

        $plan = \App\Models\Plan::findOrFail(2); // Pro

        $paymentIntent = PaymentIntent::create([
            'amount' => (int)($plan->price * 100),
            'currency' => 'usd',
            'metadata' => [
                'user_id' => $user->id,
                'plan_id' => $plan->id,
            ],
        ]);

        Purchase::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'amount' => $plan->price,
            'currency' => 'USD',
            'status' => 'pending',
            'stripe_session_id' => $paymentIntent->id,
        ]);

        return response()->json([
            'client_secret' => $paymentIntent->client_secret
        ]);
    }

    public function confirmPayment(Request $request)
    {
        $user = $request->user();
        $paymentIntentId = $request->payment_intent_id;

        Stripe::setApiKey(config('services.stripe.secret')); // ✅ use key

        try {
            $paymentIntent = PaymentIntent::retrieve($paymentIntentId);

            if ($paymentIntent->status === 'succeeded') {
                DB::table('purchases')
                    ->where('stripe_session_id', $paymentIntentId)
                    ->update(['status' => 'completed']);

                $user->update(['plan_id' => 2]);
                
                return response()->json(['success' => true, 'plan_id' => 2]);
            }

            return response()->json(['success' => false], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
