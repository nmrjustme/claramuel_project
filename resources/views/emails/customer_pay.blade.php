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
            <h2>New payment has been submitted</h2>
            <p class="info">
                  <span class="label">Record ID:</span> {{ $payment->bookingLog->id }}<br />
                  <span class="label">Payment ID:</span> {{ $payment->id }}<br />
                  <span class="label">Reservation Code:</span> {{ $payment->bookingLog->code }}<br />
                  <span class="label">Customer:</span> {{ $payment->bookingLog->user->firstname }} {{ $payment->bookingLog->user->lastname
                  }}<br />
                  <span class="label">Phone:</span> {{ $payment->bookingLog->user->phone }}<br />
                  <span class="label">Email:</span> {{ $payment->bookingLog->user->email }}
            </p>
            <p>Thank you,<br />Your Booking System</p>
      </div>
</body>

</html>