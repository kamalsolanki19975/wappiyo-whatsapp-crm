<?php

namespace Tests\Feature;

use App\Models\BillingInvoice;
use App\Models\BillingPayment;
use App\Models\BillingTransaction;
use App\Models\Organization;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Services\SubscriptionService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class PaymentGatewayLifecycleTest extends TestCase
{
    protected $organization;
    protected $user;
    protected $plan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::first() ?? User::factory()->create();
        $this->organization = Organization::first() ?? Organization::create([
            'name' => 'Payment Test Org',
            'slug' => 'payment-test-org-' . Str::random(5),
            'user_id' => $this->user->id,
        ]);

        $this->plan = SubscriptionPlan::first() ?? SubscriptionPlan::create([
            'name' => 'Pro Standard',
            'price' => 49.00,
            'period' => 'monthly',
            'features' => json_encode(['unlimited_contacts']),
        ]);
    }

    /**
     * Test subscription active status evaluation based on validity dates.
     */
    public function test_subscription_active_status_evaluation()
    {
        // 1. Valid future subscription is active
        $subscription = Subscription::updateOrCreate(
            ['organization_id' => $this->organization->id],
            [
                'plan_id' => $this->plan->id,
                'status' => 'active',
                'start_date' => Carbon::now()->subDays(5),
                'valid_until' => Carbon::now()->addDays(25),
            ]
        );

        $isActive = SubscriptionService::isSubscriptionActive($this->organization->id);
        $this->assertTrue($isActive, "Active subscription with future valid_until should evaluate as active.");

        // 2. Expired subscription evaluates as inactive
        $subscription->update([
            'valid_until' => Carbon::now()->subDays(1),
        ]);

        $isExpiredActive = SubscriptionService::isSubscriptionActive($this->organization->id);
        $this->assertFalse($isExpiredActive, "Expired subscription past valid_until should evaluate as inactive.");
    }

    /**
     * Test subscription full lifecycle transitions: Trial -> Active -> Renewal -> Expired.
     */
    public function test_subscription_lifecycle_transitions()
    {
        // 1. Initial Trial
        $subscription = Subscription::updateOrCreate(
            ['organization_id' => $this->organization->id],
            [
                'plan_id' => $this->plan->id,
                'status' => 'trial',
                'start_date' => Carbon::now(),
                'valid_until' => Carbon::now()->addDays(14),
            ]
        );
        $this->assertEquals('trial', $subscription->status);
        $this->assertTrue(Carbon::parse($subscription->valid_until)->isFuture());

        // 2. Payment Transition to Active via invoice creation
        $billingDetails = [
            'netAmount' => '49.00',
            'totalTaxAmount' => '0.00',
            'isTaxInclusive' => false,
            'taxRates' => [],
            'credit' => ['new' => 0],
        ];

        $invoice = SubscriptionService::createBillingInvoice(
            $billingDetails,
            $this->organization->id,
            $this->plan->id,
            $this->user->id
        );

        $this->assertInstanceOf(BillingInvoice::class, $invoice);
        $this->assertEquals(49.00, (float)$invoice->total);

        // Verify Subscription updated to active with 1 month extension
        $subscription->refresh();
        $this->assertEquals('active', $subscription->status);
        $this->assertTrue(Carbon::parse($subscription->valid_until)->isFuture());

        // Verify Billing Transaction was created
        $transaction = BillingTransaction::where('organization_id', $this->organization->id)
            ->where('entity_id', $invoice->id)
            ->first();
        $this->assertNotNull($transaction);
        $this->assertEquals(-49.00, (float)$transaction->amount);
    }

    /**
     * Test RazorPay webhook signature verification rejects invalid signatures.
     */
    public function test_razorpay_webhook_rejects_invalid_signature()
    {
        DB::table('settings')->updateOrInsert(
            ['key' => 'razorpay_webhook_secret'],
            ['value' => 'test_rzp_secret_key_123']
        );

        $payload = json_encode([
            'entity' => 'event',
            'event' => 'subscription.charged',
            'payload' => [
                'payment' => [
                    'entity' => [
                        'id' => 'pay_test_' . Str::random(8),
                        'amount' => 4900,
                    ]
                ]
            ]
        ]);

        $invalidSignature = 'invalid_sha256_hash_here';

        $response = $this->call(
            'POST',
            '/webhook/razorpay',
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X_RAZORPAY_SIGNATURE' => $invalidSignature,
            ],
            $payload
        );

        // Should be rejected with 400
        $this->assertEquals(400, $response->getStatusCode());
    }

    /**
     * Test RazorPay webhook signature verification accepts valid HMAC signature.
     */
    public function test_razorpay_webhook_accepts_valid_signature()
    {
        $secret = 'test_rzp_secret_key_123';
        DB::table('settings')->updateOrInsert(
            ['key' => 'razorpay_webhook_secret'],
            ['value' => $secret]
        );

        $payload = json_encode([
            'entity' => 'event',
            'event' => 'subscription.charged',
            'payload' => [
                'subscription' => [
                    'entity' => [
                        'notes' => [
                            'user' => $this->user->id,
                            'plan' => $this->plan->id,
                            'amount' => 49.00,
                            'currency' => 'USD',
                            'interval' => 'monthly',
                        ]
                    ]
                ],
                'payment' => [
                    'entity' => [
                        'id' => 'pay_valid_' . Str::random(8),
                    ]
                ]
            ]
        ]);

        $validSignature = hash_hmac('sha256', $payload, $secret);

        $response = $this->call(
            'POST',
            '/webhook/razorpay',
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X_RAZORPAY_SIGNATURE' => $validSignature,
            ],
            $payload
        );

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * Test Stripe webhook rejects invalid signature with 400.
     */
    public function test_stripe_webhook_rejects_invalid_signature()
    {
        $payload = json_encode([
            'id' => 'evt_test_123',
            'type' => 'checkout.session.completed',
        ]);

        $response = $this->call(
            'POST',
            '/webhook/stripe',
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_STRIPE_SIGNATURE' => 't=123456,v1=invalid_signature',
            ],
            $payload
        );

        $this->assertEquals(400, $response->getStatusCode());
    }
}
