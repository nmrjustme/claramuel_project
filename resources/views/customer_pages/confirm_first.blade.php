<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Confirmation Required</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body class="font-sans bg-red-50 text-dark min-h-screen flex items-center justify-center p-4">
    <div class="bg-white p-8 rounded-xl shadow-lg max-w-md w-full border border-gray-200">
        <!-- Header with Branding -->
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center h-12 w-12 rounded-full bg-primary/10 mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-primary">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-dark">Confirm Your Email Address</h1>
            <p class="text-gray-600 mt-2">Kindly verify your email address to proceed with your payment.</p>
        </div>

        <!-- Main Content -->
        <div class="space-y-4 mb-6">
            <div class="mt-4 p-3 bg-yellow-50 border-l-4 border-yellow-400 text-yellow-700 rounded-r">
                <p class="font-medium">Important:</p>
                <p>If you're not currently logged in with <span class="font-semibold">richmayandoc11@gmail.com</span>, please log in to that account first to access the email verification.</p>
            </div>
            <p class="text-center text-gray-600">Please check your inbox or spam folder for the verification email.</p>
        </div>

        <!-- Action Button -->
        <div class="text-center">
            <a href="https://mail.google.com" target="_blank" class="inline-flex items-center justify-center px-6 py-3 rounded-lg font-medium text-white bg-gradient-to-r from-primary to-secondary hover:from-secondary hover:to-primary transition-all shadow-md hover:shadow-lg">
                Open Gmail Inbox
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 ml-2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                </svg>
            </a>
        </div>
    </div>
</body>
</html>