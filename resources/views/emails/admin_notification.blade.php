<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <title>New Booking Notification</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f9fafb;
                color: #333;
                padding: 20px;
            }
            .container {
                background-color: white;
                border-radius: 8px;
                padding: 20px;
                max-width: 500px;
                margin: auto;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            }
            h2 {
                color: #2d3748;
            }
            .info {
                margin: 15px 0;
                padding: 10px;
                background-color: #f1f5f9;
                border-radius: 5px;
            }
            .label {
                font-weight: bold;
                color: #4a5568;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h2>You Have a New Booking</h2>
            <p class="info">
                <span class="label">Record ID:</span> {{ $booking->id }}<br />
                <span class="label">Customer:</span> {{ $booking->user->firstname }} {{ $booking->user->lastname }}<br />
                <span class="label">Phone:</span> {{ $booking->user->phone }}<br />
                <span class="label">Email:</span> {{ $booking->user->email }}
            </p>
            <p>Thank you,<br />Your Booking System</p>
        </div>
    </body>
</html>
