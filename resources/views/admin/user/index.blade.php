@extends('layouts.admin')
@section('title', 'Admin Management')
@php
     $active = 'admin';
@endphp

@section('content')
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

     <div class="min-h-screen">
          <!-- Header -->
          <header class="bg-white shadow-sm">
               <div
                    class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex flex-col md:flex-row items-center justify-between gap-4 md:gap-0">
                    <div class="flex items-center">
                         <div class="bg-indigo-600 rounded-lg p-2 flex items-center justify-center">
                              <i class="fas fa-user-shield text-white text-xl md:text-2xl"></i>
                         </div>
                         <h1 class="ml-3 text-xl md:text-2xl font-bold text-gray-900">Admin Management</h1>
                    </div>
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center w-full md:w-auto gap-3">
                         <div class="relative flex-grow">
                              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                   <i class="fas fa-search text-gray-400"></i>
                              </div>
                              <input type="text" id="searchInput" placeholder="Search admins..."
                                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                         </div>
                         <button id="newAdminBtn"
                              class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg flex items-center justify-center">
                              <i class="fas fa-plus mr-2"></i> <span class="hidden sm:inline">New Admin</span>
                         </button>
                    </div>
               </div>
          </header>

          <!-- Main Content -->
          <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
               <!-- Stats Overview -->
               <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-8">
                    <div class="bg-white rounded-xl p-4 md:p-6 border border-gray-200">
                         <div class="flex items-center">
                              <div class="p-3 rounded-lg bg-indigo-100 text-indigo-600 flex-shrink-0">
                                   <i class="fas fa-user-shield text-lg md:text-xl"></i>
                              </div>
                              <div class="ml-4 overflow-hidden">
                                   <h2 class="text-gray-500 text-sm truncate">Total Admins</h2>
                                   <p class="text-xl md:text-2xl font-bold truncate" id="totalAdmins">0</p>
                              </div>
                         </div>
                    </div>
               </div>

               <!-- Admin List -->
               <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                    <div class="flex flex-col">
                         <div class="overflow-x-auto">
                              <div class="align-middle inline-block min-w-full">
                                   <div class="overflow-hidden">
                                        <table class="min-w-full divide-y divide-gray-200">
                                             <thead class="bg-gray-50">
                                                  <tr>
                                                       <th scope="col"
                                                            class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                            Admin
                                                       </th>
                                                       <th scope="col"
                                                            class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                            Email Verified
                                                       </th>
                                                       <th scope="col"
                                                            class="px-4 sm:px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                            Actions
                                                       </th>
                                                  </tr>
                                             </thead>
                                             <tbody id="adminList" class="bg-white divide-y divide-gray-200">
                                                  <!-- Data will be populated by JavaScript -->
                                             </tbody>
                                        </table>
                                   </div>
                              </div>
                         </div>
                    </div>
               </div>

               <!-- Loading State -->
               <div id="loadingState" class="text-center py-12">
                    <div class="flex justify-center">
                         <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mb-4"></div>
                    </div>
                    <p class="text-gray-500">Loading admin data...</p>
               </div>

               <!-- Empty State -->
               <div id="emptyState" class="hidden text-center py-12">
                    <i class="fas fa-user-shield text-gray-300 text-4xl md:text-5xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-700 mb-2">No admins found</h3>
                    <p class="text-gray-500">Get started by creating your first admin user.</p>
                    <button id="emptyStateNewAdminBtn"
                         class="mt-4 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg">
                         <i class="fas fa-plus mr-2"></i> New Admin
                    </button>
               </div>

               <!-- Error State -->
               <div id="errorState" class="hidden bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded">
                    <div class="flex items-start">
                         <div class="flex-shrink-0 mt-0.5">
                              <i class="fas fa-exclamation-circle text-red-400"></i>
                         </div>
                         <div class="ml-3">
                              <p class="text-sm text-red-700" id="errorMessage"></p>
                         </div>
                    </div>
               </div>
          </main>
     </div>

     <!-- New Admin Modal -->
     <div id="newAdminModal"
          class="fixed inset-0 bg-gray-600/50 backdrop-blur-sm flex items-center justify-center z-50 hidden">
          <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4 max-h-[90vh] overflow-y-auto">
               <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-900">Create New Admin</h3>
               </div>
               <form id="newAdminForm" class="px-6 py-4">
                    <div class="space-y-4">
                         <div>
                              <label for="firstname" class="block text-sm font-medium text-gray-700 mb-1">First Name
                                   *</label>
                              <input type="text" id="firstname" name="firstname" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                              <p class="mt-1 text-xs text-red-600 hidden" id="firstnameError"></p>
                         </div>
                         <div>
                              <label for="lastname" class="block text-sm font-medium text-gray-700 mb-1">Last Name *</label>
                              <input type="text" id="lastname" name="lastname" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                              <p class="mt-1 text-xs text-red-600 hidden" id="lastnameError"></p>
                         </div>
                         <div>
                              <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                              <input type="email" id="email" name="email" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                              <p class="mt-1 text-xs text-red-600 hidden" id="emailError"></p>
                         </div>
                         <div>
                              <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                              <input type="tel" id="phone" name="phone"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                              <p class="mt-1 text-xs text-red-600 hidden" id="phoneError"></p>
                         </div>
                         <div>
                              <label for="temp_password" class="block text-sm font-medium text-gray-700 mb-1">Temporary
                                   Password *</label>
                              <div class="relative">
                                   <input type="password" id="temp_password" name="temp_password" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 pr-16">
                                   <div class="absolute inset-y-0 right-0 flex items-center">
                                        <button type="button" id="generatePassword"
                                             class="px-3 h-full text-gray-400 hover:text-indigo-600 border-r border-gray-200">
                                             <i class="fas fa-sync-alt"></i>
                                        </button>
                                        <button type="button" id="togglePassword"
                                             class="px-3 h-full text-gray-400 hover:text-indigo-600">
                                             <i class="fas fa-eye"></i>
                                        </button>
                                   </div>
                              </div>
                              <p class="mt-1 text-xs text-gray-500">Minimum 8 characters with letters and numbers</p>
                              <p class="mt-1 text-xs text-red-600 hidden" id="temp_passwordError"></p>
                         </div>
                    </div>
               </form>
               <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                    <button type="button" id="cancelNewAdmin"
                         class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900">
                         Cancel
                    </button>
                    <button type="button" id="submitNewAdmin"
                         class="px-4 py-2 bg-indigo-600 text-sm font-medium text-white rounded-md hover:bg-indigo-700">
                         Create Admin
                    </button>
               </div>
          </div>
     </div>

     <!-- Edit Admin Modal -->
     <div id="editAdminModal"
          class="fixed inset-0 bg-gray-600/50 backdrop-blur-sm flex items-center justify-center z-50 hidden">
          <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4 max-h-[90vh] overflow-y-auto">
               <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-900">Edit Admin</h3>
               </div>
               <form id="editAdminForm" class="px-6 py-4">
                    <input type="hidden" id="edit_id" name="id">
                    <div class="space-y-4">
                         <div>
                              <label for="edit_firstname" class="block text-sm font-medium text-gray-700 mb-1">First
                                   Name *</label>
                              <input type="text" id="edit_firstname" name="firstname" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                              <p class="mt-1 text-xs text-red-600 hidden" id="edit_firstnameError"></p>
                         </div>
                         <div>
                              <label for="edit_lastname" class="block text-sm font-medium text-gray-700 mb-1">Last
                                   Name *</label>
                              <input type="text" id="edit_lastname" name="lastname" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                              <p class="mt-1 text-xs text-red-600 hidden" id="edit_lastnameError"></p>
                         </div>
                         <div>
                              <label for="edit_email" class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                              <input type="email" id="edit_email" name="email" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                              <p class="mt-1 text-xs text-red-600 hidden" id="edit_emailError"></p>
                         </div>
                         <div>
                              <label for="edit_phone" class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                              <input type="tel" id="edit_phone" name="phone"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                              <p class="mt-1 text-xs text-red-600 hidden" id="edit_phoneError"></p>
                         </div>
                    </div>
               </form>
               <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                    <button type="button" id="cancelEditAdmin"
                         class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900">
                         Cancel
                    </button>
                    <button type="button" id="submitEditAdmin"
                         class="px-4 py-2 bg-indigo-600 text-sm font-medium text-white rounded-md hover:bg-indigo-700">
                         Update Admin
                    </button>
               </div>
          </div>
     </div>

     <!-- Success Toast -->
     <div id="successToast"
          class="fixed top-4 right-4 z-50 transform transition-transform duration-300 translate-y-[-100px]">
          <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded shadow-lg max-w-sm">
               <div class="flex items-start">
                    <div class="flex-shrink-0 mt-0.5">
                         <i class="fas fa-check-circle text-green-400"></i>
                    </div>
                    <div class="ml-3">
                         <p class="text-sm text-green-700" id="successMessage"></p>
                    </div>
               </div>
          </div>
     </div>

     <script>
          document.addEventListener('DOMContentLoaded', function () {
               // Fetch data from the server
               fetchAdmins();

               // Search functionality
               document.getElementById('searchInput').addEventListener('input', function (e) {
                    filterAdmins();
               });

               // Modal functionality
               const newAdminModal = document.getElementById('newAdminModal');
               const editAdminModal = document.getElementById('editAdminModal');
               const newAdminBtn = document.getElementById('newAdminBtn');
               const emptyStateNewAdminBtn = document.getElementById('emptyStateNewAdminBtn');
               const cancelNewBtn = document.getElementById('cancelNewAdmin');
               const cancelEditBtn = document.getElementById('cancelEditAdmin');
               const submitNewBtn = document.getElementById('submitNewAdmin');
               const submitEditBtn = document.getElementById('submitEditAdmin');
               const generateBtn = document.getElementById('generatePassword');
               const togglePasswordBtn = document.getElementById('togglePassword');
               const passwordInput = document.getElementById('temp_password');

               // Password visibility toggle
               togglePasswordBtn.addEventListener('click', function () {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);

                    // Toggle eye icon
                    const eyeIcon = this.querySelector('i');
                    if (type === 'text') {
                         eyeIcon.classList.remove('fa-eye');
                         eyeIcon.classList.add('fa-eye-slash');
                    } else {
                         eyeIcon.classList.remove('fa-eye-slash');
                         eyeIcon.classList.add('fa-eye');
                    }
               });

               // Open new admin modal
               newAdminBtn.addEventListener('click', function () {
                    newAdminModal.classList.remove('hidden');
               });

               // Open new admin modal from empty state
               emptyStateNewAdminBtn.addEventListener('click', function () {
                    newAdminModal.classList.remove('hidden');
               });

               // Close new admin modal
               cancelNewBtn.addEventListener('click', function () {
                    newAdminModal.classList.add('hidden');
                    document.getElementById('newAdminForm').reset();
                    clearValidationErrors('newAdminForm');
                    // Reset password visibility
                    passwordInput.setAttribute('type', 'password');
                    const eyeIcon = togglePasswordBtn.querySelector('i');
                    eyeIcon.classList.remove('fa-eye-slash');
                    eyeIcon.classList.add('fa-eye');
               });

               // Close edit admin modal
               cancelEditBtn.addEventListener('click', function () {
                    editAdminModal.classList.add('hidden');
                    document.getElementById('editAdminForm').reset();
                    clearValidationErrors('editAdminForm');
               });

               // Close modals when clicking outside
               [newAdminModal, editAdminModal].forEach(modal => {
                    modal.addEventListener('click', function (e) {
                         if (e.target === modal) {
                              modal.classList.add('hidden');
                              if (modal === newAdminModal) {
                                   document.getElementById('newAdminForm').reset();
                                   clearValidationErrors('newAdminForm');
                                   // Reset password visibility
                                   passwordInput.setAttribute('type', 'password');
                                   const eyeIcon = togglePasswordBtn.querySelector('i');
                                   eyeIcon.classList.remove('fa-eye-slash');
                                   eyeIcon.classList.add('fa-eye');
                              } else {
                                   document.getElementById('editAdminForm').reset();
                                   clearValidationErrors('editAdminForm');
                              }
                         }
                    });
               });

               // Generate random password
               generateBtn.addEventListener('click', function () {
                    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*';
                    let password = '';
                    for (let i = 0; i < 12; i++) {
                         password += chars.charAt(Math.floor(Math.random() * chars.length));
                    }
                    passwordInput.value = password;
                    passwordInput.setAttribute('type', 'text');
                    const eyeIcon = togglePasswordBtn.querySelector('i');
                    eyeIcon.classList.remove('fa-eye');
                    eyeIcon.classList.add('fa-eye-slash');
               });

               // Submit new admin form
               submitNewBtn.addEventListener('click', function () {
                    const form = document.getElementById('newAdminForm');
                    const formData = new FormData(form);

                    // Clear previous errors
                    clearValidationErrors('newAdminForm');

                    // Send data to server
                    fetch('{{ route('admin.list.create') }}', {
                         method: 'POST',
                         headers: {
                              'Content-Type': 'application/json',
                              'X-CSRF-TOKEN': '{{ csrf_token() }}',
                              'Accept': 'application/json'
                         },
                         body: JSON.stringify(Object.fromEntries(formData))
                    })
                         .then(response => {
                              if (!response.ok) {
                                   return response.json().then(err => { throw err; });
                              }
                              return response.json();
                         })
                         .then(data => {
                              if (data.success) {
                                   showSuccess('Admin created successfully!');
                                   newAdminModal.classList.add('hidden');
                                   form.reset();
                                   fetchAdmins(); // Refresh the admin list
                              } else {
                                   throw data;
                              }
                         })
                         .catch(error => {
                              console.error('Error:', error);
                              if (error.errors) {
                                   displayValidationErrors(error.errors, 'newAdminForm');
                              } else {
                                   alert('An error occurred while creating the admin: ' + (error.message || 'Unknown error'));
                              }
                         });
               });

               // Submit edit admin form
               submitEditBtn.addEventListener('click', function () {
                    const form = document.getElementById('editAdminForm');
                    const formData = new FormData(form);
                    const adminId = formData.get('id');

                    // Clear previous errors
                    clearValidationErrors('editAdminForm');

                    // Prepare data for API
                    const dataToSend = {
                         firstname: formData.get('firstname'),
                         lastname: formData.get('lastname'),
                         email: formData.get('email'),
                         phone: formData.get('phone'),
                         reset_password: formData.get('reset_password') === 'on'
                    };

                    // Send data to server
                    fetch(`/admin-management/update/${adminId}`, {
                         method: 'PUT',
                         headers: {
                              'Content-Type': 'application/json',
                              'X-CSRF-TOKEN': '{{ csrf_token() }}',
                              'Accept': 'application/json'
                         },
                         body: JSON.stringify(dataToSend)
                    })
                         .then(response => {
                              if (!response.ok) {
                                   return response.json().then(err => { throw err; });
                              }
                              return response.json();
                         })
                         .then(data => {
                              if (data.success) {
                                   showSuccess('Admin updated successfully!');
                                   editAdminModal.classList.add('hidden');
                                   fetchAdmins(); // Refresh the admin list
                              } else {
                                   throw data;
                              }
                         })
                         .catch(error => {
                              console.error('Error:', error);
                              if (error.errors) {
                                   displayValidationErrors(error.errors, 'editAdminForm');
                              } else {
                                   alert('An error occurred while updating the admin: ' + (error.message || 'Unknown error'));
                              }
                         });
               });
          });

          let allAdmins = [];

          // Function to fetch admins from the server
          function fetchAdmins() {
               // Show loading state
               document.getElementById('loadingState').classList.remove('hidden');
               document.getElementById('errorState').classList.add('hidden');
               document.getElementById('emptyState').classList.add('hidden');

               fetch('{{ route('admin.list.data') }}', {
                    method: 'GET',
                    headers: {
                         'Content-Type': 'application/json',
                         'X-Requested-With': 'XMLHttpRequest',
                         'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
               })
                    .then(response => {
                         if (!response.ok) {
                              throw new Error('Network response was not ok');
                         }
                         return response.json();
                    })
                    .then(data => {
                         allAdmins = data.admins;
                         displayAdmins(data);
                    })
                    .catch(error => {
                         showError('Failed to load admin data: ' + error.message);
                    });
          }

          // Function to display admins in the table
          function displayAdmins(data) {
               // Hide loading state
               document.getElementById('loadingState').classList.add('hidden');

               // Update stats
               document.getElementById('totalAdmins').textContent = data.stats.total;

               // Get the table body element
               const adminList = document.getElementById('adminList');

               // Show empty state if no admins
               if (data.admins.length === 0) {
                    adminList.innerHTML = '';
                    document.getElementById('emptyState').classList.remove('hidden');
                    return;
               }

               // Hide empty state
               document.getElementById('emptyState').classList.add('hidden');

               adminList.innerHTML = '';

               // Add each admin to the table
               data.admins.forEach(admin => {
                    const row = document.createElement('tr');
                    row.className = 'animate-fadeIn';

                    // Format date for email verification status
                    const emailVerified = admin.email_verified_at
                         ? new Date(admin.email_verified_at).toLocaleDateString()
                         : 'Not verified';

                    row.innerHTML = `
                               <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                   <div class="flex items-center">
                                       <div class="flex-shrink-0 h-10 w-10">
                                        <img class="h-10 w-10 rounded-full object-cover"
                                                       src="${admin.profile_img
                              ? `/imgs/profiles/${admin.profile_img}`
                              : `https://ui-avatars.com/api/?name=${encodeURIComponent(admin.firstname + ' ' + admin.lastname)}&background=4f46e5&color=fff`}"
                                                                 alt="${admin.firstname} ${admin.lastname}">
                                       </div>
                                       <div class="ml-4 min-w-0">
                                           <p class="text-sm font-medium text-gray-900 truncate">${admin.firstname} ${admin.lastname}</p>
                                           <p class="text-sm text-gray-500 truncate">${admin.email}</p>
                                           ${admin.phone ? `<p class="text-sm text-gray-500 truncate">${admin.phone}</p>` : ''}
                                       </div>
                                   </div>
                               </td>
                               <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                   <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${admin.email_verified_at ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'}">
                                       ${emailVerified}
                                   </span>
                               </td>
                               <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                   <div class="flex items-center justify-end space-x-3">
                                       <button class="text-indigo-600 hover:text-indigo-900 edit-admin-btn" data-id="${admin.id}">
                                           <i class="fas fa-edit mr-1"></i> Edit
                                       </button>
                                       <button class="text-yellow-600 hover:text-yellow-900 reset-password-btn" data-id="${admin.id}" title="Reset Password">
                                           <i class="fas fa-key mr-1"></i> Reset
                                       </button>
                                       <button class="text-red-600 hover:text-red-900 delete-admin-btn" data-id="${admin.id}">
                                           <i class="fas fa-trash-alt mr-1"></i> Delete
                                       </button>
                                   </div>
                               </td>
                               `;

                    adminList.appendChild(row);
               });

               // Add event listeners to edit buttons
               document.querySelectorAll('.edit-admin-btn').forEach(button => {
                    button.addEventListener('click', function () {
                         const adminId = this.getAttribute('data-id');
                         openEditModal(adminId);
                    });
               });

               // Add event listeners to reset password buttons
               document.querySelectorAll('.reset-password-btn').forEach(button => {
                    button.addEventListener('click', function () {
                         const adminId = this.getAttribute('data-id');
                         resetPassword(adminId);
                    });
               });

               // Add event listeners to delete buttons
               document.querySelectorAll('.delete-admin-btn').forEach(button => {
                    button.addEventListener('click', function () {
                         const adminId = this.getAttribute('data-id');
                         deleteAdmin(adminId);
                    });
               });
          }

          // Function to open edit modal with admin data
          function openEditModal(adminId) {
               // Find the admin in our stored data
               const admin = allAdmins.find(a => a.id == adminId);

               if (!admin) {
                    alert('Admin data not found');
                    return;
               }

               // Populate the form with admin data
               document.getElementById('edit_id').value = admin.id;
               document.getElementById('edit_firstname').value = admin.firstname;
               document.getElementById('edit_lastname').value = admin.lastname;
               document.getElementById('edit_email').value = admin.email;
               document.getElementById('edit_phone').value = admin.phone || '';

               // Show the modal
               document.getElementById('editAdminModal').classList.remove('hidden');
          }

          // Function to reset password
          function resetPassword(adminId) {
               if (!confirm('Are you sure you want to reset this admin\'s password?')) {
                    return;
               }

               fetch(`/admin-management/reset-password/${adminId}`, {
                    method: 'POST',
                    headers: {
                         'Content-Type': 'application/json',
                         'X-CSRF-TOKEN': '{{ csrf_token() }}',
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
                              showSuccess('Password reset successfully! Temporary password: ' + data.temp_password);
                         } else {
                              throw data;
                         }
                    })
                    .catch(error => {
                         console.error('Error:', error);
                         alert('An error occurred while resetting the password: ' + (error.message || 'Unknown error'));
                    });
          }

          // Function to delete an admin
          function deleteAdmin(adminId) {
               if (!confirm('Are you sure you want to delete this admin? This action cannot be undone.')) {
                    return;
               }

               fetch(`/admin-management/delete/${adminId}`, {
                    method: 'DELETE',
                    headers: {
                         'Content-Type': 'application/json',
                         'X-CSRF-TOKEN': '{{ csrf_token() }}',
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
                              showSuccess('Admin deleted successfully!');
                              fetchAdmins(); // Refresh the admin list
                         } else {
                              throw data;
                         }
                    })
                    .catch(error => {
                         console.error('Error:', error);
                         alert('An error occurred while deleting the admin: ' + (error.message || 'Unknown error'));
                    });
          }

          // Function to filter admins based on search input
          function filterAdmins() {
               const searchTerm = document.getElementById('searchInput').value.toLowerCase();
               const adminList = document.getElementById('adminList');
               const rows = adminList.getElementsByTagName('tr');

               let visibleCount = 0;

               for (let i = 0; i < rows.length; i++) {
                    const name = rows[i].querySelector('.text-sm.font-medium').textContent.toLowerCase();
                    const email = rows[i].querySelector('.text-gray-500').textContent.toLowerCase();
                    const phoneElement = rows[i].querySelector('.text-gray-500:last-child');
                    const phone = phoneElement ? phoneElement.textContent.toLowerCase() : '';

                    if (name.includes(searchTerm) || email.includes(searchTerm) || phone.includes(searchTerm)) {
                         rows[i].classList.remove('hidden');
                         visibleCount++;
                    } else {
                         rows[i].classList.add('hidden');
                    }
               }

               // Show empty state if no results
               if (visibleCount === 0 && searchTerm) {
                    document.getElementById('emptyState').classList.remove('hidden');
               } else {
                    document.getElementById('emptyState').classList.add('hidden');
               }
          }

          // Function to show error message
          function showError(message) {
               document.getElementById('loadingState').classList.add('hidden');
               document.getElementById('errorState').classList.remove('hidden');
               document.getElementById('errorMessage').textContent = message;
          }

          // Function to show success toast
          function showSuccess(message) {
               const toast = document.getElementById('successToast');
               document.getElementById('successMessage').textContent = message;

               toast.classList.remove('translate-y-[-100px]');
               toast.classList.add('translate-y-0');

               setTimeout(() => {
                    toast.classList.remove('translate-y-0');
                    toast.classList.add('translate-y-[-100px]');
               }, 3000);
          }

          // Function to display validation errors
          function displayValidationErrors(errors, formType) {
               const prefix = formType === 'newAdminForm' ? '' : 'edit_';

               // Clear all errors first
               const allErrorElements = document.querySelectorAll(`[id$="Error"]`);
               allErrorElements.forEach(element => {
                    element.textContent = '';
                    element.classList.add('hidden');
               });

               // Display new errors
               for (const field in errors) {
                    // Convert field name from snake_case to camelCase if needed
                    const fieldName = field.replace(/_([a-z])/g, (g) => g[1].toUpperCase());
                    const errorElement = document.getElementById(`${prefix}${fieldName}Error`);

                    if (errorElement) {
                         errorElement.textContent = errors[field][0];
                         errorElement.classList.remove('hidden');

                         // Also highlight the input field
                         const inputElement = document.getElementById(`${prefix}${fieldName}`);
                         if (inputElement) {
                              inputElement.classList.add('border-red-500');

                              // Remove error styling after 5 seconds
                              setTimeout(() => {
                                   inputElement.classList.remove('border-red-500');
                              }, 5000);
                         }
                    }
               }
          }

          // Function to clear validation errors
          function clearValidationErrors(formType) {
               const errorElements = document.querySelectorAll(`#${formType} [id$="Error"]`);
               errorElements.forEach(element => {
                    element.textContent = '';
                    element.classList.add('hidden');
               });
          }
     </script>

     <style>
          .animate-fadeIn {
               animation: fadeIn 0.5s;
          }

          @keyframes fadeIn {
               from {
                    opacity: 0;
               }

               to {
                    opacity: 1;
               }
          }
     </style>
@endsection