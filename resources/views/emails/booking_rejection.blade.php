<!DOCTYPE html>
<html>
<head>
    <title>Booking Rejection - Mt. Claramuel</title>
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
                            <h1 style="font-size:24px; font-weight:700; color:#1f2937; margin:0 0 16px 0;">Booking Rejection Notice</h1>
                            <p style="margin:0 0 24px 0;">Dear {{ $booking->user->firstname }},</p>
                            
                            <p style="margin:0 0 24px 0;">We regret to inform you that your booking request (Reference #{{ $booking->reference }}) has been rejected.</p>

                            @if($customMessage)
                            <p style="margin:0 0 24px 0; color:#dc2626; font-weight:500;">Reason: {{ $customMessage }}</p>
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
                                                    <span style="font-weight:500; color:#dc2626;">Rejected</span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Next Steps -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin:32px 0; text-align:center;">
                                <tr>
                                    <td style="padding-bottom:24px;">
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#f9fafb; padding:20px; border-radius:8px; border:1px solid #e5e7eb;">
                                            <tr>
                                                <td align="center" style="padding-bottom:12px;">
                                                    <h3 style="font-weight:600; color:#1f2937; margin:0; display:flex; align-items:center; justify-content:center;">
                                                        <svg xmlns="http://www.w3.org/2000/svg" style="height:20px; width:20px; margin-right:8px; color:#dc2626;" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2h-1V9z" clip-rule="evenodd" />
                                                        </svg>
                                                        Next Steps
                                                    </h3>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td align="center" style="padding-bottom:12px;">
                                                    <p style="font-size:14px; color:#4b5563; margin:0; text-align:justify;">If you believe this was a mistake or would like to discuss alternative options, please contact our support team.</p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td align="center">
                                                    <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                                                        <tr>
                                                            <td align="center" style="padding-bottom:8px;">
                                                                <a href="https://mtclaramuel.com/contact" style="display:inline-block; padding:8px 16px; color:#dc2626; font-weight:500; border-radius:4px; text-decoration:none; border-left:3px solid #dc2626;">➤ Contact Support</a>
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
                                        <p style="color:#4b5563; margin:0;">Sincerely,</p>
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