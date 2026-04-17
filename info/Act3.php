<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Performance Metrics</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            sans: ['Inter', 'sans-serif'],
          }
        }
      }
    }

    document.addEventListener('DOMContentLoaded', () => {
      // Modal functionality
      const modal = document.getElementById('imageModal');
      const modalImg = document.getElementById('modalImage');
      
      // Add click handlers to all gallery images
      document.querySelectorAll('.gallery-image').forEach(img => {
        img.addEventListener('click', () => {
          modal.classList.remove('hidden');
          modalImg.src = img.src;
          modalImg.alt = img.alt;
          document.body.classList.add('overflow-hidden');
        });
      });

      // Close modal
      document.getElementById('closeModal').addEventListener('click', () => {
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
      });

      // Close when clicking outside image
      modal.addEventListener('click', (e) => {
        if (e.target === modal) {
          modal.classList.add('hidden');
          document.body.classList.remove('overflow-hidden');
        }
      });
      
    });
  </script>
</head>
<body class="bg-gray-100 text-center p-6 font-sans">
  <div class="mb-4">
    <button onclick="history.back()" class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded shadow transition">
      ← Back
    </button>
  </div>
  
  <div class="flex flex-wrap justify-center gap-8 mb-8">
    <div class="flex flex-col items-center">
      <h3 class="font-medium text-lg mb-2">Performance</h3>
      <img src="act3_img/performance.png" alt="Performance Metrics" 
           class="gallery-image w-full max-w-xs rounded-lg shadow-lg border border-gray-300 cursor-pointer hover:opacity-90 transition">
    </div>
    <div class="flex flex-col items-center">
      <h3 class="font-medium text-lg mb-2">Logs</h3>
      <img src="act3_img/url.png" alt="URL Metrics" 
           class="gallery-image w-full max-w-xs rounded-lg shadow-lg border border-gray-300 cursor-pointer hover:opacity-90 transition">
    </div>
  </div>

  <div class="mt-6">
    <a href="https://hello-world-168882883734.europe-west1.run.app" target="_blank" rel="noopener noreferrer"
       class="inline-block bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-6 rounded-full shadow transition">
      Test Application
    </a>
  </div>
  
  <!-- Modal (hidden by default) -->
  <div id="imageModal" class="hidden fixed inset-0 z-50 bg-black bg-opacity-90 flex items-center justify-center p-4">
    <button id="closeModal" class="absolute top-4 right-6 text-white text-4xl font-bold hover:text-gray-300 transition">
      &times;
    </button>
    <img id="modalImage" class="max-w-full max-h-screen object-contain">
  </div>
  
</body>
</html>