<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Cloud Computing Activities</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Adding Inter font -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            sans: ['Inter', 'sans-serif'],
          },
          colors: {
            primary: {
              500: '#4285F4',
              600: '#3367D6',
            }
          }
        }
      }
    }
  </script>
</head>
<body class="min-h-screen bg-gray-50 flex flex-col items-center justify-center px-4 py-8 text-center font-sans">
  <div class="max-w-4xl w-full">
    <div class="mb-12">
      <h1 class="text-4xl font-bold mb-2 text-gray-800">Google Cloud Activities</h1>
      <p class="text-lg text-gray-600">Explore these hands-on cloud computing exercises</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <!-- Activity 1 -->
      <a href="Act1.php" class="group bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition transform hover:-translate-y-1">
        <div class="p-6">
          <div class="flex items-center gap-4">
            <div class="text-left">
              <h3 class="text-lg font-semibold text-gray-800 group-hover:text-blue-600 transition">Activity 1: Cloud SQL</h3>
              <p class="text-sm text-gray-600">Create and query a database using Cloud SQL</p>
            </div>
          </div>
        </div>
      </a>

      <!-- Activity 2 -->
      <a href="Act2.php" class="group bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition transform hover:-translate-y-1">
        <div class="p-6">
          <div class="flex items-center gap-4">
            <div class="text-left">
              <h3 class="text-lg font-semibold text-gray-800 group-hover:text-green-600 transition">Activity 2: BigQuery</h3>
              <p class="text-sm text-gray-600">Perform data analytics on large datasets</p>
            </div>
          </div>
        </div>
      </a>

      <!-- Activity 3 -->
      <a href="Act3.php" class="group bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition transform hover:-translate-y-1">
        <div class="p-6">
          <div class="flex items-center gap-4">
            <div class="text-left">
              <h3 class="text-lg font-semibold text-gray-800 group-hover:text-purple-600 transition">Activity 3: Cloud Functions</h3>
              <p class="text-sm text-gray-600">Develop serverless computing functions</p>
            </div>
          </div>
        </div>
      </a>

      <!-- Activity 4 -->
      <a href="Act4.php" class="group bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition transform hover:-translate-y-1">
        <div class="p-6">
          <div class="flex items-center gap-4">

            <div class="text-left">
              <h3 class="text-lg font-semibold text-gray-800 group-hover:text-yellow-600 transition">Activity 4: Dialogflow</h3>
              <p class="text-sm text-gray-600">Build an AI-based chatbot</p>
            </div>
          </div>
        </div>
      </a>

      <!-- Activity 5 -->
      <a href="Act5.php" class="group bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition transform hover:-translate-y-1">
        <div class="p-6">
          <div class="flex items-center gap-4">
            <div class="text-left">
              <h3 class="text-lg font-semibold text-gray-800 group-hover:text-red-600 transition">Activity 5: Vision AI</h3>
              <p class="text-sm text-gray-600">Image analysis with machine learning</p>
            </div>
          </div>
        </div>
      </a>
    </div>
  </div>
</body>
</html>