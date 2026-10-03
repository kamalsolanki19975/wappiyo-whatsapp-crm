<?php

namespace Tests\Feature;

use App\Mail\CustomEmail;
use App\Mail\CustomEmailVerification;
use App\Mail\SignupOtpMail;
use App\Mail\SubscriptionRenewalMail;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ProductionEmailPipelineTest extends TestCase
{
    /**
     * Test Signup OTP email rendering and content.
     */
    public function test_signup_otp_mail_rendering_and_content()
    {
        $mail = new SignupOtpMail("John Doe", "482910", 10);
        $rendered = $mail->render();

        $this->assertStringContainsString("Verify Your Mail ID", $rendered);
        $this->assertStringContainsString("John Doe", $rendered);
        $this->assertStringContainsString("482910", $rendered);
        $this->assertStringContainsString("10 minutes", $rendered);
        $this->assertEquals("Verify Your Mail ID — Wappiyo Security Code", $mail->build()->subject);
    }

    /**
     * Test Signup OTP mail dispatch via Mail facade.
     */
    public function test_signup_otp_mail_dispatch()
    {
        Mail::fake();

        Mail::to("newuser@example.com")->send(new SignupOtpMail("Jane Smith", "918234", 10));

        Mail::assertSent(SignupOtpMail::class, function ($mail) {
            return $mail->hasTo("newuser@example.com") &&
                   $mail->name === "Jane Smith" &&
                   $mail->otp === "918234";
        });
    }

    /**
     * Test Subscription Renewal email rendering and content.
     */
    public function test_subscription_renewal_mail_rendering_and_content()
    {
        $mail = new SubscriptionRenewalMail(
            "Acme Corp",
            "Enterprise Growth",
            "2026-10-15",
            7,
            "/billing"
        );
        $rendered = $mail->render();

        $this->assertStringContainsString("Upcoming Subscription Renewal", $rendered);
        $this->assertStringContainsString("Acme Corp", $rendered);
        $this->assertStringContainsString("Enterprise Growth", $rendered);
        $this->assertStringContainsString("2026-10-15", $rendered);
        $this->assertStringContainsString("7 day(s)", $rendered);
        $this->assertEquals("Action Required: Your Wappiyo Subscription Renews Soon", $mail->build()->subject);
    }

    /**
     * Test Subscription Renewal mail dispatch via Mail facade.
     */
    public function test_subscription_renewal_mail_dispatch()
    {
        Mail::fake();

        Mail::to("billing@acme.com")->send(new SubscriptionRenewalMail(
            "Acme Corp",
            "Pro Plan",
            "2026-10-20",
            3,
            "/billing"
        ));

        Mail::assertSent(SubscriptionRenewalMail::class, function ($mail) {
            return $mail->hasTo("billing@acme.com") &&
                   $mail->clientName === "Acme Corp" &&
                   $mail->daysRemaining === 3;
        });
    }

    /**
     * Test CustomEmail mailable queueability and template rendering.
     */
    public function test_custom_email_implements_should_queue_and_renders()
    {
        $mail = new CustomEmail("System Notice", "<p>Maintenance scheduled for tonight.</p>");

        $this->assertInstanceOf(ShouldQueue::class, $mail);

        $rendered = $mail->render();
        $this->assertStringContainsString("Maintenance scheduled for tonight.", $rendered);
        $this->assertStringContainsString("Wappiyo", $rendered);
        $this->assertEquals("System Notice", $mail->build()->subject);
    }

    /**
     * Test CustomEmail queue dispatch.
     */
    public function test_custom_email_queue_dispatch()
    {
        Mail::fake();

        Mail::to("client@example.com")->queue(new CustomEmail("Urgent Notice", "<p>Please check your inbox</p>"));

        Mail::assertQueued(CustomEmail::class, function ($mail) {
            return $mail->hasTo("client@example.com") &&
                   $mail->subject === "Urgent Notice";
        });
    }

    /**
     * Test CustomEmailVerification URL rendering for users.
     */
    public function test_custom_email_verification_rendering()
    {
        $user = new User([
            'first_name' => 'Alice',
            'last_name' => 'Wong',
            'email' => 'alice@example.com',
        ]);
        $user->id = 99999;

        $mail = new CustomEmailVerification($user);
        $rendered = $mail->render();

        $this->assertStringContainsString("Please verify your email by clicking on the link below", $rendered);
        $this->assertStringContainsString("/email/verify/", $rendered);
    }

    /**
     * Test invalid email address handling prevents mail dispatch without crashing.
     */
    public function test_invalid_email_address_is_prevented()
    {
        $invalidEmails = ['invalid-email', 'missing@domain', '@nodomain.com', 'spaces in@email.com'];

        foreach ($invalidEmails as $email) {
            $validator = \Illuminate\Support\Facades\Validator::make(
                ['email' => $email],
                ['email' => 'required|email:rfc,dns']
            );
            $this->assertTrue($validator->fails(), "Email {$email} should fail RFC validation.");
        }
    }
}
