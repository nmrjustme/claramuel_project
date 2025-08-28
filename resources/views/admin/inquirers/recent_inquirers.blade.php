<script>
    // Global variable to track new inquiries
let newInquiriesCount = 0;

// Function to mark a single inquiry as read
function markInquiryAsRead(inquiryId) {
    return fetch(`/api/inquiries/mark-read/${inquiryId}`, {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update the specific inquiry item in the UI
            const inquiryItem = document.querySelector(`.inquiry-item[data-id="${inquiryId}"]`);
            if (inquiryItem) {
                inquiryItem.classList.remove('border-l-4', 'border-red-500', 'bg-red-50');
                const unreadDot = inquiryItem.querySelector('.inline-block.h-2.w-2.rounded-full.bg-red-500');
                if (unreadDot) unreadDot.remove();
            }
            
            // Update the counter
            updateNewInquiriesCount(data.newCount);
            return data;
        }
        throw new Error(data.message || 'Failed to mark as read');
    });
}

function openInquiriesPageAndSearch(code) {
    // Store the ID to search for in sessionStorage
    sessionStorage.setItem('searchInquiryId', code);
    
    // Open the inquiries page
    window.location.href = '/Inquiries';
}

// Function to handle view button clicks
window.handleViewInquiry = function(button) {
    const id = button.getAttribute('data-id');
    
    // Mark as read before opening modal
    markInquiryAsRead(id)
            .then(() => {
                // Call the modal's open function after marking as read
                if (typeof window.openModal_accept_inquirer === 'function') {
                    window.openModal_accept_inquirer(button);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
    };

// Function to load inquiries via AJAX
function loadInquiries(search = '') {
    showLoadingState('inquiries-container', 'Loading inquiries...');
    
    fetch(`/api/inquiries?search=${encodeURIComponent(search)}`, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            renderInquiries(data.inquiries);
            updateNewInquiriesCount(data.newCount);
        } else {
            showErrorState('inquiries-container', { message: 'Failed to load inquiries' });
        }
    })
    .catch(error => {
        console.error('Error loading inquiries:', error);
        showErrorState('inquiries-container', error);
    });
}

// Function to render inquiries
function renderInquiries(inquiries) {
    const container = document.getElementById('inquiries-container');
    
    if (inquiries.length === 0) {
        container.innerHTML = `
            <div class="p-4 text-center text-gray-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                </svg>
                <p class="mt-2">No inquiries found</p>
            </div>
        `;
        return;
    }

    let html = '';
    inquiries.forEach(inquirer => {
        html += `
            <div class="inquiry-item flex items-center p-3 hover:bg-gray-50 rounded-lg transition-colors group ${!inquirer.is_read ? 'border-l-4 border-red-500 bg-red-50' : ''}" data-id="${inquirer.id}">
                <div class="ml-3 flex-1 min-w-0">
                    <div class="flex justify-between items-baseline">
                        <h4 class="text-sm font-medium text-gray-900 truncate">
                            ${inquirer.user.firstname} ${inquirer.user.lastname}
                            ${!inquirer.is_read ? '<span class="ml-1 inline-block h-2 w-2 rounded-full bg-red-500"></span>' : ''}
                        </h4>
                        <span class="text-xs text-gray-500 ml-2 whitespace-nowrap">
                            ${new Date(inquirer.created_at).toLocaleString()}
                        </span>
                    </div>
                    <p class="text-sm text-gray-500 truncate">${inquirer.user.email || 'No email provided'}</p>
                    <p class="text-sm text-gray-500 truncate">${inquirer.user.phone}</p>
                </div>
                <button 
                    onclick="openInquiriesPageAndSearch('${inquirer.code}')" 
                    class="px-3 py-1 text-sm text-gray-700 hover:text-white hover:bg-red-600 border border-gray-300 rounded-md transition-colors"
                >
                    View
                </button>
            </div>
        `;
    });

    container.innerHTML = html;
}

// Function to update the new inquiries count
function updateNewInquiriesCount(count) {
    newInquiriesCount = count;
    const counterElement = document.querySelector('.new-inquiries-count');
    
    if (count > 0) {
        counterElement.textContent = `${count} new`;
        counterElement.classList.remove('hidden');
    } else {
        counterElement.classList.add('hidden');
    }
    
    counterElement.dataset.prevCount = count;
}

// Function to mark all inquiries as read
function markAllAsRead() {
    fetch('/api/inquiries/mark-all-read', {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateNewInquiriesCount(data.newCount);
            
            // Update all inquiry items in the UI
            document.querySelectorAll('.inquiry-item').forEach(item => {
                item.classList.remove('border-l-4', 'border-red-500', 'bg-red-50');
                const unreadDot = item.querySelector('.inline-block.h-2.w-2.rounded-full.bg-red-500');
                if (unreadDot) {
                    unreadDot.remove();
                }
            });
        }
    })
    .catch(error => {
        console.error('Error marking all inquiries as read:', error);
    });
}

