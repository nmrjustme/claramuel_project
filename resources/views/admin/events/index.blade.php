@extends('layouts.admin')
@section('title', 'Events')

@section('sidebar')
<x-sidebar active="event_management" />
@endsection

@section('content')
<div class="bg-white rounded-xl shadow-md overflow-hidden">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center p-6 border-b gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Events Packages Management</h2>
            <p class="text-sm text-gray-500 mt-1">Manage all event packages and pricing</p>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
            <input type="text" placeholder="Search packages..."
                class="border border-gray-300 rounded-lg px-4 py-2 w-full focus:ring-2 focus:ring-red-500 focus:border-transparent"
                aria-label="Search packages">

            <button
                class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors duration-200 open-modal"
                data-title="Add New Package">
                Add Package
            </button>
        </div>
    </div>

    <!-- Table Section -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Package
                        Name</th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <tr class="hover:bg-gray-50 transition-colors duration-150">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">Birthday Basic</div>
                        <div class="text-sm text-gray-500">20 guests included</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span
                            class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                            Birthday
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">₱1,000.00</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">4 hours</td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex justify-end space-x-2">
                            <button
                                class="text-blue-600 hover:text-blue-900 px-3 py-1 rounded transition-colors duration-200">
                                Edit
                            </button>
                            <button
                                class="text-red-600 hover:text-red-900 px-3 py-1 rounded transition-colors duration-200">
                                Delete
                            </button>
                        </div>
                    </td>
                </tr>

                <tr class="hover:bg-gray-50 transition-colors duration-150">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">Wedding Gold</div>
                        <div class="text-sm text-gray-500">100 guests included</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span
                            class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                            Wedding
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">₱3,000.00</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Full day</td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex justify-end space-x-2">
                            <button
                                class="text-blue-600 hover:text-blue-900 px-3 py-1 rounded transition-colors duration-200">
                                Edit
                            </button>
                            <button
                                class="text-red-600 hover:text-red-900 px-3 py-1 rounded transition-colors duration-200">
                                Delete
                            </button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Pagination Section -->
    <div class="flex flex-col sm:flex-row justify-between items-center p-4 border-t bg-gray-50">
        <div class="text-sm text-gray-500 mb-2 sm:mb-0">
            Showing <span class="font-medium">1</span> to <span class="font-medium">2</span> of <span
                class="font-medium">15</span> packages
        </div>
        <div class="flex space-x-1">
            <button
                class="px-3 py-1 border rounded-md text-sm font-medium hover:bg-gray-100 transition-colors duration-200">
                Previous
            </button>
            <button class="px-3 py-1 border rounded-md text-sm font-medium bg-red-600 text-white hover:bg-red-700">
                1
            </button>
            <button
                class="px-3 py-1 border rounded-md text-sm font-medium hover:bg-gray-100 transition-colors duration-200">
                2
            </button>
            <button
                class="px-3 py-1 border rounded-md text-sm font-medium hover:bg-gray-100 transition-colors duration-200">
                Next
            </button>
        </div>
    </div>
</div>

