

<div class="flex items-center justify-between mb-6 mt-6">
    <div>
        <h1 class="text-3xl font-bold text-white">Cottages Management</h1>
        <p class="text-gray-200">Manage all cottages and their details.</p>
    </div>
    <button 
        onclick="openAddCottageModal()"
        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors flex items-center"
    >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Add New Cottage
    </button>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Images</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200" id="cottagesTableBody">
                <!-- Cottages will be loaded here via AJAX -->
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-red-600 mx-auto"></div>
                        <p class="mt-2">Loading cottages...</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div id="paginationContainer" class="px-6 py-3 border-t border-gray-200 flex items-center justify-between">
        <!-- Pagination will be loaded here -->
    </div>
</div>


<!-- Add Cottage Modal -->
<div id="addCottageModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-800">Add New Cottage</h3>
                <button onclick="closeAddCottageModal()" class="text-gray-400 hover:text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <form id="addCottageForm" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="cottage_name" class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                        <input type="text" id="cottage_name" name="name" required 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500"
                            oninput="validateField(this)">
                        <p class="mt-1 text-xs text-red-500 hidden validation-message">Please enter a valid name</p>
                    </div>
                    
                    <div>
                        <label for="cottage_quantity" class="block text-sm font-medium text-gray-700 mb-1">Quantity *</label>
                        <input type="number" id="cottage_quantity" name="quantity" required min="1"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500"
                            oninput="validateField(this)">
                        <p class="mt-1 text-xs text-red-500 hidden validation-message">Please enter a valid quantity</p>
                    </div>
                    
                    <div>
                        <label for="cottage_price" class="block text-sm font-medium text-gray-700 mb-1">Price *</label>
                        <input type="number" step="0.01" id="cottage_price" name="price" required min="0"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500"
                            oninput="validateField(this)">
                        <p class="mt-1 text-xs text-red-500 hidden validation-message">Please enter a valid price</p>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label for="cottage_description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea id="cottage_description" name="description" rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500"></textarea>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Images *</label>
                        <div class="drop-area mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label for="cottage_images" class="relative cursor-pointer bg-white rounded-md font-medium text-red-600 hover:text-red-500 focus-within:outline-none">
                                        <span>Upload files</span>
                                        <input id="cottage_images" name="images[]" type="file" multiple class="sr-only" required>
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
                            </div>
                        </div>
                        <div id="addCottageImagePreviews" class="mt-2 flex flex-wrap gap-2 hidden"></div>
                    </div>
                </div>
                
                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" onclick="closeAddCottageModal()" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md text-sm font-medium hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Save Cottage
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Cottage Modal -->
<div id="editCottageModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-800">Edit Cottage</h3>
                <button onclick="closeEditCottageModal()" class="text-gray-400 hover:text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <form id="editCottageForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="edit_cottage_name" class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                        <input type="text" id="edit_cottage_name" name="name" required 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500"
                            oninput="validateField(this)">
                        <p class="mt-1 text-xs text-red-500 hidden validation-message">Please enter a valid name</p>
                    </div>
                    
                    <div>
                        <label for="edit_cottage_quantity" class="block text-sm font-medium text-gray-700 mb-1">Quantity *</label>
                        <input type="number" id="edit_cottage_quantity" name="quantity" required min="1"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500"
                            oninput="validateField(this)">
                        <p class="mt-1 text-xs text-red-500 hidden validation-message">Please enter a valid quantity</p>
                    </div>
                    
                    <div>
                        <label for="edit_cottage_price" class="block text-sm font-medium text-gray-700 mb-1">Price *</label>
                        <input type="number" step="0.01" id="edit_cottage_price" name="price" required min="0"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500"
                            oninput="validateField(this)">
                        <p class="mt-1 text-xs text-red-500 hidden validation-message">Please enter a valid price</p>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label for="edit_cottage_description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea id="edit_cottage_description" name="description" rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500"></textarea>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Add More Images</label>
                        <div class="drop-area mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label for="edit_cottage_images" class="relative cursor-pointer bg-white rounded-md font-medium text-red-600 hover:text-red-500 focus-within:outline-none">
                                        <span>Upload files</span>
                                        <input id="edit_cottage_images" name="images[]" type="file" multiple class="sr-only">
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
                            </div>
                        </div>
                        <div id="editCottageImagePreviews" class="mt-2 flex flex-wrap gap-2 hidden"></div>
                    </div>
                </div>
                
                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" onclick="closeEditCottageModal()" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md text-sm font-medium hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Update Cottage
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteCottageModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-800">Confirm Deletion</h3>
                <button onclick="closeDeleteCottageModal()" class="text-gray-400 hover:text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <p class="text-gray-600 mb-6">Are you sure you want to delete this cottage? This action cannot be undone and all associated images will be permanently removed.</p>
            
            <form id="deleteCottageForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeDeleteCottageModal()" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md text-sm font-medium hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Delete Cottage
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Image Gallery Modal -->
<div id="cottageImageModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] overflow-hidden">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-800">Cottage Images</h3>
                <button onclick="closeCottageImageModal()" class="text-gray-400 hover:text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <div id="cottageImageContainer" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 overflow-y-auto max-h-[70vh] p-2">
                <!-- Images will be loaded here via JavaScript -->
            </div>
        </div>
    </div>