// Function to filter inquiries based on search
function filterInquiries() {
    const searchInput = document.getElementById('inquiry-search');
    loadInquiries(searchInput.value);
}

// Helper functions for UI states
function showLoadingState(containerId, message) {
    const container = document.getElementById(containerId);
    if (container) {
        container.innerHTML = `
            <div class="text-center py-8">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-red-600 mx-auto"></div>
                <p class="mt-2 text-gray-500">${message}</p>
            </div>
        `;
    }
}

function showErrorState(containerId, error) {
    const container = document.getElementById(containerId);
    if (container) {
        container.innerHTML = `
            <div class="p-4 text-center text-red-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="mt-2">${error.message || 'Failed to load data. Please try again.'}</p>
                <button onclick="loadInquiries()" class="mt-4 px-4 py-2 bg-red-100 text-red-700 rounded hover:bg-red-200">
                    Retry
                </button>
            </div>
        `;
    }
}

// Function to add a new inquiry with animation
function addNewInquiry(booking) {
    const container = document.getElementById('inquiries-container');
    
    // Create the new inquiry element
    const inquiryElement = document.createElement('div');
    inquiryElement.className = 'inquiry-item flex items-center p-3 hover:bg-gray-50 rounded-lg transition-colors group border-l-4 border-red-500 bg-red-50 animate-pulse';
    inquiryElement.dataset.id = booking.id;
    inquiryElement.innerHTML = `
        <div class="ml-3 flex-1 min-w-0">
            <div class="flex justify-between items-baseline">
                <h4 class="text-sm font-medium text-gray-900 truncate">
                    ${booking.user.firstname} ${booking.user.lastname}
                    <span class="ml-1 inline-block h-2 w-2 rounded-full bg-red-500"></span>
                </h4>
                <span class="text-xs text-gray-500 ml-2 whitespace-nowrap">
                    ${new Date(booking.created_at).toLocaleString()}
                </span>
            </div>
            <p class="text-sm text-gray-500 truncate">${booking.user.email || 'No email provided'}</p>
            <p class="text-sm text-gray-500 truncate">${booking.user.phone}</p>
        </div>
        <button 
            onclick="openInquiriesPageAndSearch('${booking.code}')" 
            class="px-3 py-1 text-sm text-gray-700 hover:text-white hover:bg-red-600 border border-gray-300 rounded-md transition-colors"
        >
            View
        </button>
    `;
    
    // Remove animation after 2 seconds
    setTimeout(() => {
        inquiryElement.classList.remove('animate-pulse');
    }, 2000);
    
    // Insert at the top of the container
    if (container.firstChild) {
        container.insertBefore(inquiryElement, container.firstChild);
    } else {
        container.appendChild(inquiryElement);
    }
    
    // Update the counter
    updateNewInquiriesCount(newInquiriesCount + 1);
}

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    loadInquiries();
    
    // Set up Echo channel for real-time updates
    if (typeof Echo !== 'undefined') {
        window.Echo.channel('bookings')
            .listen('.booking.created', (e) => {
                console.log('New inquiry received:', e);
                addNewInquiry(e.booking);
            });
    }
});
</script>