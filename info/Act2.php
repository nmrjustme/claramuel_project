<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Activity 2 Image</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
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
        <button onclick="history.back()"
            class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded shadow">
            ← Back
        </button>
    </div>

    <h1 class="text-2xl font-bold mb-6">Covid 19 Analysis</h1>

    <div class="flex flex-wrap justify-center gap-6 mb-8">
        <div class="flex flex-col items-center">
            <h3 class="font-medium text-lg mb-2">Covid Case Analysis 1</h3>
            <img src="act2_img/1.png" alt="Covid Case Analysis 1"
                class="gallery-image w-full max-w-xs rounded-lg shadow-lg border border-gray-300 cursor-pointer hover:opacity-90 transition">
        </div>
        <div class="flex flex-col items-center">
            <h3 class="font-medium text-lg mb-2">Covid Case Analysis 2</h3>
            <img src="act2_img/2.png" alt="Covid Case Analysis 2"
                class="gallery-image w-full max-w-xs rounded-lg shadow-lg border border-gray-300 cursor-pointer hover:opacity-90 transition">
        </div>
        <div class="flex flex-col items-center">
            <h3 class="font-medium text-lg mb-2">Covid Case Analysis 3</h3>
            <img src="act2_img/3.png" alt="Covid Case Analysis 3"
                class="gallery-image w-full max-w-xs rounded-lg shadow-lg border border-gray-300 cursor-pointer hover:opacity-90 transition">
        </div>
        <div class="flex flex-col items-center">
            <h3 class="font-medium text-lg mb-2">Covid Case Analysis 4</h3>
            <img src="act2_img/4.png" alt="Covid Case Analysis 4"
                class="gallery-image w-full max-w-xs rounded-lg shadow-lg border border-gray-300 cursor-pointer hover:opacity-90 transition">
        </div>
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