@extends('layouts.admin')
@php
$active = 'email';
@endphp

@section('content')
<div class="container mx-auto px-4 py-4">
    <div class="bg-white rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-white flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">📬 Email Inbox</h1>
                <p class="text-sm text-gray-600 mt-1">View and manage incoming booking replies</p>
            </div>
            <div class="flex space-x-2">
                <button id="refreshInbox" class="flex items-center text-blue-600 hover:text-blue-800">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" />
                    </svg>
                    Refresh
                </button>
            </div>
        </div>
        
        <div class="p-6">
            <div id="notificationArea">
                <div id="errorAlert" class="hidden bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <strong id="errorMessage" class="font-medium"></strong>
                    </div>
                    <span id="errorDetails" class="block sm:inline ml-8"></span>
                </div>
                
                <div id="successAlert" class="hidden bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <strong id="successMessage" class="font-medium"></strong>
                    </div>
                </div>
            </div>
            
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <select id="emailLimit" class="appearance-none bg-white border border-gray-300 rounded-md pl-3 pr-8 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="10">Show 10 emails</option>
                            <option value="20" selected>Show 20 emails</option>
                            <option value="50">Show 50 emails</option>
                        </select>
                    </div>
                    <button id="markAllRead" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 py-2 rounded-md text-sm transition-colors duration-200">
                        Mark All as Read
                    </button>
                </div>
                <div class="text-sm text-gray-500" id="inboxStatus">
                    Last refreshed: Never
                </div>
            </div>
            
            <div id="emailList" class="space-y-3">
                <div class="text-center text-gray-500 py-8">
                    <svg class="animate-spin -ml-1 mr-3 h-8 w-8 text-gray-400 inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <p class="mt-2">Loading inbox...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Email Detail Modal -->
