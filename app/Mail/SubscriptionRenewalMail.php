<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SubscriptionRenewalMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $clientName;
    public string $planName;
    public string $renewalDate;
    public int $daysRemaining;
    public string $billingUrl;

    public function __construct(string $clientName, string $planName, string $renewalDate, int $daysRemaining, string $billingUrl = '/billing')
    {
        $this->clientName = $clientName;
        $this->planName = $planName;
        $this->renewalDate = $renewalDate;
        $this->daysRemaining = $daysRemaining;
        $this->billingUrl = url($billingUrl);
    }

    public function build()
    {
        return $this->subject(__('Action Required: Your Wappiyo Subscription Renews Soon'))
            ->view('emails.subscription_renewal');
    }
}
