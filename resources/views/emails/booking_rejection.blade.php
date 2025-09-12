<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Reservation Request Was Declined - {{ $booking->code }}</title>
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
        .custom-message {
            background: #fdecea; /* soft red for rejection */
            border: 1px solid #f5c2c0;
            border-left: 6px solid #d93025; /* red accent */
            padding: 25px;
            border-radius: 8px;
            margin: 20px 0;
            color: #611a15;
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
            background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='%23f5c2c0' viewBox='0 0 24 24'%3E%3Cpath d='M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm5 13.59L15.59 17 12 13.41 8.41 17 7 15.59 10.59 12 7 8.41 8.41 7 12 10.59 15.59 7 17 8.41 13.41 12 17 15.59z'/%3E%3C/svg%3E") no-repeat;
            background-size: contain;
            opacity: 0.2;
            transform: translate(30px, -30px);
        }
        
        .rejection-title {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 15px;
            color: #d93025;
            display: flex;
            align-items: center;
        }
        
        .rejection-title:before {
            content: "✕";
            display: inline-block;
            width: 28px;
            height: 28px;
            background: #d93025;
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
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Your Reservation Was Declined</h1>
            <p>Reservation Code: {{ $booking->code }}</p>
        </div>
        
        <div class="custom-message">
            <div class="rejection-title">Reservation Declined</div>
            <p>Dear {{ $booking->user->firstname }},</p>
            <p>We regret to inform you that your reservation request has been <strong>declined</strong>. Unfortunately, we are unable to accommodate your booking at this time.</p>
            
            @if($customMessage)
                <p>{{ $customMessage }}</p>
            @endif
            
            <p>Please feel free to contact us for assistance or to discuss alternative dates and options.</p>
            
            <div class="contact-info">
                <p><strong>Contact Information:</strong><br>
                Phone: +63 995 290 1333<br>
                Email: mtclaramuelresort@gmail.com</p>
            </div>
        </div>
        <div class="footer">
            <p>We apologize for the inconvenience and hope to welcome you another time.</p>
        </div>
    </div>
</body>
</html>