</div>

<div id="discountModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
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

<!-- Include SortableJS for drag and drop -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>

<script>
// Global variables
let currentCottageId = null;
let uploadedCottageImages = [];
let currentPage = 1;
const perPage = 10;

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initDragAndDrop();
    loadCottages();
    
    // Add event listeners for image uploads
    document.getElementById('cottage_images').addEventListener('change', function(e) {
        handleCottageImageUpload(e, false);
    });
    
    document.getElementById('edit_cottage_images').addEventListener('change', function(e) {
        handleCottageImageUpload(e, true);
    });
    
    // Add real-time validation for required fields
    document.querySelectorAll('[required]').forEach(field => {
        field.addEventListener('input', function() {
            validateField(this);
        });
    });
    
    // Form submissions
    document.getElementById('addCottageForm').addEventListener('submit', function(e) {
        e.preventDefault();
        storeCottage(this);
    });
    
    document.getElementById('editCottageForm').addEventListener('submit', function(e) {
        e.preventDefault();
        submitCottageForm(this, 'PUT');
    });
    
    document.getElementById('deleteCottageForm').addEventListener('submit', function(e) {
        e.preventDefault();
        submitCottageForm(this, 'DELETE');
    });
});

// Load cottages via AJAX
function loadCottages(page = 1) {
    currentPage = page;
    const tableBody = document.getElementById('cottagesTableBody');
    tableBody.innerHTML = `
        <tr>
            <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-red-600 mx-auto"></div>
                <p class="mt-2">Loading cottages...</p>
            </td>
        </tr>
    `;
    
    fetch(`/admin/cottages?page=${page}&per_page=${perPage}`, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            renderCottagesTable(data.data);
            renderPagination(data);
        } else {
            throw new Error(data.message || 'Failed to load cottages');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        tableBody.innerHTML = `
            <tr>
                <td colspan="5" class="px-6 py-4 text-center text-sm text-red-500">
                    Failed to load cottages. ${error.message}
                    <button onclick="loadCottages(${page})" class="mt-2 px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                        Retry
                    </button>
                </td>
            </tr>
        `;
    });
}

