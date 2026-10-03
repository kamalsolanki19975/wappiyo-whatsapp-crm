<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscription Renewal Notice</title>
    <style>
        body { margin: 0; padding: 0; background-color: #f8fafc; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; }
        .wrapper { width: 100%; table-layout: fixed; background-color: #f8fafc; padding: 40px 0; }
        .container { max-width: 560px; margin: 0 auto; background-color: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }
        .header { background: linear-gradient(135deg, #022828 0%, #064E3B 100%); padding: 32px 30px; text-align: center; }
        .logo-text { font-size: 24px; font-weight: 800; color: #ffffff; letter-spacing: -0.5px; }
        .content { padding: 36px 32px; color: #334155; line-height: 1.6; }
        .title { font-size: 20px; font-weight: 700; color: #0f172a; margin-top: 0; margin-bottom: 12px; }
        .plan-box { background-color: #f1f5f9; border-radius: 12px; padding: 20px; margin: 24px 0; border-left: 4px solid #10B981; }
        .plan-row { display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 14px; }
        .cta-btn { display: inline-block; padding: 14px 28px; background: linear-gradient(135deg, #22C55E 0%, #16A34A 100%); color: #ffffff !important; text-decoration: none; font-weight: 700; font-size: 14px; border-radius: 10px; margin: 20px 0; }
        .footer { padding: 24px 32px; background-color: #f8fafc; border-top: 1px solid #e2e8f0; font-size: 12px; color: #64748b; text-align: center; }
        @media (prefers-color-scheme: dark) {
            .container { background-color: #111113 !important; border-color: #27272a !important; }
            .content { color: #d4d4d8 !important; }
            .title { color: #ffffff !important; }
            .plan-box { background-color: #18181b !important; }
            .footer { background-color: #09090b !important; border-color: #27272a !important; color: #71717a !important; }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <div class="logo-text">Wappiyo</div>
            </div>
            <div class="content">
                <h1 class="title">Upcoming Subscription Renewal</h1>
                <p>Hello <strong>{{ $clientName }}</strong>,</p>
                <p>This is a friendly reminder that your <strong>{{ $planName }}</strong> subscription for Wappiyo is scheduled for renewal in <strong>{{ $daysRemaining }} day(s)</strong>.</p>
                
                <div class="plan-box">
                    <div style="font-size: 13px; font-weight: 600; color: #64748b; margin-bottom: 10px;">SUBSCRIPTION SUMMARY</div>
                    <div style="font-size: 15px; font-weight: 700; color: #0f172a; margin-bottom: 6px;">Plan: {{ $planName }}</div>
                    <div style="font-size: 14px; color: #334155; margin-bottom: 4px;">Renewal Date: <strong>{{ $renewalDate }}</strong></div>
                    <div style="font-size: 14px; color: #10B981; font-weight: 600;">Status: Active / Approaching Renewal</div>
                </div>

                <p>To avoid any disruption to your live WhatsApp automated workflows, multi-agent chat queues, and broadcast campaigns, please verify your payment method in your billing portal.</p>

                <div style="text-align: center;">
                    <a href="{{ $billingUrl }}" class="cta-btn">Manage Subscription & Billing &rarr;</a>
                </div>

                <p style="font-size: 13px; color: #64748b; margin-top: 24px;">Need assistance or have questions regarding your plan? Reply directly to this email or reach our support team at any time.</p>
            </div>
            <div class="footer">
                &copy; {{ date('Y') }} Wappiyo. All rights reserved.<br>
                Official Meta WhatsApp Cloud API Partner.
            </div>
        </div>
    </div>
</body>
</html>
