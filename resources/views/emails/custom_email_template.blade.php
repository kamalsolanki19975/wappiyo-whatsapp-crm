<!DOCTYPE html>
<html lang="en" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta charset="utf-8">
    <meta name="x-apple-disable-message-reformatting">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="format-detection" content="telephone=no, date=no, address=no, email=no">
    <title>Wappiyo</title>
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
        img {
            border: 0;
            outline: none;
            text-decoration: none;
            -ms-interpolation-mode: bicubic;
        }
        a {
            color: #22C55E;
            text-decoration: underline;
        }
        .btn-brand {
            background-color: #22C55E;
            color: #FFFFFF !important;
            display: inline-block;
            font-weight: 600;
            padding: 12px 28px;
            text-decoration: none;
            border-radius: 8px;
        }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #F8FAFC; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #F8FAFC; padding: 40px 16px;">
        <tr>
            <td align="center">
                <!-- Outer Container -->
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 580px; background-color: #FFFFFF; border-radius: 16px; border: 1px solid #E2E8F0; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);">
                    <!-- Brand Top Color Accent Bar -->
                    <tr>
                        <td height="4" style="background: linear-gradient(90deg, #22C55E 0%, #10B981 50%, #022828 100%); line-height: 4px; font-size: 4px;">&nbsp;</td>
                    </tr>
                    
                    <!-- Header with Official Wappiyo Logo -->
                    <tr>
                        <td align="center" style="padding: 32px 32px 24px 32px; border-bottom: 1px solid #F1F5F9;">
                            <a href="{{ config('app.url') }}" target="_blank" style="text-decoration: none; display: inline-block;">
                                <img src="{{ config('app.url') }}/images/logo.png" alt="Wappiyo" width="160" style="display: block; width: 160px; height: auto; max-height: 44px; border: 0;" />
                            </a>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 32px; font-size: 15px; line-height: 24px; color: #334155;">
                            {!! $body !!}
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding: 24px 32px; background-color: #F8FAFC; border-top: 1px solid #F1F5F9; text-align: center; font-size: 12px; line-height: 18px; color: #64748B;">
                            <p style="margin: 0 0 8px 0; font-weight: 600; color: #022828;">
                                Wappiyo — WhatsApp Marketing & CRM Automation
                            </p>
                            <p style="margin: 0 0 12px 0;">
                                You are receiving this notification from your Wappiyo account.
                            </p>
                            <p style="margin: 0; color: #94A3B8;">
                                &copy; {{ date('Y') }} Wappiyo. All rights reserved.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>