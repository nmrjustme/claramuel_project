<!DOCTYPE html>
<html>
<head>
    <title>Your OTP Code</title>
</head>
<body>
    <h2>OTP Verification Required</h2>
    
    <p>Hello {{ $booking['firstname'] }},</p>

    <p>Your booking details:</p>
    
    <ul>
        <li>Check-in: {{ $booking['checkin_date'] }}</li>
        <li>Check-out: {{ $booking['checkout_date'] }}</li>
    </ul>

    <p>Your OTP verification code is:</p>
    
    <h3 style="font-size: 24px; color: #007bff; margin: 20px 0;">{{ $otp }}</h3>
    
    <p>Enter this code on the verification page to proceed with your payment.</p>
    
    <p><strong>This OTP will expire in 30 minutes.</strong></p>

    <p>If you didn't make this booking, please ignore this email.</p>
</body>
</html>