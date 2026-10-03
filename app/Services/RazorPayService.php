<?php

namespace App\Services;

use Carbon\Carbon;
use DB;
use Helper;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\BillingPayment;
use App\Models\BillingTransaction;
use App\Models\Coupon;
use App\Models\Organization;
use App\Models\Subscription;
use App\Models\Team;
use App\Models\User;
use App\Traits\ConsumesExternalServices;

class RazorPayService
{
    protected $config;
    protected $razorpay;

    public function __construct()
    {
        $publicKey = \App\Models\Setting::where('key', 'razorpay_key_id')->value('value') ?? '';
        $secretKey = \App\Models\Setting::where('key', 'razorpay_secret_key')->value('value') ?? '';
        $webhookSecret = \App\Models\Setting::where('key', 'razorpay_webhook_secret')->value('value') ?? $secretKey;
        
        $this->config = [
            'public_key' => $publicKey,
            'secret_key' => $secretKey,
            'webhook_secret' => $webhookSecret,
        ];

        if (!empty($publicKey) && !empty($secretKey) && class_exists('\Razorpay\Api\Api')) {
            $this->razorpay = new \Razorpay\Api\Api($publicKey, $secretKey);
        }
    }

    public function createPlan($plan, $razorpayPlan, $razorpayAmount)
    {
        try {
            $request = $this->razorpay->plan->create([
                'period' => $interval == 'month' ? 'monthly' : 'yearly',
                'interval' => 1,
                'item' => [
                    'name' => $razorpayPlan,
                    'description' => $plan->description,
                    'amount' => $razorpayAmount,
                    'currency' => Helper::config('currency'),
                ],
            ]);
            return (object) array('success' => true, 'data' => $request);
        } catch (\Exception $e) {
            return (object) array('success' => false, 'error' => $e->getCode() . ' - ' . $e->getMessage());
        }
    }

    public function createSubscription($plan, $razorpayPlanRequest, $amount, $coupon, $taxRates, $interval)
    {
        try {
            $request = $this->razorpay->subscription->create([
                'plan_id' => $razorpayPlanRequest->id,
                'total_count' => $interval == 'month' ? 36 : 3,
                'notes' => [
                    'user' => auth()->user()->id,
                    'plan' => $plan->id,
                    'plan_amount' => $interval == 'year' ? $plan->yearly_price : $plan->monthly_price,
                    'amount' => $amount,
                    'currency' => Helper::config('currency'),
                    'interval' => $interval,
                    'coupon' => $coupon->id ?? null,
                    'tax_rates' => isset($taxRates) ?? $taxRates->pluck('id')->implode('_')
                ]
            ]);
            return (object) array('success' => true, 'data' => $request);
        } catch (\Exception $e) {
            return (object) array('success' => false, 'error' => $e->getMessage());
        }
    }

    public function handleSubscription(Request $request,$plan, $coupon, $taxRates, $amount, $interval)
    {
        $razorpayAmount = in_array($plan->currency, config('currencies.zero_decimals')) ? $amount : ($amount * 100);
        $razorpayPlan = $plan->id . '_' .$interval . '_' . $razorpayAmount . '_' . Helper::config('currency');
        $razorpayPlanQuery = $this->createPlan($plan, $razorpayPlan, $razorpayAmount);

        if($razorpayPlanQuery->success){
            $razorpayPlanQuery = $this->createSubscription($plan, $razorpayPlanQuery->data, $amount, $coupon, $taxRates, $interval);
            return redirect($razorpayPlanQuery->data->short_url);
        } else {
            \Log::error('RazorPay Plan Creation Failed', ['error' => $razorpayPlanQuery]);
            return redirect()->route('billing')->with('status', ['type' => 'error', 'message' => __('Unable to process payment with Razorpay.')]);
        }
    }

    public function cancelSubscription($plan_subscription_id)
    {
        // Attempt to cancel the current subscription
        try {
            $request = $this->razorpay->subscription->fetch($plan_subscription_id)->cancel();
            return (object) array('success' => true, 'data' => $request);
        } catch (\Exception $e) {
            return (object) array('success' => false, 'error' => $e->getCode() . ' - ' . $e->getMessage());
        }
    }

    public function handleWebhook(Request $request)
    {
        $payload = json_decode($request->getContent());

        $signature = $request->header('x-razorpay-signature');

        $computedSignature = hash_hmac('sha256', $request->getContent(), $this->config['webhook_secret']);

        // Validate the webhook signature
        if (hash_equals($computedSignature, $signature ?? '')) {
            // Get the metadata
            $metadata = $payload->payload->subscription->entity->notes ?? ($payload->payload->payment->entity->notes ?? null);

            if (isset($metadata->user)) {
                $user = User::where('id', '=', $metadata->user)->first();

                // If a user was found
                if ($user) {
                    $orgId = $metadata->organization_id ?? (Team::where('user_id', $user->id)->value('organization_id'));
                    if ($orgId) {
                        $subscription = Subscription::where('organization_id', $orgId)->first();
                        if ($subscription && isset($metadata->plan)) {
                            $subscription->plan_id = $metadata->plan;
                            $subscription->status = 'active';
                            $subscription->valid_until = Carbon::now()->addMonth();
                            $subscription->save();
                        }

                        $paymentId = $payload->payload->payment->entity->id ?? ($payload->payload->subscription->entity->id ?? null);
                        if ($paymentId && !BillingPayment::where('processor', 'razorpay')->where('details', $paymentId)->exists()) {
                            $payment = BillingPayment::create([
                                'organization_id' => $orgId,
                                'processor' => 'razorpay',
                                'details' => $paymentId,
                                'amount' => $metadata->amount ?? 0,
                            ]);

                            BillingTransaction::create([
                                'organization_id' => $orgId,
                                'entity_type' => 'payment',
                                'entity_id' => $payment->id,
                                'description' => 'Razorpay Payment',
                                'amount' => $metadata->amount ?? 0,
                                'created_by' => $user->id,
                            ]);
                        }
                    }
                }
            }
        } else {
            Log::info('Razorpay signature validation failed.');

            return response()->json([
                'status' => 400
            ], 400);
        }

        return response()->json([
            'status' => 200
        ], 200);
    }
}