function renderCottagesTable(cottages) {
    const tableBody = document.getElementById('cottagesTableBody');
    
    if (cottages.length === 0) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                    No cottages found. Add your first cottage.
                </td>
            </tr>
        `;
        return;
    }
    
    tableBody.innerHTML = '';
    
    cottages.forEach(cottage => {
        const row = document.createElement('tr');
        row.dataset.id = cottage.id;
        row.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="font-medium text-gray-900">${cottage.name}</div>
                ${cottage.description ? `<div class="text-sm text-gray-500 mt-1">${cottage.description.substring(0, 50)}${cottage.description.length > 50 ? '...' : ''}</div>` : ''}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                ${cottage.quantity}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                ₱${parseFloat(cottage.price).toFixed(2)} <span class="text-gray-400">/Per Entrance</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex -space-x-2">
                    ${cottage.images.slice(0, 3).map(image => `
                        <img 
                            class="h-10 w-10 rounded-full border-2 border-white object-cover cursor-pointer hover:scale-110 transition-transform" 
                            src="${image.url}" 
                            alt="Cottage image"
                            title="View all images"
                            onclick="openCottageImageModal('${cottage.id}')"
                        >
                    `).join('')}
                    ${cottage.images.length > 3 ? `
                        <span 
                            class="h-10 w-10 rounded-full border-2 border-white bg-gray-100 flex items-center justify-center text-xs font-medium text-gray-600 cursor-pointer hover:bg-gray-200"
                            onclick="openCottageImageModal('${cottage.id}')"
                        >
                            +${cottage.images.length - 3}
                        </span>
                    ` : ''}
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap flex items-center space-x-2">
                <!-- Edit Button -->
                <button 
                    onclick="openEditCottageModal('${cottage.id}')"
                    class="px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-md transition-all duration-200 flex items-center space-x-1.5 text-sm shadow-sm hover:shadow-md"
                    aria-label="Edit cottage"
                    title="Edit Cottage"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    <span>Edit</span>
                </button>
                
                <!-- Delete Button -->
                <button 
                    onclick="openDeleteCottageModal('${cottage.id}')"
                    class="px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-md transition-all duration-200 flex items-center space-x-1.5 text-sm shadow-sm hover:shadow-md"
                    aria-label="Delete cottage"
                    title="Delete Cottage"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    <span>Delete</span>
                </button>
                <button 
                    onclick="openDiscountModal('${cottage.id}')"
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
        tableBody.appendChild(row);
    });
}

function renderPagination(data) {
    const paginationContainer = document.getElementById('paginationContainer');
    
    if (data.last_page <= 1) {
        paginationContainer.innerHTML = '';
        return;
    }
    
    let paginationHTML = `
        <div class="flex-1 flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-700">
                    Showing <span class="font-medium">${data.from}</span> to <span class="font-medium">${data.to}</span> of <span class="font-medium">${data.total}</span> cottages
                </p>
            </div>
            <div class="flex space-x-2">
    `;
    
    // Previous button
    if (data.current_page > 1) {
        paginationHTML += `
            <button onclick="loadCottages(${data.current_page - 1})" class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                Previous
            </button>
        `;
    }
    
    // Page numbers
    for (let i = 1; i <= data.last_page; i++) {
        if (i === data.current_page) {
            paginationHTML += `
                <button class="px-3 py-1 bg-red-600 text-white rounded-md text-sm font-medium">
                    ${i}
                </button>
            `;
        } else {
            paginationHTML += `
                <button onclick="loadCottages(${i})" class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                    ${i}
                </button>
            `;
        }
    }
    
    // Next button
    if (data.current_page < data.last_page) {
        paginationHTML += `
            <button onclick="loadCottages(${data.current_page + 1})" class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                Next
            </button>
        `;
    }
    
    paginationHTML += `</div></div>`;
    paginationContainer.innerHTML = paginationHTML;
}

function storeCottage(form) {
    const formData = new FormData(form);
    const url = '/admin/cottages/store';
    
    // Show loading state
    const submitButton = form.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;
    submitButton.innerHTML = `Loading...`;
    submitButton.disabled = true;

    fetch(url, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
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
            // Refresh the table
            loadCottages(currentPage);
            // Close modal and reset form
            closeAddCottageModal();
        } else {
            throw new Error(data.message || 'Failed to store cottage');
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

// Modal functions
function openAddCottageModal() {
    resetAddCottageForm();
    document.getElementById('addCottageModal').classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

function closeAddCottageModal() {
    document.getElementById('addCottageModal').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
    resetAddCottageForm();
}

function openEditCottageModal(id) {
    currentCottageId = id;
    
    fetch(`/admin/cottages/${id}/edit`, {
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const cottage = data.cottage;
            
            document.getElementById('edit_cottage_name').value = cottage.name;
            document.getElementById('edit_cottage_quantity').value = cottage.quantity;
            document.getElementById('edit_cottage_price').value = cottage.price;
            document.getElementById('edit_cottage_description').value = cottage.description || '';
            
            document.getElementById('editCottageForm').action = `/admin/cottages/${id}`;
            document.getElementById('editCottageModal').classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        } else {
            throw new Error(data.message || 'Failed to load cottage data');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast(error.message, 'error');
    });
}

function closeEditCottageModal() {
    document.getElementById('editCottageModal').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
    currentCottageId = null;
    uploadedCottageImages = [];
}

function openDeleteCottageModal(id) {
    currentCottageId = id;
    document.getElementById('deleteCottageForm').action = `/admin/cottages/${id}`;
    document.getElementById('deleteCottageModal').classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

function closeDeleteCottageModal() {
    document.getElementById('deleteCottageModal').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
    currentCottageId = null;
}

function openCottageImageModal(cottageId) {
    currentCottageId = cottageId;
    fetchCottageImages(cottageId);
    document.getElementById('cottageImageModal').classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

function closeCottageImageModal() {
    document.getElementById('cottageImageModal').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
    currentCottageId = null;
    uploadedCottageImages = [];
}

// Image handling functions
function fetchCottageImages(cottageId) {
    const container = document.getElementById('cottageImageContainer');
    container.innerHTML = '<div class="col-span-full text-center py-8"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-red-600 mx-auto"></div><p class="mt-2 text-gray-500">Loading images...</p></div>';
    
    fetch(`/admin/cottages/${cottageId}/edit`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderCottageImageGallery(data.images);
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
                    <button onclick="fetchCottageImages(${cottageId})" class="mt-4 px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                        Retry
                    </button>
                </div>
            `;
        });
}

