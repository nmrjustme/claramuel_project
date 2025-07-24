<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Email Confirmation</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: '#DC2626',
            secondary: '#B91C1C',
            dark: '#1F2937',
            light: '#F9FAFB',
            accent: '#EA580C',
          },
          fontFamily: {
            sans: ['Inter', 'sans-serif'],
          },
        }
      }
    }
  </script>
</head>
<body class="bg-light min-h-screen flex items-center justify-center p-4 font-sans">
  <!-- Email Confirmation Box -->
  <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6 mx-2 border border-red-100">
    <div class="flex items-center justify-center mb-4">
      <div class="bg-red-50 p-3 rounded-full">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
        </svg>
      </div>
    </div>
    
    <h3 class="text-lg font-medium text-center text-dark mb-2">Check your email</h3>
    <p class="text-gray-600 text-center mb-6">
      We've sent a confirmation email to your address. Please click the link in the email to continue.
    </p>
    
    <div class="bg-red-50 p-4 rounded-md mb-6 border border-red-100">
      <div class="flex items-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary mr-2" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
        </svg>
        <span class="text-sm text-secondary">Didn't receive an email? Check your spam folder.</span>
      </div>
    </div>
    
    <form id="send-verification" method="POST" action="#">
      <button 
        type="submit"
        class="w-full text-center dark:text-blue-300 text-sm text-gray-600 hover:underline rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary dark:focus:ring-offset-gray-800"
      >
        Click here to re-send the verification email.
      </button>
    </form>

    <!-- Status Message -->
    <div id="status-message" class="mt-2 font-medium text-sm text-green-600 hidden">
      A new verification link has been sent to your email address.
    </div>
  </div>

  <script>
    document.getElementById('send-verification').addEventListener('submit', function(e) {
      e.preventDefault();
      
      // Simulate form submission
      setTimeout(() => {
        const statusMessage = document.getElementById('status-message');
        statusMessage.classList.remove('hidden');
        
        // Hide after 5 seconds
        setTimeout(() => {
          statusMessage.classList.add('hidden');
        }, 5000);
      }, 500);
    });
  </script>
</body>
</html>