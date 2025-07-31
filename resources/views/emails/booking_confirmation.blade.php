<!DOCTYPE html>
<html>
<head>
    <title>Booking Confirmation - Mt. Claramuel</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style type="text/css">
        /* Client-specific fixes */
        .ExternalClass { width:100%; }
        .ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div { line-height: 100%; }
        /* iOS BLUE LINKS */
        a[x-apple-data-detectors] {
            color: inherit !important;
            text-decoration: none !important;
            font-size: inherit !important;
            font-family: inherit !important;
            font-weight: inherit !important;
            line-height: inherit !important;
        }
    </style>
</head>
<body style="margin:0; padding:0; -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%; background-color:#f9fafb; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #374151; line-height: 1.5;">
    <!--[if mso]>
    <style type="text/css">
    body, table, td {font-family: Arial, sans-serif !important;}
    </style>
    <![endif]-->
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
        <tr>
            <td align="center" style="padding:20px;">
                <!--[if (gte mso 9)|(IE)]>
                <table width="600" align="center" cellspacing="0" cellpadding="0" border="0">
                <tr>
                <td>
                <![endif]-->
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width:600px; margin:0 auto; background-color:white; border-radius:12px; box-shadow:0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); overflow:hidden;">
                    <tr>
                        <td style="padding:32px;">
                            <!-- Header -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td align="center" style="padding-bottom:24px;">
                                        <h1 style="font-size:30px; font-weight:700; color:#dc2626; margin:0;">Mt. Claramuel</h1>
                                        <div style="width:80px; height:4px; background-color:#dc2626; margin:8px auto 0; border-radius:9999px;"></div>
                                    </td>
                                </tr>
                            </table>

                            <!-- Main Content -->
                            <h1 style="font-size:24px; font-weight:700; color:#1f2937; margin:0 0 16px 0;">Booking Confirmation</h1>
                            <p style="margin:0 0 24px 0;">Dear {{ $booking->user->firstname }},</p>
                            
                            <p style="margin:0 0 24px 0;">Your booking request has been confirmed!</p>

                            @if($customMessage)
                            <p style="margin:0 0 24px 0;">{{ $customMessage }}</p>
                            @endif

                            <!-- Booking Details -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#f9fafb; padding:24px; border-radius:8px; margin-bottom:32px; border:1px solid #e5e7eb;">
                                <tr>
                                    <td style="padding-bottom:16px;">
                                        <h2 style="font-size:18px; font-weight:600; margin:0; color:#1f2937; display:flex; align-items:center;">
                                            <svg xmlns="http://www.w3.org/2000/svg" style="height:20px; width:20px; margin-right:8px; color:#dc2626;" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                                            </svg>
                                            Booking Details
                                        </h2>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                            <tr>
                                                <td style="padding-bottom:12px; display:flex; justify-content:space-between;">
                                                    <span style="color:#4b5563;">Status:</span>
                                                    <span style="font-weight:500; color:#10b981;">Confirmed</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="display:flex; justify-content:space-between;">
                                                    <span style="color:#4b5563;">Confirmed at:</span>
                                                    <span style="font-weight:500;">{{ $booking->confirmed_at->format('F j, Y g:i a') }}</span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- CTA Button -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin:32px 0; text-align:center;">
                                <tr>
                                    <td style="padding-bottom:24px;">
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center">
                                            <tr>
                                                <td style="border-radius:8px; background:linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);">
                                                    <a href="{{ $verificationUrl }}" style="display:inline-block; padding:16px 32px; color:white; font-weight:700; border-radius:8px; font-size:16px; text-decoration:none;">VERIFY EMAIL & PROCEED TO PAYMENT</a>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                
                                <!-- Backup Payment Section -->
                                <tr>
                                    <td style="padding-bottom:24px;">
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#f9fafb; padding:20px; border-radius:8px; border:1px solid #e5e7eb;">
                                            <tr>
                                                <td align="center" style="padding-bottom:12px;">
                                                    <h3 style="font-weight:600; color:#1f2937; margin:0; display:flex; align-items:center; justify-content:center;">
                                                        <svg xmlns="http://www.w3.org/2000/svg" style="height:20px; width:20px; margin-right:8px; color:#dc2626;" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                                        </svg>
                                                        Payment Upload Options
                                                    </h3>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td align="center" style="padding-bottom:12px;">
                                                    <p style="font-size:14px; color:#4b5563; margin:0; text-align:justify;">In case you were unable to upload your proof of payment earlier, you may use the link below to upload it anytime. <b>But please make sure to verify your email first by clicking the 'Verify Email & Proceed to Payment' button above.</b></p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td align="center">
                                                    <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                                                        <tr>
                                                            <td align="center" style="padding-bottom:8px;">
                                                                <a href="https://mtclaramuel.com/payment/create/{{ $booking->id }}" style="display:inline-block; padding:8px 16px; color:#dc2626; font-weight:500; border-radius:4px; text-decoration:none; border-left:3px solid #dc2626;">➤ Go Directly to Payment Upload Page</a>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center">
                                                                <p style="font-size:12px; color:#6b7280; margin:0; text-align:center;">Or copy this link: <span style="font-family:monospace; word-break:break-all; font-weight:700;">https://mtclaramuel.com/payment/create/{{ $booking->id }}</span></p>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Footer -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="padding-top:24px; border-top:1px solid #e5e7eb;">
                                <tr>
                                    <td style="padding-bottom:8px;">
                                        <p style="color:#4b5563; margin:0;">Thanks,</p>
                                        <p style="font-weight:500; color:#1f2937; margin:0;">The Mt. Claramuel Team</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding-top:24px;">
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#eff6ff; padding:16px; border-radius:8px;">
                                            <tr>
                                                <td>
                                                    <h3 style="font-weight:500; color:#1e40af; margin:0 0 8px 0;">Need Help?</h3>
                                                    <p style="font-size:14px; color:#1d4ed8; margin:0;">If you have any questions, please contact our support team.</p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
                <!--[if (gte mso 9)|(IE)]>
                </td>
                </tr>
                </table>
                <![endif]-->
            </td>
        </tr>
    </table>
</body>
</html>