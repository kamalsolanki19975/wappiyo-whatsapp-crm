<!DOCTYPE html>
<html lang="en" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta charset="utf-8">
    <meta name="x-apple-disable-message-reformatting">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="format-detection" content="telephone=no, date=no, address=no, email=no">
    <title>Verify Your Mail ID — Wappiyo</title>
    <!--[if mso]>
    <xml><o:OfficeDocumentSettings><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml>
    <style>
      body, table, td, p, a, li, blockquote {font-family: Arial, sans-serif !important;}
    </style>
    <![endif]-->
    <style>
        body {
            margin: 0;
            padding: 0;
            width: 100% !important;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
            background-color: #F8FAFC;
        }
        @media only screen and (max-width: 600px) {
            .container-table {
                width: 100% !important;
                border-radius: 0 !important;
            }
            .content-cell {
                padding: 24px 16px !important;
            }
            .otp-box {
                font-size: 28px !important;
                letter-spacing: 6px !important;
                padding: 12px 16px !important;
            }
        }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #F8FAFC; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #1E293B;">
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #F8FAFC; padding: 32px 12px;">
        <tr>
            <td align="center">
                <!-- Outer Container -->
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" class="container-table" style="max-width: 540px; background-color: #FFFFFF; border-radius: 16px; border: 1px solid #E2E8F0; overflow: hidden; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);">
                    <!-- Brand Top Accent Bar -->
                    <tr>
                        <td height="5" style="background: linear-gradient(90deg, #22C55E 0%, #10B981 50%, #06B6D4 100%); line-height: 5px; font-size: 5px;">&nbsp;</td>
                    </tr>

                    <!-- Header -->
                    <tr>
                        <td align="center" style="padding: 28px 24px 20px 24px; border-bottom: 1px solid #F1F5F9;">
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center">
                                        <div style="font-size: 22px; font-weight: 800; color: #0F172A; letter-spacing: -0.5px;">
                                            <span style="color: #22C55E;">Wapp</span><span style="color: #0F172A;">iyo</span>
                                        </div>
                                        <div style="font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 1.5px; color: #64748B; margin-top: 2px;">
                                            WhatsApp CRM & Platform
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td class="content-cell" style="padding: 32px 32px 28px 32px;">
                            <h1 style="margin: 0 0 12px 0; font-size: 20px; font-weight: 800; color: #0F172A; text-align: center; letter-spacing: -0.3px;">
                                Verify Your Mail ID
                            </h1>
                            <p style="margin: 0 0 20px 0; font-size: 14px; line-height: 1.6; color: #475569; text-align: center;">
                                Hello <strong>{{ $name }}</strong>, welcome to Wappiyo! To complete your registration and activate your workspace, please use the 6-digit verification code below:
                            </p>

                            <!-- OTP Box -->
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin: 24px 0;">
                                <tr>
                                    <td align="center">
                                        <div class="otp-box" style="display: inline-block; font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace; font-size: 34px; font-weight: 800; letter-spacing: 8px; color: #15803D; background-color: #F0FDF4; border: 2px dashed #86EFAC; border-radius: 12px; padding: 14px 28px; text-align: center;">
                                            {{ $otp }}
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 16px 0 8px 0; font-size: 13px; line-height: 1.5; color: #64748B; text-align: center;">
                                ⏱️ This code expires in <strong>{{ $expiryMinutes }} minutes</strong>.
                            </p>

                            <!-- Security Alert Box -->
                            <div style="margin-top: 24px; padding: 14px 16px; background-color: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 10px; font-size: 12px; line-height: 1.5; color: #64748B;">
                                🔒 <strong>Security Tip:</strong> Wappiyo will never ask for your verification code via phone, WhatsApp, or unsolicited message. If you did not create this account, you can safely ignore this email.
                            </div>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td align="center" style="padding: 20px 24px 24px 24px; background-color: #F8FAFC; border-top: 1px solid #F1F5F9; font-size: 11px; line-height: 1.6; color: #94A3B8;">
                            &copy; {{ date('Y') }} Wappiyo Inc. All rights reserved.<br>
                            Need assistance? Contact us at <a href="mailto:support@wappiyo.com" style="color: #22C55E; text-decoration: none;">support@wappiyo.com</a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
