<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your reservation request has been received in mtclaramuel</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            text-align: center;
            padding: 20px 0;
            border-bottom: 1px solid #eeeeee;
        }

        .content {
            padding: 20px 0;
        }

        .receipt {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }

        .qr-code {
            text-align: center;
            margin: 25px 0;
            padding: 15px;
            background: white;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
        }

        .qr-code img {
            width: 200px;
            height: 200px;
            display: block;
            margin: 0 auto 15px;
        }

        .detail-row {
            display: flex;
            margin-bottom: 10px;
        }

        .detail-label {
            font-weight: bold;
            width: 120px;
        }

        .detail-value {
            flex: 1;
        }

        .resort-info {
            margin-top: 15px;
            font-style: italic;
        }

        .contact-info {
            margin-top: 10px;
        }

        .custom-message {
            background: #e6f0ff;
            /* light blue background */
            border: 1px solid #b5d0f0;
            /* soft blue border */
            border-left: 6px solid #4287f4;
            /* bold blue accent */
            padding: 25px;
            border-radius: 8px;
            margin: 20px 0;
            color: #003c7a;
            /* dark blue text */
            position: relative;
            overflow: hidden;
        }


        .custom-message p {
            margin: 0 0 15px 0;
            line-height: 1.6;
        }

        .custom-message:before {
            content: "";
            position: absolute;
            top: 0;
            right: 0;
            width: 100px;
            height: 100px;
            background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23b5dfc0' width='100px' height='100px'%3E%3Cpath d='M0 0h24v24H0z' fill='none'/%3E%3Cpath d='M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z'/%3E%3C/svg%3E") no-repeat;
            background-size: contain;
            opacity: 0.2;
            transform: translate(30px, -30px);
        }

        .confirmation-title {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 15px;
            color: #2e7d32;
            display: flex;
            align-items: center;
        }

        .confirmation-title:before {
            content: "✓";
            display: inline-block;
            width: 28px;
            height: 28px;
            background: #34a853;
            color: white;
            border-radius: 50%;
            text-align: center;
            line-height: 28px;
            margin-right: 10px;
            font-size: 16px;
        }

        .footer {
            text-align: center;
            padding: 20px 0;
            border-top: 1px solid #eeeeee;
            font-size: 14px;
            color: #777777;
        }

        .pdf-notice {
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            color: #0d47a1;
        }

        .pdf-notice h3 {
            margin-top: 0;
            display: flex;
            align-items: center;
        }

        .pdf-notice h3:before {
            content: "📄";
            margin-right: 10px;
            font-size: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="custom-message">
            <p>Dear {{ $booking->user->firstname }},</p>

            <p>Your reservation request has been <strong>successfully received</strong>. We’re pleased to inform you
                that a confirmation will be sent shortly to your <strong>registered email address</strong> and
                <strong>mobile number</strong>.</p>
            <div class="resort-info">
                <p><strong>Resort Location:</strong><br>
                    Narra Street, Brgy. Marana 3rd, Ilagan, 3300 Isabela, Philippines</p>
            </div>

            <div class="contact-info">
                <p><strong>Contact Information:</strong><br>
                    Phone: +63 995 290 1333<br>
                    Email: mtclaramuelresort@gmail.com</p>
            </div>

            <p>If you have any questions, special requests, or need further assistance, please don’t hesitate to contact
                us. We want your stay with us to be both <strong>memorable</strong> and <strong>comfortable</strong>.
            </p>

            <p>Thank you for choosing <strong>Mt. ClaRamuel Resort</strong></p>
        </div>

        <!-- PDF Notice Section -->
        <div class="pdf-notice">
            <h3>Official Invoice Attached</h3>
            <p>Your official invoice has been generated and attached to this email as a PDF file.</p>
            <p><strong>File Name:</strong> invoice_{{ $booking->code }}.pdf</p>
            <p>Please keep this invoice for your records and present it during check-in if requested.</p>
        </div>

        <div class="footer">
            <p>Thank you for choosing Mt. ClaRamuel Resort!</p>
        </div>
    </div>
</body>

</html>