<!DOCTYPE html>
<html>

<head>
      <title>Payment Successful</title>
      <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen">
      <div class="bg-white p-8 rounded shadow-md text-center">
            <h1 class="text-2xl text-green-600 font-bold mb-4">Payment Successful!</h1>
            <p class="mb-4">Thank you for your purchase.</p>
            <a href="{{ route('maya.checkout') }}" class="text-blue-600 hover:underline">Return to Home</a>
      </div>
</body>

</html>