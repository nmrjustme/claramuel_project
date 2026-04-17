<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Richter Chatbots</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Dialogflow Messenger -->
    <script src="https://www.gstatic.com/dialogflow-console/fast/messenger/bootstrap.js?v=1"></script>
    <df-messenger
        intent="WELCOME"
        chat-title="Richter Chatbot"
        agent-id="6549c59c-5dd2-4f03-a079-c1e72e082520"
        language-code="en"
    ></df-messenger>
    <style>
        df-messenger {
            --df-messenger-bot-message: #ffffff;
            --df-messenger-button-titlebar-color: #3b82f6;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="max-w-3xl mx-auto px-4 py-8">
        <!-- Back Button -->
        <button onclick="window.history.back()" class="mb-6 flex items-center text-blue-600 hover:text-blue-800 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Back
        </button>

        <header class="text-center mb-10">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Welcome to Richter Chatbots</h1>
            <p class="text-lg text-gray-600">Your intelligent assistant for quick answers</p>
        </header>

        <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-800 mb-3">About This Page</h2>
            <p class="text-gray-600 mb-4">
                This page demonstrates a simple integration with Dialogflow's chatbot. 
                Click the chat icon in the bottom right corner to start a conversation.
            </p>
            
            <h2 class="text-xl font-semibold text-gray-800 mb-3">How It Works</h2>
            <p class="text-gray-600">
                The chatbot can answer questions and help with basic information. 
                Try asking about common topics to get started.
            </p>
        </div>

        <footer class="text-center text-gray-500 text-sm mt-12">
            <p>© 2023 Richter Chatbots | Simple Chatbot Interface</p>
        </footer>
    </div>
</body>
</html>