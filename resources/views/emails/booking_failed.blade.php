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

        .resort-info {
            margin-top: 15px;
            font-style: italic;
        }

        .contact-info {
            margin-top: 10px;
        }

        .custom-message {
            background: #fdeaea;
            /* soft red/pink background */
            border: 1px solid #f5c2c7;
            border-left: 6px solid #d93025;
            /* Google red accent */
            padding: 25px;
            border-radius: 8px;
            margin: 20px 0;
            color: #7a1c1c;
            /* dark red text */
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

        .computer-generated {
            text-align: center;
            padding: 15px;
            margin-top: 20px;
            font-size: 12px;
            color: #999;
            border-top: 1px solid #eeeeee;
            font-style: italic;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="custom-message">
            <p>Dear {{ $firstname }},</p>

            <p>We detect that your <strong>payment has failed</strong> due to one of the following reasons:
                <em>you closed the payment page, the transaction was cancelled, the payment session expired,
                    insufficient funds, incorrect card details, or your bank declined the transaction</em>.
                Please try again to complete your reservation.
            </p>

            <p>Your reservation will not be processed until payment is successfully completed. We recommend reattempting
                your booking at your earliest convenience.</p>

            <div class="resort-info">
                <p><strong>Resort Location:</strong><br>
                    Narra Street, Brgy. Marana 3rd, Ilagan, 3300 Isabela, Philippines</p>
            </div>

            <div class="contact-info">
                <p><strong>Contact Information:</strong><br>
                    Phone: +63 995 290 1333<br>
                    Email: mtclaramuelresort@gmail.com</p>
            </div>

            <p>If you believe the payment went through but still received this message, please contact us immediately so
                we can verify your transaction.</p>

            <p>We look forward to assisting you and securing your stay at <strong>Mt. ClaRamuel Resort</strong>.</p>
        </div>

        <div class="computer-generated">
            <p>This is a computer-generated email. Please do not reply to this message.</p>
        </div>

    </div>
</body>

</html>