@extends('layouts.admin')
@section('title', 'Facilities Management')

@php
    $active = 'facilities';
@endphp

@section('content')
<div class="min-h-screen p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Rooms Management</h1>
            <p class="text-gray-600">Manage all rooms and their details.</p>
        </div>
        <button 
            onclick="openAddModal()"
            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors flex items-center"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Add New Rooms
        </button>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-lightgray overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Images</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="facilitiesTableBody">
                    @forelse ($facilities as $facility)
                    <tr data-id="{{ $facility->id }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-medium text-gray-900">{{ $facility->name }}</div>
                            @if($facility->description)
                                <div class="text-sm text-gray-500 mt-1">{{ Str::limit($facility->description, 50) }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">{{ $facility->category }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            ₱{{ number_format($facility->price, 2) }} <span class="text-gray-400">/night</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex -space-x-2">
                                @foreach($facility->images->sortBy('order')->take(3) as $image)
                                    <img 
                                        class="h-10 w-10 rounded-full border-2 border-white object-cover cursor-pointer hover:scale-110 transition-transform" 
                                        src="{{ asset('imgs/facility_img/' . $image->image) }}" 
                                        alt="Facility image"
                                        title="View all images"
                                        onclick="openImageModal('{{ $facility->id }}')"
                                    >
                                @endforeach
                                @if($facility->images->count() > 3)
                                    <span 
                                        class="h-10 w-10 rounded-full border-2 border-white bg-gray-100 flex items-center justify-center text-xs font-medium text-gray-600 cursor-pointer hover:bg-gray-200"
                                        onclick="openImageModal('{{ $facility->id }}')"
                                    >
                                        +{{ $facility->images->count() - 3 }}
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap flex items-center space-x-2">
                            <!-- Edit Button -->
                            <button 
                                onclick="openEditModal('{{ $facility->id }}')"
                                class="px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-md transition-all duration-200 flex items-center space-x-1.5 text-sm shadow-sm hover:shadow-md"
                                aria-label="Edit facility"
                                title="Edit Facility"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                <span>Edit</span>
                            </button>
                            
                            <!-- Delete Button -->
                            <button 
                                onclick="openDeleteModal('{{ $facility->id }}')"
                                class="px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-md transition-all duration-200 flex items-center space-x-1.5 text-sm shadow-sm hover:shadow-md"
                                aria-label="Delete facility"
                                title="Delete Facility"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                <span>Delete</span>
                            </button>
                            
                            <!-- Discount Button -->
                            <button 
                                onclick="openDiscountModal('{{ $facility->id }}')"
                                class="px-3 py-1.5 bg-green-50 hover:bg-green-100 text-green-600 rounded-md transition-all duration-200 flex items-center space-x-1.5 text-sm shadow-sm hover:shadow-md"
                                aria-label="Manage discounts"
                                title="Manage Discounts"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Discount</span>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                            No facilities found. Add your first facility.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
    </div>
</div>

<!-- Add Facility Modal -->
<div id="addFacilityModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-800">Add New Facility</h3>
                <button onclick="closeAddModal()" class="text-gray-400 hover:text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <form id="addFacilityForm" action="{{ route('admin.facilities.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                        <input type="text" id="name" name="name" required 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500"
                            oninput="validateField(this)">
                        <p class="mt-1 text-xs text-red-500 hidden validation-message">Please enter a valid name</p>
                    </div>
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category *</label>
                        <input type="text" step="0.01" id="category" name="category" required 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500"
                            oninput="validateField(this)">
                        <p class="mt-1 text-xs text-red-500 hidden validation-message">Please enter a valid price</p>
                    </div>
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Price (per night) *</label>
                        <input type="number" step="0.01" id="price" name="price" required 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500"
                            oninput="validateField(this)">
                        <p class="mt-1 text-xs text-red-500 hidden validation-message">Please enter a valid price</p>
                    </div>
                    
                    <div>
                        <label for="pax" class="block text-sm font-medium text-gray-700 mb-1">Pax (Capacity)</label>
                        <input type="number" id="pax" name="pax" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                    
                    <div>
                        <label for="bed_number" class="block text-sm font-medium text-gray-700 mb-1">Bed Number</label>
                        <input type="number" id="bed_number" name="bed_number" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                    
                    <div>
                        <label for="room_number" class="block text-sm font-medium text-gray-700 mb-1">Room Number</label>
                        <input type="number" id="room_number" name="room_number" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                    
                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea id="description" name="description" rows="3" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500"></textarea>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label for="included" class="block text-sm font-medium text-gray-700 mb-1">What's Included (comma separated)</label>
                        <input type="text" id="included" name="included" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500" 
                            placeholder="e.g. Breakfast, WiFi, Aircon">
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Images</label>
                        <div class="drop-area mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label for="images" class="relative cursor-pointer bg-white rounded-md font-medium text-red-600 hover:text-red-500 focus-within:outline-none">
                                        <span>Upload files</span>
                                        <input id="images" name="images[]" type="file" multiple class="sr-only">
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
                            </div>
                        </div>
                        <div id="addImagePreviews" class="mt-2 flex flex-wrap gap-2 hidden"></div>
                    </div>
                </div>
                
                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" onclick="closeAddModal()" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md text-sm font-medium hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Save Facility
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Facility Modal -->
<div id="editFacilityModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-800">Edit Facility</h3>
                <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <form id="editFacilityForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="edit_name" class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                        <input type="text" id="edit_name" name="name" required 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500"
                            oninput="validateField(this)">
                        <p class="mt-1 text-xs text-red-500 hidden validation-message">Please enter a valid name</p>
                    </div>
                    <div>
                        <label for="edit_category" class="block text-sm font-medium text-gray-700 mb-1">Category *</label>
                        <input type="text" step="0.01" id="edit_category" name="category" required 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500"
                            oninput="validateField(this)">
                        <p class="mt-1 text-xs text-red-500 hidden validation-message">Please enter a valid price</p>
                    </div>
                    <div>
                        <label for="edit_price" class="block text-sm font-medium text-gray-700 mb-1">Price (per night) *</label>
                        <input type="number" step="0.01" id="edit_price" name="price" required 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500"
                            oninput="validateField(this)">
                        <p class="mt-1 text-xs text-red-500 hidden validation-message">Please enter a valid price</p>
                    </div>
                    
                    <div>
                        <label for="edit_pax" class="block text-sm font-medium text-gray-700 mb-1">Pax (Capacity)</label>
                        <input type="number" id="edit_pax" name="pax" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                    
                    <div>
                        <label for="edit_bed_number" class="block text-sm font-medium text-gray-700 mb-1">Bed Number</label>
                        <input type="number" id="edit_bed_number" name="bed_number" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                    
                    <div>
                        <label for="edit_room_number" class="block text-sm font-medium text-gray-700 mb-1">Room Number</label>
                        <input type="number" id="edit_room_number" name="room_number" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                    
                    <div class="md:col-span-2">
                        <label for="edit_description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea id="edit_description" name="description" rows="3" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500"></textarea>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label for="edit_included" class="block text-sm font-medium text-gray-700 mb-1">What's Included (comma separated)</label>
                        <input type="text" id="edit_included" name="included" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500" 
                            placeholder="e.g. Breakfast, WiFi, Aircon">
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Add More Images</label>
                        <div class="drop-area mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label for="edit_images" class="relative cursor-pointer bg-white rounded-md font-medium text-red-600 hover:text-red-500 focus-within:outline-none">
                                        <span>Upload files</span>
                                        <input id="edit_images" name="images[]" type="file" multiple class="sr-only">
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
                            </div>
                        </div>
                        <div id="editImagePreviews" class="mt-2 flex flex-wrap gap-2 hidden"></div>
                    </div>
                </div>
                
                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md text-sm font-medium hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Update Facility
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteFacilityModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-800">Confirm Deletion</h3>
                <button onclick="closeDeleteModal()" class="text-gray-400 hover:text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <p class="text-gray-600 mb-6">Are you sure you want to delete this facility? This action cannot be undone and all associated images will be permanently removed.</p>
            
            <form id="deleteFacilityForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md text-sm font-medium hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Delete Facility
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Image Gallery Modal -->
<div id="imageGalleryModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] overflow-hidden">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-800">Facility Images</h3>
                <button onclick="closeImageModal()" class="text-gray-400 hover:text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <div id="imageGalleryContainer" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 overflow-y-auto max-h-[70vh] p-2">
                <!-- Images will be loaded here via JavaScript -->
            </div>
        </div>
    </div>
</div>

<!-- Discount Management Modal -->
<div id="discountModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-800" id="discountModalTitle">Manage Discounts</h3>
                <button onclick="closeDiscountModal()" class="text-gray-400 hover:text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <div class="mb-4">
                <button 
                    onclick="openAddDiscountForm()"
                    class="px-4 py-2 bg-red-600 text-white rounded-md text-sm font-medium hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                >
                    Add New Discount
                </button>
            </div>
            
            <div id="discountList" class="space-y-4 max-h-96 overflow-y-auto">
                <!-- Discounts will be loaded here -->
            </div>
            
            <!-- Add/Edit Discount Form (hidden by default) -->
            <div id="discountFormContainer" class="hidden mt-6">
                <form id="discountForm">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="discount_type" class="block text-sm font-medium text-gray-700 mb-1">Discount Type *</label>
                            <select id="discount_type" name="discount_type" required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
                                <option value="">Select Type</option>
                                <option value="percent">Percentage</option>
                                <option value="fixed">Fixed Amount</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="discount_value" class="block text-sm font-medium text-gray-700 mb-1">Discount Value *</label>
                            <input type="number" step="0.01" id="discount_value" name="discount_value" required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
                        </div>
                        
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date *</label>
                            <input type="date" id="start_date" name="start_date" required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
                        </div>
                        
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">End Date *</label>
                            <input type="date" id="end_date" name="end_date" required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
                        </div>
                    </div>
                    
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" onclick="cancelDiscountForm()" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md text-sm font-medium hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            Save Discount
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Toast Container -->
<div id="toast-container" class="fixed top-4 right-4 space-y-2 z-50"></div>

@endsection

@section('content_js')
<!-- Include SortableJS for drag and drop -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
<script>
// Global variables
let currentFacilityId = null;
let uploadedImages = [];

// Discount modal functions
function openDiscountModal(facilityId) {
    currentFacilityId = facilityId;
    document.getElementById('discountModalTitle').textContent = `Discounts for ${document.querySelector(`#facilitiesTableBody tr[data-id="${facilityId}"] td:first-child div:first-child`).textContent}`;
    document.getElementById('discountModal').classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
    loadDiscounts(facilityId);
}

function closeDiscountModal() {
    document.getElementById('discountModal').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
    currentFacilityId = null;
    resetDiscountForm();
}

function openAddDiscountForm() {
    document.getElementById('discountFormContainer').classList.remove('hidden');
    document.getElementById('discountList').classList.add('hidden');
    document.getElementById('discountForm').reset();
    document.getElementById('discountForm').dataset.mode = 'add';
}

function cancelDiscountForm() {
    document.getElementById('discountFormContainer').classList.add('hidden');
    document.getElementById('discountList').classList.remove('hidden');
    resetDiscountForm();
}

function resetDiscountForm() {
    document.getElementById('discountForm').reset();
    document.getElementById('discountForm').removeAttribute('data-discount-id');
    document.getElementById('discountForm').dataset.mode = 'add';
}

function loadDiscounts(facilityId) {
    const discountList = document.getElementById('discountList');
    discountList.innerHTML = '<div class="text-center py-8"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-red-600 mx-auto"></div><p class="mt-2 text-gray-500">Loading discounts...</p></div>';

    fetch(`/facilities/discounted/${facilityId}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.discounts.length > 0) {
            renderDiscountList(data.discounts);
        } else {
            discountList.innerHTML = '<div class="text-center py-8 text-gray-500">No discounts found for this facility.</div>';
        }
    })
    .catch(error => {
        console.error('Error loading discounts:', error);
        discountList.innerHTML = '<div class="text-center py-8 text-red-500">Failed to load discounts. Please try again.</div>';
    });
}


function renderDiscountList(discounts) {
    const discountList = document.getElementById('discountList');
    discountList.innerHTML = '';
    
    discounts.forEach(discount => {
        const discountElement = document.createElement('div');
        discountElement.className = 'bg-gray-50 p-4 rounded-lg border border-gray-200';
        discountElement.innerHTML = `
            <div class="flex items-center space-x-2">
                <div>
                    <h4 class="font-medium text-gray-900">${discount.discount_type === 'percent' ? `${discount.discount_value}% off` : `₱${parseFloat(discount.discount_value).toFixed(2)} off`}</h4>
                    <p class="text-sm text-gray-500 mt-1">${formatDate(discount.start_date)} - ${formatDate(discount.end_date)}</p>
                    <p class="text-xs ${isDiscountActive(discount.start_date, discount.end_date) ? 'text-green-600' : 'text-gray-500'}">
                        ${getDiscountStatus(discount.start_date, discount.end_date)}
                    </p>
                </div>
                <div class="flex space-x-2 ml-auto">
                    <button onclick="editDiscount('${discount.id}', '${discount.discount_type}', '${discount.discount_value}', '${discount.start_date}', '${discount.end_date}')" class="text-blue-600 hover:text-blue-800">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </button>
                    <button onclick="deleteDiscount('${discount.id}')" class="text-red-600 hover:text-red-800">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>
            </div>
        `;
        discountList.appendChild(discountElement);
    });
}

function isDiscountActive(startDate, endDate) {
    const today = new Date();
    const start = new Date(startDate);
    const end = new Date(endDate);
    return today >= start && today <= end;
}

function getDiscountStatus(startDate, endDate) {
    const today = new Date();
    today.setHours(0, 0, 0, 0); // Normalize to midnight
    
    const start = new Date(startDate);
    start.setHours(0, 0, 0, 0);
    
    const end = new Date(endDate);
    end.setHours(0, 0, 0, 0);

    if (start.getTime() === today.getTime()) {
        return 'Starts today';
    } else if (start > today) {
        const diffDays = Math.ceil((start - today) / (1000 * 60 * 60 * 24));
        return `Starts in ${diffDays} day${diffDays !== 1 ? 's' : ''}`;
    } else if (end < today) {
        return 'Expired';
    } else {
        const remainingDays = Math.ceil((end - today) / (1000 * 60 * 60 * 24));
        return `Active (${remainingDays} day${remainingDays !== 1 ? 's' : ''} remaining)`;
    }
}
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
}

function editDiscount(discountId, type, value, startDate, endDate) {
    document.getElementById('discountFormContainer').classList.remove('hidden');
    document.getElementById('discountList').classList.add('hidden');
    
    document.getElementById('discount_type').value = type;
    document.getElementById('discount_value').value = value;
    document.getElementById('start_date').value = startDate;
    document.getElementById('end_date').value = endDate;
    
    // Set minimum date to today for start date when editing
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('start_date').min = today;
    
    document.getElementById('discountForm').dataset.mode = 'edit';
    document.getElementById('discountForm').dataset.discountId = discountId;
}


function deleteDiscount(discountId) {
    if (confirm('Are you sure you want to delete this discount?')) {
        fetch(`/facilities/discounts/delete/${discountId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Discount deleted successfully', 'success');
                loadDiscounts(currentFacilityId);
            } else {
                throw new Error(data.message || 'Failed to delete discount');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast(error.message, 'error');
        });
    }
}

// Handle discount form submission
// Handle discount form submission
document.getElementById('discountForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const mode = this.dataset.mode;
    const discountId = this.dataset.discountId;
    const facilityId = currentFacilityId;
    
    const startDate = new Date(formData.get('start_date'));
    const endDate = new Date(formData.get('end_date'));
    
    // Validate dates
    if (startDate >= endDate) {
        showToast('End date must be after start date', 'error');
        return;
    }
    
    const url = mode === 'add' 
        ? `/facilities/${facilityId}/discounts` 
        : `/facilities/discounts/${discountId}`;

    // For PUT method, append _method field
    if (mode === 'edit') {
        formData.append('_method', 'PUT');
    }
    
    const submitButton = this.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;
    submitButton.innerHTML = 'Saving...';
    submitButton.disabled = true;
    
    fetch(url, {
        method: 'POST', // Always use POST
        body: formData,
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(`Discount ${mode === 'add' ? 'added' : 'updated'} successfully`, 'success');
            loadDiscounts(facilityId);
            cancelDiscountForm();
        } else if (data.error === 'date_overlap') {
            throw new Error('This discount overlaps with an existing discount for this facility');
        } else {
            throw new Error(data.message || `Failed to ${mode} discount`);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast(error.message, 'error');
    })
    .finally(() => {
        submitButton.innerHTML = originalText;
        submitButton.disabled = false;
    });
});

document.getElementById('start_date').addEventListener('change', function() {
    const endDateInput = document.getElementById('end_date');
    if (this.value && endDateInput.value && new Date(this.value) >= new Date(endDateInput.value)) {
        endDateInput.value = '';
        showToast('End date must be after start date', 'error');
    }
    endDateInput.min = this.value;
});

document.getElementById('end_date').addEventListener('change', function() {
    const startDateInput = document.getElementById('start_date');
    if (this.value && startDateInput.value && new Date(this.value) <= new Date(startDateInput.value)) {
        this.value = '';
        showToast('End date must be after start date', 'error');
    }
});

// Modal functions
function openAddModal() {
    resetAddForm();
    document.getElementById('addFacilityModal').classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

function closeAddModal() {
    document.getElementById('addFacilityModal').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
    resetAddForm();
}

function openEditModal(id) {
    currentFacilityId = id;
    console.log("Opening modal for ID:", id);

    fetch(`/admin/facilities/${id}/edit`, {
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(async response => {
        const contentType = response.headers.get('content-type') || '';

        if (!response.ok || !contentType.includes('application/json')) {
            const text = await response.text(); // Get raw HTML/text
            throw new Error('Failed to load facility data. Server returned HTML or error.');
        }

        return response.json();
    })
    .then(data => {
        if (!data.success || !data.facility) {
            throw new Error(data.message || 'Facility data not found');
        }

        const facility = data.facility;

        document.getElementById('edit_name').value = facility.name;
        document.getElementById('edit_category').value = facility.category;
        document.getElementById('edit_price').value = facility.price;
        document.getElementById('edit_description').value = facility.description || '';
        document.getElementById('edit_pax').value = facility.pax || '';
        document.getElementById('edit_bed_number').value = facility.bed_number || '';
        document.getElementById('edit_room_number').value = facility.room_number || '';
        document.getElementById('edit_included').value = facility.included || '';

        document.getElementById('editFacilityForm').action = `/admin/facilities/update/${id}`;
        document.getElementById('editFacilityModal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    })
    .catch(error => {
        console.error('Error:', error);
        showToast(error.message, 'error');
    });
}


function closeEditModal() {
    document.getElementById('editFacilityModal').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
    currentFacilityId = null;
}

function openDeleteModal(id) {
    currentFacilityId = id;
    document.getElementById('deleteFacilityForm').action = `/facilities/delete/${id}`;
    document.getElementById('deleteFacilityModal').classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteFacilityModal').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
    currentFacilityId = null;
}

function openImageModal(facilityId) {
    currentFacilityId = facilityId;
    fetchImages(facilityId);
    document.getElementById('imageGalleryModal').classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

function closeImageModal() {
    document.getElementById('imageGalleryModal').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
    currentFacilityId = null;
    uploadedImages = [];
}

// Image handling functions
function fetchImages(facilityId) {
    const container = document.getElementById('imageGalleryContainer');
    container.innerHTML = '<div class="col-span-full text-center py-8"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-red-600 mx-auto"></div><p class="mt-2 text-gray-500">Loading images...</p></div>';
    
    fetch(`/facilities/${facilityId}/images`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                renderImageGallery(data.images);
                initSortable();
            } else {
                throw new Error(data.message || 'Failed to load images');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            container.innerHTML = `
                <div class="col-span-full text-center py-8">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <p class="mt-2 text-red-500">Failed to load images</p>
                    <p class="text-sm text-gray-500 mt-1">${error.message}</p>
                    <button onclick="fetchImages(${facilityId})" class="mt-4 px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                        Retry
                    </button>
                </div>
            `;
        });
}

function renderImageGallery(images) {
    const container = document.getElementById('imageGalleryContainer');
    container.innerHTML = '';
    
    if (images.length === 0 && uploadedImages.length === 0) {
        container.innerHTML = `
            <div class="col-span-full text-center py-8">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <p class="mt-2 text-gray-500">No images available for this facility.</p>
                <p class="text-sm text-gray-400 mt-1">Drag & drop images here or click to upload</p>
            </div>
        `;
    } else {
        // Combine existing images with newly uploaded ones
        const allImages = [...images, ...uploadedImages];
        
        allImages.forEach((image, index) => {
            const isNew = !image.id;
            const imgSrc = isNew ? image.preview : `/imgs/facility_img/${image.image}`;
            const imgId = isNew ? `new-${index}` : image.id;
            
            const imageElement = document.createElement('div');
            imageElement.className = 'relative group cursor-move bg-gray-100 rounded-lg overflow-hidden';
            imageElement.dataset.id = imgId;
            imageElement.innerHTML = `
                <img 
                    src="${imgSrc}" 
                    alt="Facility image" 
                    class="w-full h-40 object-cover"
                    draggable="false"
                >
                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 flex items-center justify-center rounded-lg transition-all duration-300 opacity-0 group-hover:opacity-100">
                    <a 
                        href="${imgSrc}" 
                        target="_blank" 
                        class="mr-2 p-2 bg-white rounded-full text-blue-600 hover:text-blue-800"
                        title="View Full Size"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </a>
                    <button 
                        onclick="deleteImage('${imgId}', event)"
                        class="p-2 bg-white rounded-full text-red-600 hover:text-red-800"
                        title="Delete Image"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>
                ${isNew ? '<div class="absolute top-2 left-2 bg-yellow-500 text-white text-xs px-2 py-1 rounded">New</div>' : ''}
            `;
            container.appendChild(imageElement);
        });
    }
    
    // Initialize drag and drop for images
    initSortable();
}

function initSortable() {
    new Sortable(document.getElementById('imageGalleryContainer'), {
        animation: 150,
        ghostClass: 'bg-gray-200',
        chosenClass: 'bg-gray-100',
        dragClass: 'cursor-grabbing',
        onEnd: function() {
            const items = document.querySelectorAll('#imageGalleryContainer > div');
            const imageOrder = Array.from(items).map((item, index) => ({
                id: item.dataset.id,
                order: index + 1
            }));
            
            // Only update order for existing images (not newly uploaded ones)
            const existingImages = imageOrder.filter(item => !item.id.startsWith('new-'));
            
            if (existingImages.length > 0) {
                fetch('/admin/facilities/update-image-order', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        images: existingImages
                    })
                }).catch(error => console.error('Error updating order:', error));
            }
        }
    });
}

// Form handling
document.getElementById('addFacilityForm').addEventListener('submit', function(e) {
    e.preventDefault();
    submitForm(this, 'POST');
});

document.getElementById('editFacilityForm').addEventListener('submit', function(e) {
    e.preventDefault();
    submitForm(this, 'PUT');
});

document.getElementById('deleteFacilityForm').addEventListener('submit', function(e) {
    e.preventDefault();
    submitForm(this, 'DELETE');
});

function submitForm(form, method) {
    const formData = new FormData(form);
    const url = form.action;
    
    // For PUT/DELETE methods, we need to add _method field
    if (method !== 'POST') {
        formData.append('_method', method);
    }
    
    // Show loading state
    const submitButton = form.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;
    submitButton.innerHTML = `Loading...`;
    submitButton.disabled = true;
    
    fetch(url, {
        method: 'POST', // Always use POST and rely on _method for Laravel
        body: formData,
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => { throw err; });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            // Refresh the table or update the UI
            if (method === 'POST') {
                addFacilityToTable(data.facility);
                closeAddModal();
            } else if (method === 'PUT') {
                updateFacilityInTable(data.facility);
                closeEditModal();
            } else if (method === 'DELETE') {
                document.querySelector(`#facilitiesTableBody tr[data-id="${currentFacilityId}"]`).remove();
                closeDeleteModal();
            }
        } else {
            throw new Error(data.message || 'Operation failed');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        let errorMessage = error.message || 'An error occurred';
        
        // Handle validation errors
        if (error.errors) {
            errorMessage = Object.values(error.errors).join(', ');
        }
        
        showToast(errorMessage, 'error');
    })
    .finally(() => {
        submitButton.innerHTML = originalText;
        submitButton.disabled = false;
    });
}

function addFacilityToTable(facility) {
    const tableBody = document.getElementById('facilitiesTableBody');
    const emptyRow = tableBody.querySelector('tr:first-child td[colspan]');
    
    if (emptyRow) {
        tableBody.innerHTML = '';
    }
    
    const row = document.createElement('tr');
    row.dataset.id = facility.id;
    row.innerHTML = `
        <td class="px-6 py-4 whitespace-nowrap">
            <div class="font-medium text-gray-900">${facility.name}</div>
            ${facility.description ? `<div class="text-sm text-gray-500 mt-1">${facility.description.substring(0, 50)}${facility.description.length > 50 ? '...' : ''}</div>` : ''}
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">${facility.category}</span>
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
            ₱${parseFloat(facility.price).toFixed(2)} <span class="text-gray-400">/night</span>
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
            <div class="flex -space-x-2">
                ${facility.images.slice(0, 3).map(image => `
                    <img 
                        class="h-10 w-10 rounded-full border-2 border-white object-cover cursor-pointer hover:scale-110 transition-transform" 
                        src="/imgs/facility_img/${image.image}" 
                        alt="Facility image"
                        title="View all images"
                        onclick="openImageModal('${facility.id}')"
                    >
                `).join('')}
                ${facility.images.length > 3 ? `
                    <span 
                        class="h-10 w-10 rounded-full border-2 border-white bg-gray-100 flex items-center justify-center text-xs font-medium text-gray-600 cursor-pointer hover:bg-gray-200"
                        onclick="openImageModal('${facility.id}')"
                    >
                        +${facility.images.length - 3}
                    </span>
                ` : ''}
            </div>
        </td>
        <td class="px-6 py-4 whitespace-nowrap flex items-center space-x-2">
            <!-- Edit Button -->
            <button 
                onclick="openEditModal('${facility.id}')"
                class="px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-md transition-all duration-200 flex items-center space-x-1.5 text-sm shadow-sm hover:shadow-md"
                aria-label="Edit facility"
                title="Edit Facility"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                <span>Edit</span>
            </button>
            
            <!-- Delete Button -->
            <button 
                onclick="openDeleteModal('${facility.id}')"
                class="px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-md transition-all duration-200 flex items-center space-x-1.5 text-sm shadow-sm hover:shadow-md"
                aria-label="Delete facility"
                title="Delete Facility"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                <span>Delete</span>
            </button>
            
            <!-- Discount Button -->
            <button 
                onclick="openDiscountModal('${facility.id}')"
                class="px-3 py-1.5 bg-green-50 hover:bg-green-100 text-green-600 rounded-md transition-all duration-200 flex items-center space-x-1.5 text-sm shadow-sm hover:shadow-md"
                aria-label="Manage discounts"
                title="Manage Discounts"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Discount</span>
            </button>
        </td>
    `;
    
    tableBody.prepend(row);
}

function updateFacilityInTable(facility) {
    const row = document.querySelector(`#facilitiesTableBody tr[data-id="${facility.id}"]`);
    if (row) {
        row.querySelector('td:first-child div:first-child').textContent = facility.name;
        if (facility.description) {
            const descElement = row.querySelector('td:first-child div.text-sm') || 
                document.createElement('div');
            descElement.className = 'text-sm text-gray-500 mt-1';
            descElement.textContent = facility.description.substring(0, 50) + 
                (facility.description.length > 50 ? '...' : '');
            if (!row.querySelector('td:first-child div.text-sm')) {
                row.querySelector('td:first-child').appendChild(descElement);
            }
        } else {
            const descElement = row.querySelector('td:first-child div.text-sm');
            if (descElement) descElement.remove();
        }
        
        row.querySelector('td:nth-child(2) span').textContent = facility.category;
        row.querySelector('td:nth-child(3)').innerHTML = 
            `₱${parseFloat(facility.price).toFixed(2)} <span class="text-gray-400">/night</span>`;
        
        // Update images
        const imagesContainer = row.querySelector('td:nth-child(4) div');
        imagesContainer.innerHTML = '';
        
        facility.images.slice(0, 3).forEach(image => {
            const img = document.createElement('img');
            img.className = 'h-10 w-10 rounded-full border-2 border-white object-cover cursor-pointer hover:scale-110 transition-transform';
            img.src = `/imgs/facility_img/${image.image}`;
            img.alt = 'Facility image';
            img.title = 'View all images';
            img.onclick = () => openImageModal(facility.id);
            imagesContainer.appendChild(img);
        });
        
        if (facility.images.length > 3) {
            const moreSpan = document.createElement('span');
            moreSpan.className = 'h-10 w-10 rounded-full border-2 border-white bg-gray-100 flex items-center justify-center text-xs font-medium text-gray-600 cursor-pointer hover:bg-gray-200';
            moreSpan.textContent = `+${facility.images.length - 3}`;
            moreSpan.onclick = () => openImageModal(facility.id);
            imagesContainer.appendChild(moreSpan);
        }
    }
}

// Image upload handling
function handleImageUpload(event, isEdit = false) {
    const files = event.target.files;
    const container = isEdit ? 
        document.getElementById('editImagePreviews') : 
        document.getElementById('addImagePreviews');
    
    container.innerHTML = '';
    
    if (files.length > 0) {
        container.classList.remove('hidden');
        
        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            const reader = new FileReader();
            
            reader.onload = function(e) {
                const preview = document.createElement('div');
                preview.className = 'relative group';
                preview.innerHTML = `
                    <img src="${e.target.result}" class="h-24 w-24 object-cover rounded-lg border border-gray-200">
                    <button 
                        type="button" 
                        onclick="removeImagePreview(this, ${i}, ${isEdit})"
                        class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                `;
                container.appendChild(preview);
            };
            
            reader.readAsDataURL(file);
            
            // For image gallery modal
            if (isEdit) {
                const previewUrl = URL.createObjectURL(file);
                uploadedImages.push({
                    preview: previewUrl,
                    file: file
                });
            }
        }
        
        if (isEdit && currentFacilityId) {
            fetchImages(currentFacilityId);
        }
    } else {
        container.classList.add('hidden');
    }
}

function removeImagePreview(button, index, isEdit) {
    if (isEdit) {
        uploadedImages.splice(index, 1);
        if (currentFacilityId) {
            fetchImages(currentFacilityId);
        }
    }
    
    const container = button.closest('div.relative').parentNode;
    button.closest('div.relative').remove();
    
    // Remove the file from the input
    const input = isEdit ? 
        document.getElementById('edit_images') : 
        document.getElementById('images');
    const files = Array.from(input.files);
    files.splice(index, 1);
    
    // Create a new DataTransfer object and set the files
    const dataTransfer = new DataTransfer();
    files.forEach(file => dataTransfer.items.add(file));
    input.files = dataTransfer.files;
    
    // Hide container if no more images
    if (container.children.length === 0) {
        container.classList.add('hidden');
    }
}

function deleteImage(imageId, event) {
    event.preventDefault();
    event.stopPropagation();
    
    if (confirm('Are you sure you want to delete this image?')) {
        const isNewImage = imageId.startsWith('new-');
        
        if (isNewImage) {
            // Remove from uploadedImages array
            const index = parseInt(imageId.replace('new-', ''));
            uploadedImages.splice(index, 1);
            
            // Remove the image element from the DOM
            const imageElement = document.querySelector(`#imageGalleryContainer div[data-id="${imageId}"]`);
            if (imageElement) {
                imageElement.remove();
            }
            
            showToast('Image removed', 'success');
            
            // If no images left, show empty state
            if (document.querySelectorAll('#imageGalleryContainer > div').length === 0) {
                renderEmptyState();
            }
        } else {
            // Delete from server
            fetch(`/admin/facilities/images/${imageId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => {
                if (response.ok) {
                    return response.json();
                }
                throw new Error('Failed to delete image');
            })
            .then(data => {
                if (data.success) {
                    showToast('Image deleted successfully', 'success');
                    
                    // Remove the image element from the DOM
                    const imageElement = document.querySelector(`#imageGalleryContainer div[data-id="${imageId}"]`);
                    if (imageElement) {
                        imageElement.remove();
                    }
                    
                    // Also update the facility row images if we're viewing the modal
                    updateFacilityRowImages(currentFacilityId);
                    
                    // If no images left, show empty state
                    if (document.querySelectorAll('#imageGalleryContainer > div').length === 0) {
                        renderEmptyState();
                    }
                } else {
                    throw new Error(data.message || 'Failed to delete image');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast(error.message, 'error');
            });
        }
    }
}

function renderEmptyState() {
    const container = document.getElementById('imageGalleryContainer');
    container.innerHTML = `
        <div class="col-span-full text-center py-8">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <p class="mt-2 text-gray-500">No images available for this facility.</p>
            <p class="text-sm text-gray-400 mt-1">Drag & drop images here or click to upload</p>
        </div>
    `;
}

function updateFacilityRowImages(facilityId) {
    // Fetch the updated facility data to refresh the images in the table row
    fetch(`/admin/facilities/${facilityId}/edit`, {
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.facility) {
            const facility = data.facility;
            const row = document.querySelector(`#facilitiesTableBody tr[data-id="${facilityId}"]`);
            
            if (row) {
                const imagesContainer = row.querySelector('td:nth-child(4) div');
                imagesContainer.innerHTML = '';
                
                facility.images.slice(0, 3).forEach(image => {
                    const img = document.createElement('img');
                    img.className = 'h-10 w-10 rounded-full border-2 border-white object-cover cursor-pointer hover:scale-110 transition-transform';
                    img.src = `/imgs/facility_img/${image.image}`;
                    img.alt = 'Facility image';
                    img.title = 'View all images';
                    img.onclick = () => openImageModal(facilityId);
                    imagesContainer.appendChild(img);
                });
                
                if (facility.images.length > 3) {
                    const moreSpan = document.createElement('span');
                    moreSpan.className = 'h-10 w-10 rounded-full border-2 border-white bg-gray-100 flex items-center justify-center text-xs font-medium text-gray-600 cursor-pointer hover:bg-gray-200';
                    moreSpan.textContent = `+${facility.images.length - 3}`;
                    moreSpan.onclick = () => openImageModal(facilityId);
                    imagesContainer.appendChild(moreSpan);
                }
            }
        }
    })
    .catch(error => console.error('Error updating facility row:', error));
}

// Utility functions
function resetAddForm() {
    document.getElementById('addFacilityForm').reset();
    document.getElementById('addImagePreviews').innerHTML = '';
    document.getElementById('addImagePreviews').classList.add('hidden');
    // Reset validation states
    document.querySelectorAll('#addFacilityForm .validation-message').forEach(el => {
        el.classList.add('hidden');
    });
    document.querySelectorAll('#addFacilityForm [required]').forEach(el => {
        el.classList.remove('border-red-500', 'border-green-500');
    });
}

function showToast(message, type = 'success') {
    const toastContainer = document.getElementById('toast-container');
    const toast = document.createElement('div');
    toast.className = `px-6 py-3 rounded-md shadow-lg text-white flex items-center ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
    } animate-fade-in`;
    toast.innerHTML = `
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${type === 'success' ? 'M5 13l4 4L19 7' : 'M6 18L18 6M6 6l12 12'}" />
        </svg>
        <span>${message}</span>
    `;
    toastContainer.appendChild(toast);
    
    setTimeout(() => {
        toast.classList.add('animate-fade-out');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

function validateField(field) {
    const validationMessage = field.nextElementSibling;
    if (field.checkValidity()) {
        field.classList.remove('border-red-500');
        field.classList.add('border-green-500');
        validationMessage.classList.add('hidden');
    } else {
        field.classList.remove('border-green-500');
        field.classList.add('border-red-500');
        validationMessage.classList.remove('hidden');
    }
}

// Initialize drag and drop for image upload
function initDragAndDrop() {
    const dropAreas = document.querySelectorAll('.drop-area');
    
    dropAreas.forEach(dropArea => {
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, preventDefaults, false);
        });
        
        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }
        
        ['dragenter', 'dragover'].forEach(eventName => {
            dropArea.addEventListener(eventName, highlight, false);
        });
        
        ['dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, unhighlight, false);
        });
        
        function highlight() {
            dropArea.classList.add('border-blue-500', 'bg-blue-50');
        }
        
        function unhighlight() {
            dropArea.classList.remove('border-blue-500', 'bg-blue-50');
        }
        
        dropArea.addEventListener('drop', handleDrop, false);
    });
    
    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        const isEdit = e.target.closest('#editFacilityModal');
        
        if (isEdit) {
            document.getElementById('edit_images').files = files;
            const event = new Event('change');
            document.getElementById('edit_images').dispatchEvent(event);
        } else {
            document.getElementById('images').files = files;
            const event = new Event('change');
            document.getElementById('images').dispatchEvent(event);
        }
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initDragAndDrop();
    
    // Add event listeners for image uploads
    document.getElementById('images').addEventListener('change', function(e) {
        handleImageUpload(e, false);
    });
    
    document.getElementById('edit_images').addEventListener('change', function(e) {
        handleImageUpload(e, true);
    });
    
    // Add real-time validation for required fields
    document.querySelectorAll('[required]').forEach(field => {
        field.addEventListener('input', function() {
            validateField(this);
        });
    });
});
</script>
@endsection

@section('content_css')
<style>
/* Animation classes */
.animate-fade-in {
    animation: fadeIn 0.3s ease forwards;
}

.animate-fade-out {
    animation: fadeOut 0.3s ease forwards;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateX(100%);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes fadeOut {
    from {
        opacity: 1;
        transform: translateX(0);
    }
    to {
        opacity: 0;
        transform: translateX(100%);
    }
}

/* Drag and drop styles */
.drop-area {
    transition: all 0.2s ease;
}

/* Sortable.js styles */
.sortable-ghost {
    opacity: 0.5;
    background: #f3f4f6;
}

/* Image gallery styles */
#imageGalleryContainer {
    scrollbar-width: thin;
    scrollbar-color: #e5e7eb #f9fafb;
}

#imageGalleryContainer::-webkit-scrollbar {
    width: 8px;
}

#imageGalleryContainer::-webkit-scrollbar-track {
    background: #f9fafb;
}

#imageGalleryContainer::-webkit-scrollbar-thumb {
    background-color: #e5e7eb;
    border-radius: 4px;
}
</style>
@endsection