function renderCottageImageGallery(images) {
    const container = document.getElementById('cottageImageContainer');
    container.innerHTML = '';
    
    if (images.length === 0 && uploadedCottageImages.length === 0) {
        container.innerHTML = `
            <div class="col-span-full text-center py-8">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <p class="mt-2 text-gray-500">No images available for this cottage.</p>
                <p class="text-sm text-gray-400 mt-1">Drag & drop images here or click to upload</p>
            </div>
        `;
    } else {
        // Combine existing images with newly uploaded ones
        const allImages = [...images, ...uploadedCottageImages];
        
        allImages.forEach((image, index) => {
            const isNew = !image.id;
            const imgSrc = isNew ? image.preview : image.url;
            const imgId = isNew ? `new-${index}` : image.id;
            
            const imageElement = document.createElement('div');
            imageElement.className = 'relative group cursor-move bg-gray-100 rounded-lg overflow-hidden';
            imageElement.dataset.id = imgId;
            imageElement.innerHTML = `
                <img 
                    src="${imgSrc}" 
                    alt="Cottage image" 
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
                        onclick="deleteCottageImage('${imgId}', event)"
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
    new Sortable(document.getElementById('cottageImageContainer'), {
        animation: 150,
        ghostClass: 'bg-gray-200',
        chosenClass: 'bg-gray-100',
        dragClass: 'cursor-grabbing',
        onEnd: function() {
            const items = document.querySelectorAll('#cottageImageContainer > div');
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
function submitCottageForm(form, method) {
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
                addCottageToTable(data.cottage);
                closeAddCottageModal();
            } else if (method === 'PUT') {
                updateCottageInTable(data.cottage);
                closeEditCottageModal();
            } else if (method === 'DELETE') {
                document.querySelector(`#cottagesTableBody tr[data-id="${currentCottageId}"]`).remove();
                closeDeleteCottageModal();
            }
            loadCottages(currentPage);
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

function addCottageToTable(cottage) {
    const tableBody = document.getElementById('cottagesTableBody');
    const emptyRow = tableBody.querySelector('tr:first-child td[colspan]');
    
    if (emptyRow) {
        tableBody.innerHTML = '';
    }
    
    const row = document.createElement('tr');
    row.dataset.id = cottage.id;
    row.innerHTML = `
        <td class="px-6 py-4 whitespace-nowrap">
            <div class="font-medium text-gray-900">${cottage.name}</div>
            ${cottage.description ? `<div class="text-sm text-gray-500 mt-1">${cottage.description.substring(0, 50)}${cottage.description.length > 50 ? '...' : ''}</div>` : ''}
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
            ${cottage.quantity}
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
            ₱${parseFloat(cottage.price).toFixed(2)} <span class="text-gray-400">/night</span>
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
            <div class="flex -space-x-2">
                ${cottage.images.slice(0, 3).map(image => `
                    <img 
                        class="h-10 w-10 rounded-full border-2 border-white object-cover cursor-pointer hover:scale-110 transition-transform" 
                        src="${image.url}" 
                        alt="Cottage image"
                        title="View all images"
                        onclick="openCottageImageModal('${cottage.id}')"
                    >
                `).join('')}
                ${cottage.images.length > 3 ? `
                    <span 
                        class="h-10 w-10 rounded-full border-2 border-white bg-gray-100 flex items-center justify-center text-xs font-medium text-gray-600 cursor-pointer hover:bg-gray-200"
                        onclick="openCottageImageModal('${cottage.id}')"
                    >
                        +${cottage.images.length - 3}
                    </span>
                ` : ''}
            </div>
        </td>
        <td class="px-6 py-4 whitespace-nowrap flex items-center space-x-2">
            <!-- Edit Button -->
            <button 
                onclick="openEditCottageModal('${cottage.id}')"
                class="px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-md transition-all duration-200 flex items-center space-x-1.5 text-sm shadow-sm hover:shadow-md"
                aria-label="Edit cottage"
                title="Edit Cottage"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                <span>Edit</span>
            </button>
            
            <!-- Delete Button -->
            <button 
                onclick="openDeleteCottageModal('${cottage.id}')"
                class="px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-md transition-all duration-200 flex items-center space-x-1.5 text-sm shadow-sm hover:shadow-md"
                aria-label="Delete cottage"
                title="Delete Cottage"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                <span>Delete</span>
            </button>
            <button 
                onclick="openDiscountModal('${cottage.id}')"
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

function updateCottageInTable(cottage) {
    const row = document.querySelector(`#cottagesTableBody tr[data-id="${cottage.id}"]`);
    if (row) {
        row.querySelector('td:first-child div:first-child').textContent = cottage.name;
        if (cottage.description) {
            const descElement = row.querySelector('td:first-child div.text-sm') || 
                document.createElement('div');
            descElement.className = 'text-sm text-gray-500 mt-1';
            descElement.textContent = cottage.description.substring(0, 50) + 
                (cottage.description.length > 50 ? '...' : '');
            if (!row.querySelector('td:first-child div.text-sm')) {
                row.querySelector('td:first-child').appendChild(descElement);
            }
        } else {
            const descElement = row.querySelector('td:first-child div.text-sm');
            if (descElement) descElement.remove();
        }
        
        row.querySelector('td:nth-child(2)').textContent = cottage.quantity;
        row.querySelector('td:nth-child(3)').innerHTML = 
            `₱${parseFloat(cottage.price).toFixed(2)} <span class="text-gray-400">/night</span>`;
        
        // Update images
        const imagesContainer = row.querySelector('td:nth-child(4) div');
        imagesContainer.innerHTML = '';
        
        cottage.images.slice(0, 3).forEach(image => {
            const img = document.createElement('img');
            img.className = 'h-10 w-10 rounded-full border-2 border-white object-cover cursor-pointer hover:scale-110 transition-transform';
            img.src = image.url;
            img.alt = 'Cottage image';
            img.title = 'View all images';
            img.onclick = () => openCottageImageModal(cottage.id);
            imagesContainer.appendChild(img);
        });
        
        if (cottage.images.length > 3) {
            const moreSpan = document.createElement('span');
            moreSpan.className = 'h-10 w-10 rounded-full border-2 border-white bg-gray-100 flex items-center justify-center text-xs font-medium text-gray-600 cursor-pointer hover:bg-gray-200';
            moreSpan.textContent = `+${cottage.images.length - 3}`;
            moreSpan.onclick = () => openCottageImageModal(cottage.id);
            imagesContainer.appendChild(moreSpan);
        }
    }
}

// Image upload handling
function handleCottageImageUpload(event, isEdit = false) {
    const files = event.target.files;
    const container = isEdit ? 
        document.getElementById('editCottageImagePreviews') : 
        document.getElementById('addCottageImagePreviews');
    
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
                        onclick="removeCottageImagePreview(this, ${i}, ${isEdit})"
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
                uploadedCottageImages.push({
                    preview: previewUrl,
                    file: file
                });
            }
        }
        
        if (isEdit && currentCottageId) {
            fetchCottageImages(currentCottageId);
        }
    } else {
        container.classList.add('hidden');
    }
}

