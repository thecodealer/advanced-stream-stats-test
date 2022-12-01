<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\SubscriptionPlan;
use App\Models\BraintreeCustomer;
use App\Utils\BraintreeUtil;

class SubscriptionController extends Controller {
    public function index() {
        $plans = SubscriptionPlan::all();
        if ($plans->isEmpty()) {
            return view('no-result')->with(['title' => 'Subscription']);
        }
        $user = Auth::user();
        return view('subscription')->with(['plans' => $plans, 'plan' => $user->customer->plan ?? null]);
    }

    public function paymentToken() {
        try {
            $braintree = BraintreeUtil::gateway();
            $user = Auth::user();
            $braintreeCustomer = BraintreeCustomer::where('user_id', $user->id)->first();
            // create BraintreeCustomer for the user if it doesn't exist
            if (!$braintreeCustomer) {
                $nameBits = explode(' ', $user->name);
                $result = $braintree->customer()->create([
                    'email' => $user->email,
                    'firstName' => $nameBits[0] ?? $user->name,
                    'lastName' => $nameBits[1] ?? $user->name,
                ]);

                if ($result->success) {
                    $braintreeCustomer = new BraintreeCustomer();
                    $braintreeCustomer->user_id = $user->id;
                    $braintreeCustomer->customer_id = $result->customer->id;
                    $braintreeCustomer->save();
                }
            }

            // create token specifying the customerId
            $clientToken = $braintree->clientToken()->generate([
                'customerId' => $braintreeCustomer->customer_id, // this is important for using token with subscriptions
            ]);
            return response()->json(['token' => $clientToken]);
        }
        catch(\Exception $e) {
            return response()->json(['error' => 'An error occured'], 500);
        }
    }

    public function pay(Request $request) {
        try {
            $braintree = BraintreeUtil::gateway();

            $plan = SubscriptionPlan::findOrFail($request->planId);

            // create Braintree plan if it doesn't exist
            if (!$plan->external_id) {
                $result = $braintree->plan()->create([
                    'name' => $plan->name,
                    'billingFrequency' => $plan->billing_cycle === 'yearly' ? 12 : 1,
                    'currencyIsoCode' => $plan->currency,
                    'price' => $plan->price,
                ]);

                if ($result->success) {
                    $plan->external_id = $result->plan->id;
                    $plan->save();
                }
            }

            $result = $braintree->subscription()->create([
                'paymentMethodNonce' => $request->paymentMethod['nonce'] ?? null,
                'planId' => $plan->external_id,
            ]);

            if (!$result->success) {
                return response()->json(['error' => $result->message], 400);
            }

            $user = Auth::user();
            $braintreeCustomer = BraintreeCustomer::where('user_id', $user->id)->first();

            // save user subscription information
            $braintreeCustomer->plan_id = $plan->id;
            $braintreeCustomer->subscription_id = $result->subscription->id;
            $braintreeCustomer->save();

            return response()->json($result);
        }
        catch(\Exception $e) {
            return response()->json(['error' => 'An error occured'], 500);
        }
    }

    public function cancel(Request $request) {
        try {
            $braintree = BraintreeUtil::gateway();

            $user = Auth::user();
            $braintreeCustomer = BraintreeCustomer::where('user_id', $user->id)->first();

            if (!$braintreeCustomer || !$braintreeCustomer->subscription_id) {
                return response()->json(['error' => 'You do not have an active subscription'], 400);
            }

            $result = $braintree->subscription()->cancel($braintreeCustomer->subscription_id);

            if (!$result->success) {
                return response()->json(['error' => $result->message], 400);
            }

            // save user subscription information
            $braintreeCustomer->plan_id = null;
            $braintreeCustomer->subscription_id = null;
            $braintreeCustomer->save();

            return response()->json($result);
        }
        catch(\Exception $e) {
            return response()->json(['error' => 'An error occured'], 500);
        }
    }
}