<!-- Event Booking Record -->
<section id="booking-record" class="mb-8">
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center p-6 border-b gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Events Booking Record</h2>
                <p class="text-sm text-gray-500 mt-1">Manage all event bookings and reservations</p>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                <input type="text" placeholder="Search bookings..."
                    class="border border-gray-300 rounded-lg px-4 py-2 w-full focus:ring-2 focus:ring-red-500 focus:border-transparent"
                    aria-label="Search bookings">

                <button
                    class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors duration-200 open-modal"
                    data-title="Add Booking">
                    + Add Booking
                </button>
            </div>
        </div>

        <!-- Table Section -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Customer</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status</th>
                        <th scope="col"
                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">Birthday Basic</div>
                            <div class="text-sm text-gray-500">20 guests</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">2024-05-01</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">Emma Wilson</div>
                            <div class="text-sm text-gray-500">emma@example.com</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span
                                class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Confirmed
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end space-x-2">
                                <button
                                    class="text-blue-600 hover:text-blue-900 px-3 py-1 rounded transition-colors duration-200">
                                    Edit
                                </button>
                                <button
                                    class="text-red-600 hover:text-red-900 px-3 py-1 rounded transition-colors duration-200">
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>

                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">Wedding Gold</div>
                            <div class="text-sm text-gray-500">100 guests</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">2024-05-10</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">Michael Brown</div>
                            <div class="text-sm text-gray-500">michael@example.com</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span
                                class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                Pending
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end space-x-2">
                                <button
                                    class="text-blue-600 hover:text-blue-900 px-3 py-1 rounded transition-colors duration-200">
                                    Edit
                                </button>
                                <button
                                    class="text-red-600 hover:text-red-900 px-3 py-1 rounded transition-colors duration-200">
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination Section -->
        <div class="flex flex-col sm:flex-row justify-between items-center p-4 border-t bg-gray-50">
            <div class="text-sm text-gray-500 mb-2 sm:mb-0">
                Showing <span class="font-medium">1</span> to <span class="font-medium">2</span> of <span
                    class="font-medium">50</span> bookings
            </div>
            <div class="flex space-x-1">
                <button
                    class="px-3 py-1 border rounded-md text-sm font-medium hover:bg-gray-100 transition-colors duration-200">
                    Previous
                </button>
                <button class="px-3 py-1 border rounded-md text-sm font-medium bg-red-600 text-white hover:bg-red-700">
                    1
                </button>
                <button
                    class="px-3 py-1 border rounded-md text-sm font-medium hover:bg-gray-100 transition-colors duration-200">
                    2
                </button>
                <button
                    class="px-3 py-1 border rounded-md text-sm font-medium hover:bg-gray-100 transition-colors duration-200">
                    Next
                </button>
            </div>
        </div>
    </div>
</section>

<!-- Event Images -->
<h2 class="text-xl font-bold text-primary mb-4 dark:text-white">Birthday</h2>
<div id="parkImageGallery" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4">
    <!-- Dynamically added images will appear here -->
    <div id="addParkImagePlaceholder"
        class="relative group border-dashed border-2 border-gray-300 flex items-center justify-center h-32 rounded cursor-pointer">
        <span class="text-gray-500">+ Add Image</span>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const parkImageGallery = document.getElementById('parkImageGallery');
        const addParkImagePlaceholder = document.getElementById('addParkImagePlaceholder');

        addParkImagePlaceholder.addEventListener('click', function () {
            const imageInput = document.createElement('input');
            imageInput.type = 'file';
            imageInput.accept = 'image/*';
            imageInput.style.display = 'none';
            document.body.appendChild(imageInput);

            imageInput.addEventListener('change', function () {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        const imageContainer = document.createElement('div');
                        imageContainer.classList.add('relative', 'group');
                        imageContainer.innerHTML = `
            <img src="${e.target.result}" alt="Uploaded Image" class="w-full h-32 object-cover rounded">
            <button class="absolute top-2 right-2 bg-red-600 text-white text-xs px-2 py-1 rounded hidden group-hover:block delete-image">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M10 3h4m-4 0a1 1 0 00-1 1v1h6V4a1 1 0 00-1-1h-4z" />
              </svg>
            </button>
          `;
                        parkImageGallery.insertBefore(imageContainer, addParkImagePlaceholder);

                        // Add delete functionality
                        const deleteButton = imageContainer.querySelector('.delete-image');
                        deleteButton.addEventListener('click', function () {
                            if (confirm('Are you sure you want to delete this image?')) {
                                imageContainer.remove();
                            }
                        });
                    };
                    reader.readAsDataURL(file);
                }
                document.body.removeChild(imageInput);
            });

            imageInput.click();
        });
    });
</script>

<!-- Modal -->
<div id="addModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6 relative mx-2">
        <button id="closeModalBtn" type="button"
            class="absolute top-2 right-2 text-gray-400 hover:text-red-600 text-2xl">&times;</button>
        <h2 class="text-xl font-bold mb-4" id="modalTitle">Add Item</h2>
        <!-- Example form, customize fields as needed per page -->
        <form id="addForm">
            <div class="mb-4">
                <label class="block mb-1 font-semibold">Name</label>
                <input type="text" class="w-full border rounded px-3 py-2" required>
            </div>
            <div class="mb-4">
                <label class="block mb-1 font-semibold">Description</label>
                <input type="text" class="w-full border rounded px-3 py-2">
            </div>
            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Add</button>
        </form>
    </div>
</div>
@endsection