function removeCottageImagePreview(button, index, isEdit) {
    if (isEdit) {
        uploadedCottageImages.splice(index, 1);
        if (currentCottageId) {
            fetchCottageImages(currentCottageId);
        }
    }
    
    const container = button.closest('div.relative').parentNode;
    button.closest('div.relative').remove();
    
    // Remove the file from the input
    const input = isEdit ? 
        document.getElementById('edit_cottage_images') : 
        document.getElementById('cottage_images');
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

function deleteCottageImage(imageId, event) {
    event.preventDefault();
    event.stopPropagation();
    
    if (confirm('Are you sure you want to delete this image?')) {
        const isNewImage = imageId.startsWith('new-');
        
        if (isNewImage) {
            // ... existing code ...
        } else {
            // Delete from server
            fetch(`/admin/cottage-images/${imageId}`, {
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
                    const imageElement = document.querySelector(`#cottageImageContainer div[data-id="${imageId}"]`);
                    if (imageElement) {
                        imageElement.remove();
                    }
                    
                    // Reload the cottage data to update the list
                    loadCottages(currentPage);
                    
                    // If no images left, show empty state
                    if (document.querySelectorAll('#cottageImageContainer > div').length === 0) {
                        renderCottageEmptyState();
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

function renderCottageEmptyState() {
    const container = document.getElementById('cottageImageContainer');
    container.innerHTML = `
        <div class="col-span-full text-center py-8">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <p class="mt-2 text-gray-500">No images available for this cottage.</p>
            <p class="text-sm text-gray-400 mt-1">Drag & drop images here or click to upload</p>
        </div>
    `;
}

function updateCottageRowImages(cottageId) {
    // Fetch the updated cottage data to refresh the images in the table row
    fetch(`/admin/cottages/${cottageId}/edit`, {
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.cottage) {
            const cottage = data.cottage;
            const row = document.querySelector(`#cottagesTableBody tr[data-id="${cottageId}"]`);
            
            if (row) {
                const imagesContainer = row.querySelector('td:nth-child(4) div');
                imagesContainer.innerHTML = '';
                
                cottage.images.slice(0, 3).forEach(image => {
                    const img = document.createElement('img');
                    img.className = 'h-10 w-10 rounded-full border-2 border-white object-cover cursor-pointer hover:scale-110 transition-transform';
                    img.src = image.url;
                    img.alt = 'Cottage image';
                    img.title = 'View all images';
                    img.onclick = () => openCottageImageModal(cottageId);
                    imagesContainer.appendChild(img);
                });
                
                if (cottage.images.length > 3) {
                    const moreSpan = document.createElement('span');
                    moreSpan.className = 'h-10 w-10 rounded-full border-2 border-white bg-gray-100 flex items-center justify-center text-xs font-medium text-gray-600 cursor-pointer hover:bg-gray-200';
                    moreSpan.textContent = `+${cottage.images.length - 3}`;
                    moreSpan.onclick = () => openCottageImageModal(cottageId);
                    imagesContainer.appendChild(moreSpan);
                }
            }
        }
    })
    .catch(error => console.error('Error updating cottage row:', error));
}

// Utility functions
function resetAddCottageForm() {
    document.getElementById('addCottageForm').reset();
    document.getElementById('addCottageImagePreviews').innerHTML = '';
    document.getElementById('addCottageImagePreviews').classList.add('hidden');
    // Reset validation states
    document.querySelectorAll('#addCottageForm .validation-message').forEach(el => {
        el.classList.add('hidden');
    });
    document.querySelectorAll('#addCottageForm [required]').forEach(el => {
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
        const isEdit = e.target.closest('#editCottageModal');
        
        if (isEdit) {
            document.getElementById('edit_cottage_images').files = files;
            const event = new Event('change');
            document.getElementById('edit_cottage_images').dispatchEvent(event);
        } else {
            document.getElementById('cottage_images').files = files;
            const event = new Event('change');
            document.getElementById('cottage_images').dispatchEvent(event);
        }
    }
}
</script>

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
#cottageImageContainer {
    scrollbar-width: thin;
    scrollbar-color: #e5e7eb #f9fafb;
}

#cottageImageContainer::-webkit-scrollbar {
    width: 8px;
}

#cottageImageContainer::-webkit-scrollbar-track {
    background: #f9fafb;
}

#cottageImageContainer::-webkit-scrollbar-thumb {
    background-color: #e5e7eb;
    border-radius: 4px;
}
</style>