<div id="emailModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div id="modalBackdrop" class="fixed inset-0 bg-black/50 backdrop-blur-sm" aria-hidden="true"></div>
        <!-- Modal container -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full relative z-50">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modalSubject">Email Details</h3>
                            <button id="closeModal" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <div class="mt-4 border-t border-gray-200 pt-4">
                            <div class="flex items-center mb-4">
                                <div class="bg-blue-500 text-white rounded-full h-10 w-10 flex items-center justify-center mr-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900" id="modalFrom">From: Loading...</p>
                                    <p class="text-xs text-gray-500" id="modalDate">Date: Loading...</p>
                                </div>
                            </div>
                            
                            <!-- Email Content Container -->
                            <div class="space-y-4">
                                <!-- Customer's Reply Section -->
                                <div class="bg-blue-50 p-4 rounded-md border border-blue-100">
                                    <div class="flex items-center mb-2">
                                        <div class="bg-blue-100 text-blue-800 rounded-full h-6 w-6 flex items-center justify-center mr-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                                            </svg>
                                        </div>
                                        <span class="text-xs font-medium text-blue-800">CUSTOMER REPLY</span>
                                    </div>
                                    <div class="prose max-w-none text-sm" id="customerReply">
                                        Loading customer reply...
                                    </div>
                                </div>

                                <div class="mt-4" id="attachmentsContainer">
                                    <div class="flex items-center mb-2">
                                        <div class="bg-gray-200 text-gray-600 rounded-full h-6 w-6 flex items-center justify-center mr-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                            </svg>
                                        </div>
                                        <span class="text-xs font-medium text-gray-600">ATTACHMENTS</span>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2" id="attachmentsList">
                                        <!-- Attachments will be dynamically inserted here -->
                                    </div>
                                </div>
                                
                                <!-- Original Email Section -->
                                <div class="bg-gray-50 p-4 rounded-md border border-gray-200">
                                    <div class="flex items-center mb-2">
                                        <div class="bg-gray-200 text-gray-600 rounded-full h-6 w-6 flex items-center justify-center mr-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <span class="text-xs font-medium text-gray-600">ORIGINAL MESSAGE</span>
                                    </div>
                                    <div class="prose max-w-none text-sm text-gray-600" id="originalEmail">
                                        Loading original email...
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" id="replyButton" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Reply
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content_js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // DOM Elements
    const refreshBtn = document.getElementById('refreshInbox');
    const emailList = document.getElementById('emailList');
    const markAllReadBtn = document.getElementById('markAllRead');
    const errorAlert = document.getElementById('errorAlert');
    const errorMessage = document.getElementById('errorMessage');
    const errorDetails = document.getElementById('errorDetails');
    const successAlert = document.getElementById('successAlert');
    const successMessage = document.getElementById('successMessage');
    const inboxStatus = document.getElementById('inboxStatus');
    const emailLimit = document.getElementById('emailLimit');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    
    // Modal Elements
    const emailModal = document.getElementById('emailModal');
    const modalBackdrop = document.getElementById('modalBackdrop');
    const modalSubject = document.getElementById('modalSubject');
    const modalFrom = document.getElementById('modalFrom');
    const modalDate = document.getElementById('modalDate');
    const customerReply = document.getElementById('customerReply');
    const originalEmail = document.getElementById('originalEmail');
    const closeModal = document.getElementById('closeModal');
    const replyButton = document.getElementById('replyButton');
    
    // State variables
    let currentEmails = [];
    let selectedEmailId = null;
    let unreadCount = 0;
    
    // Initialize
    loadInbox();
    
    // Event Listeners
    refreshBtn.addEventListener('click', loadInbox);
    emailLimit.addEventListener('change', loadInbox);
    markAllReadBtn.addEventListener('click', markAllAsRead);
    closeModal.addEventListener('click', hideModal);
    modalBackdrop.addEventListener('click', hideModal);
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !emailModal.classList.contains('hidden')) {
            hideModal();
        }
    });
    
    // Functions
    function loadInbox() {
        const limit = emailLimit.value;
        
        emailList.innerHTML = `
            <div class="text-center text-gray-500 py-8">
                <svg class="animate-spin -ml-1 mr-3 h-8 w-8 text-gray-400 inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="mt-2">Loading inbox...</p>
            </div>`;
        
        fetch(`/inbox?limit=${limit}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(async response => {
            if (!response.ok) {
                const errorData = await response.json().catch(() => ({}));
                throw new Error(errorData.message || `Server error: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                currentEmails = data.emails;
                unreadCount = data.unread_count || 0;
                renderEmails();
                updateInboxStatus();
                updateUnreadCount();
                showSuccess('Inbox refreshed successfully');
            } else {
                throw new Error(data.message || 'Failed to fetch inbox');
            }
        })
        .catch(err => {
            console.error('Error:', err);
            showError('Error fetching inbox', err.message);
            emailList.innerHTML = `
                <div class="text-center text-gray-500 py-8">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <p class="mt-2 text-lg font-medium">Failed to load emails</p>
                    <p class="mt-1 text-sm">${err.message}</p>
                    <button onclick="loadInbox()" class="mt-4 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">
                        Try Again
                    </button>
                </div>`;
        });
    }
    
    function renderEmails() {
        if (!currentEmails || !currentEmails.length) {
            emailList.innerHTML = `
                <div class="text-center text-gray-500 py-8">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <p class="mt-2 text-lg font-medium">No emails found</p>
                    <p class="mt-1">Your inbox is empty</p>
                </div>`;
            return;
        }
        
        let html = '';
        currentEmails.forEach(email => {
            const bodyText = typeof email.body === 'string' ? email.body : '';
            const bodyPreview = bodyText.length > 150 
                ? bodyText.substring(0, 150) + "..." 
                : bodyText || '(No body content)';
            
            const subject = typeof email.subject === 'string' ? email.subject : '(No Subject)';
            const fromName = typeof email.from_name === 'string' ? email.from_name : '';
            const fromEmail = typeof email.from === 'string' ? email.from : 'unknown';
            const date = email.date ? new Date(email.date).toLocaleString() : 'No date';
            
            // Styles based on read status
            const fontWeightClass = email.is_read ? 'font-normal' : 'font-semibold';
            const bgColorClass = email.is_read ? 'bg-blue-100' : 'bg-blue-200';
            const textColorClass = email.is_read ? 'text-gray-600' : 'text-gray-800';
            
            html += `
                <div class="block p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors duration-150 cursor-pointer email-item ${fontWeightClass}" data-id="${email.id}" data-read="${email.is_read}">
                    <div class="flex justify-between items-start">
                        <div class="flex items-start w-full">
                            <div class="${bgColorClass} text-blue-800 rounded-full h-10 w-10 flex items-center justify-center mr-3 flex-shrink-0">
                                ${email.has_attachments ? `
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                    </svg>
                                ` : `
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                    </svg>
                                `}
                            </div>
                            <div class="w-full">
                                <div class="flex justify-between items-start w-full">
                                    <h6 class="${textColorClass} mb-1 truncate">${escapeHtml(subject)}</h6>
                                    <small class="text-gray-500 whitespace-nowrap ml-2">${date}</small>
                                </div>
                                <p class="text-gray-600 mb-2 text-sm line-clamp-2">${escapeHtml(bodyPreview)}</p>
                                <small class="text-gray-500 truncate block">From: ${escapeHtml(fromName)} &lt;${escapeHtml(fromEmail)}&gt;</small>
                            </div>
                        </div>
                    </div>
                </div>`;
        });
        
        emailList.innerHTML = html;
        
        // Add click handlers to email items
        document.querySelectorAll('.email-item').forEach(item => {
            item.addEventListener('click', () => {
                const emailId = item.getAttribute('data-id');
                const isRead = item.getAttribute('data-read') === 'true';
                const email = currentEmails.find(e => e.id == emailId);
                
                if (email) {
                    showEmailDetails(email);
                    
                    // Mark as read if not already read
                    if (!isRead) {
                        markEmailAsRead(emailId, item);
                    }
                }
            });
        });
    }
    
    function markEmailAsRead(emailId, element) {
        console.log('Attempting to mark email as read:', emailId);
        
        fetch('/email/mark-as-read', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ email_id: emailId })
        })
        .then(async response => {
            console.log('Raw response:', response);
            const data = await response.json();
            console.log('Parsed response:', data);
            
            if (!response.ok) {
                throw new Error(data.error || data.message || `Server error: ${response.status}`);
            }
            return data;
        })
        .then(data => {
            if (data.success) {
                // Update the UI immediately
                if (element) {
                    element.classList.remove('font-semibold');
                    element.classList.add('font-normal');
                    const bgElement = element.querySelector('.bg-blue-200');
                    if (bgElement) {
                        bgElement.classList.replace('bg-blue-200', 'bg-blue-100');
                    }
                    element.setAttribute('data-read', 'true');
                }
                
                // Update the unread count
                unreadCount = Math.max(0, unreadCount - 1);
                updateUnreadCount();
                
                // Update the email in currentEmails
                const emailIndex = currentEmails.findIndex(e => e.id == emailId);
                if (emailIndex !== -1) {
                    currentEmails[emailIndex].is_read = true;
                }
            } else {
                throw new Error(data.message || 'Failed to mark email as read');
            }
        })
        .catch(error => {
            console.error('Error marking email as read:', error);
            
            let errorMsg = error.message;
            let details = '';
            
            // Handle specific error cases
            if (error.message.includes('Failed to connect to IMAP')) {
                errorMsg = 'Email server connection failed';
                details = 'Please check your email server settings and try again.';
            } else if (error.message.includes('Email not found')) {
                errorMsg = 'Email no longer exists';
                details = 'The email may have been moved or deleted from the server.';
            } else if (error.message.includes('read flag')) {
                errorMsg = 'Permission denied';
                details = 'Your account doesn\'t have permission to modify this email.';
            }
            
            showError(errorMsg, details);
            
            // If the error is likely temporary, offer a retry button
            if (!error.message.includes('Email not found')) {
                const retryBtn = document.createElement('button');
                retryBtn.className = 'ml-2 text-blue-600 hover:underline';
                retryBtn.textContent = 'Retry';
                retryBtn.onclick = () => markEmailAsRead(emailId, element);
                
                errorDetails.appendChild(retryBtn);
            }
        });
    }
    
    function markAllAsRead() {
        const unreadEmails = currentEmails.filter(email => !email.is_read);
        if (unreadEmails.length === 0) {
            showSuccess('No unread emails to mark');
            return;
        }
        
        if (!confirm(`Mark ${unreadEmails.length} email(s) as read?`)) {
            return;
        }
        
        const promises = unreadEmails.map(email => {
            return fetch(`/email/mark-as-read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ email_id: email.id })
            });
        });
        
        Promise.all(promises)
            .then(responses => Promise.all(responses.map(r => r.json())))
            .then(results => {
                const successCount = results.filter(r => r.success).length;
                if (successCount > 0) {
                    // Update all emails in UI
                    document.querySelectorAll('.email-item[data-read="false"]').forEach(item => {
                        item.classList.remove('font-semibold');
                        item.classList.add('font-normal');
                        item.querySelector('.bg-blue-200').classList.replace('bg-blue-200', 'bg-blue-100');
                        item.setAttribute('data-read', 'true');
                    });
                    
                    // Update the unread count
                    unreadCount = 0;
                    updateUnreadCount();
                    
                    // Update currentEmails
                    currentEmails.forEach(email => {
                        email.is_read = true;
                    });
                    
                    showSuccess(`Marked ${successCount} email(s) as read`);
                }
            })
            .catch(error => {
                console.error('Error marking emails as read:', error);
                showError('Failed to mark some emails as read');
            });
    }
    
    function updateUnreadCount() {
        // Update the badge in the header
        document.querySelectorAll('.email-badge').forEach(badge => {
            badge.textContent = unreadCount;
            badge.classList.toggle('hidden', unreadCount === 0);
        });
        
        // Update the mark all as read button state
        markAllReadBtn.disabled = unreadCount === 0;
    }
    
    function showEmailDetails(email) {
        selectedEmailId = email.id;
        
        modalSubject.textContent = email.subject || '(No Subject)';
        modalFrom.textContent = `From: ${email.from_name || ''} <${email.from || 'unknown'}>`;
        modalDate.textContent = `Date: ${email.date ? new Date(email.date).toLocaleString() : 'No date'}`;
        
        // Parse the email body to separate reply from original message
        const emailContent = parseEmailContent(email.body || '(No body content)');
        
        // Format and display the customer's reply
        let replyContent = emailContent.reply || emailContent.full;
        replyContent = formatEmailContent(replyContent);
        customerReply.innerHTML = replyContent;
        
        // Format and display the original email if available
        let originalContent = emailContent.original ? 
            `Original message from: ${emailContent.originalFrom || 'Unknown'}${emailContent.original}` : 
            'No original message found';
        originalContent = formatEmailContent(originalContent);
        originalEmail.innerHTML = originalContent;
        
        // Show modal
        document.body.classList.add('overflow-hidden');
        emailModal.classList.remove('hidden');
        
        // Focus the close button for better keyboard navigation
        closeModal.focus();
    }

    function parseEmailContent(body) {
        // Common reply patterns to split the email
        const patterns = [
            /^On.*[\r\n]+.*wrote:[\r\n]+/m,  // "On [date], [name] wrote:"
            /^From:.*[\r\n]+.*[\r\n]+.*[\r\n]+/m,  // "From: [name] <email>"
            /^-+Original Message-+[\r\n]+/m,  // "-----Original Message-----"
            /^From:.*[\r\n]+Sent:.*[\r\n]+To:.*[\r\n]+Subject:.*[\r\n]+/m,  // Outlook style
            /\n[>]+/  // Quoted text with >
        ];
        
        let reply = body;
        let original = '';
        let originalFrom = '';
        
        // Try each pattern to find the best split
        for (const pattern of patterns) {
            const match = body.match(pattern);
            if (match) {
                const splitIndex = match.index;
                reply = body.substring(0, splitIndex).trim();
                original = body.substring(splitIndex).trim();
                
                // Try to extract original sender
                const fromMatch = original.match(/From:\s*(.*)/i);
                if (fromMatch) {
                    originalFrom = fromMatch[1];
                }
                
                break;
            }
        }
        
        return {
            full: body,
            reply: reply,
            original: original,
            originalFrom: originalFrom
        };
    }
    
    function formatEmailContent(content) {
        if (!content) return '(No content)';
        
        // Basic formatting
        let formatted = escapeHtml(content);
        formatted = formatted.replace(/\n/g, '<br>');
        formatted = formatted.replace(/(https?:\/\/[^\s]+)/g, '<a href="$1" target="_blank" class="text-blue-600 hover:underline">$1</a>');
        
        // Format quoted text
        formatted = formatted.replace(/^(&gt;.*<br>)+/gm, '<span class="text-gray-500 text-opacity-75">$&</span>');
        
        return formatted;
    }
    
    function hideModal() {
        document.body.classList.remove('overflow-hidden');
        emailModal.classList.add('hidden');
    }
    
    function updateInboxStatus() {
        const now = new Date();
        inboxStatus.textContent = `Last refreshed: ${now.toLocaleTimeString()}`;
    }
    
    function showError(title, details = '') {
        errorMessage.textContent = title;
        errorDetails.textContent = details;
        errorAlert.classList.remove('hidden');
        successAlert.classList.add('hidden');
        
        setTimeout(() => {
            errorAlert.classList.add('hidden');
        }, 5000);
    }
    
    function showSuccess(message) {
        successMessage.textContent = message;
        successAlert.classList.remove('hidden');
        errorAlert.classList.add('hidden');
        
        setTimeout(() => {
            successAlert.classList.add('hidden');
        }, 3000);
    }
    
    function escapeHtml(unsafe) {
        if (typeof unsafe !== 'string') return '';
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    // Button handlers
    replyButton.addEventListener('click', function() {
        if (selectedEmailId) {
            const email = currentEmails.find(e => e.id == selectedEmailId);
            if (email) {
                // Implement your reply functionality here
                alert(`Reply to: ${email.from}`);
                hideModal();
            }
        }
    });

    function showEmailDetails(email) {
        selectedEmailId = email.id;
        
        modalSubject.textContent = email.subject || '(No Subject)';
        modalFrom.textContent = `From: ${email.from_name || ''} <${email.from || 'unknown'}>`;
        modalDate.textContent = `Date: ${email.date ? new Date(email.date).toLocaleString() : 'No date'}`;
        
        // Parse the email body to separate reply from original message
        const emailContent = parseEmailContent(email.body || '(No body content)');
        
        // Format and display the customer's reply
        let replyContent = emailContent.reply || emailContent.full;
        replyContent = formatEmailContent(replyContent);
        customerReply.innerHTML = replyContent;
        
        // Format and display the original email if available
        let originalContent = emailContent.original ? 
            `Original message from: ${emailContent.originalFrom || 'Unknown'}${emailContent.original}` : 
            'No original message found';
        originalContent = formatEmailContent(originalContent);
        originalEmail.innerHTML = originalContent;
        
        // Handle attachments
        const attachmentsContainer = document.getElementById('attachmentsContainer');
        const attachmentsList = document.getElementById('attachmentsList');
        
        if (email.attachments && email.attachments.length > 0) {
            attachmentsContainer.classList.remove('hidden');
            attachmentsList.innerHTML = '';
            
            email.attachments.forEach(attachment => {
                const sizeKB = Math.round(attachment.size / 1024);
                const icon = getAttachmentIcon(attachment.mime);
                
                attachmentsList.innerHTML += `
                    <a href="${attachment.download_url}" 
                       class="flex items-center p-2 border border-gray-200 rounded hover:bg-gray-50 transition-colors"
                       download="${attachment.name}">
                        <div class="text-gray-500 mr-3">${icon}</div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">${escapeHtml(attachment.name)}</p>
                            <p class="text-xs text-gray-500">${sizeKB} KB • ${attachment.mime.split('/')[1] || 'file'}</p>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                    </a>`;
            });
        } else {
            attachmentsContainer.classList.add('hidden');
        }
        
        // Show modal
        document.body.classList.add('overflow-hidden');
        emailModal.classList.remove('hidden');
        closeModal.focus();
    }

    // Add this helper function for attachment icons
    function getAttachmentIcon(mimeType) {
        const type = mimeType.split('/')[0];
        const subtype = mimeType.split('/')[1];
        
        const icons = {
            image: `<svg class="h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                   </svg>`,
            application: `<svg class="h-5 w-5 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                         </svg>`,
            text: `<svg class="h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                   </svg>`,
            default: `<svg class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                     </svg>`
        };
        
        if (type === 'application') {
            if (subtype.includes('pdf')) return icons.application;
            if (subtype.includes('zip') || subtype.includes('compressed')) {
                return `<svg class="h-5 w-5 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>`;
            }
        }
        
        return icons[type] || icons.default;
    }

    // Initialize unread count
    updateUnreadCount();
});
</script>
@endsection