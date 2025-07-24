<script>
    // Global variable to track new inquiries
let newInquiriesCount = 0;

// Add CSS animations for notifications
const notificationStyles = document.createElement('style');
notificationStyles.textContent = `
    @keyframes pop-in {
        0% { transform: scale(0.5); opacity: 0; }
        100% { transform: scale(1); opacity: 1; }
    }
    @keyframes fade-in-up {
        0% { transform: translateY(20px); opacity: 0; }
        100% { transform: translateY(0); opacity: 1; }
    }
    @keyframes fade-out {
        0% { opacity: 1; }
        100% { opacity: 0; }
    }
    .animate-pop-in { animation: pop-in 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards; }
    .animate-fade-in-up { animation: fade-in-up 0.3s ease-out forwards; }
    .animate-fade-out { animation: fade-out 0.3s ease-out forwards; }
    
    /* Enhanced notification styles */
    .enhanced-notification {
        position: fixed;
        inset: 0;
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: rgba(0,0,0,0.5);
    }
    .enhanced-notification-content {
        background: white;
        border-radius: 0.5rem;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        width: 100%;
        max-width: 28rem;
        overflow: hidden;
    }
    .enhanced-notification-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #ef4444;
        padding: 0.75rem 1rem;
        color: white;
    }
    .enhanced-notification-body {
        padding: 1.5rem;
    }
    .enhanced-notification-footer {
        display: flex;
        justify-content: flex-end;
        padding: 0 1.5rem 1.5rem;
    }
    .notification-badge {
        position: absolute;
        top: -0.5rem;
        right: -0.5rem;
        background: #ef4444;
        color: white;
        border-radius: 9999px;
        width: 1.5rem;
        height: 1.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: bold;
    }
`;
document.head.appendChild(notificationStyles);

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

function openInquiriesPageAndSearch(inquiryId) {
    // Store the ID to search for in sessionStorage
    sessionStorage.setItem('searchInquiryId', inquiryId);
    
    // Open the inquiries page
    window.location.href = '/Inquiries'; // Update this URL to your actual inquiries page route
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
             // Optionally show error to user
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
                    onclick="openInquiriesPageAndSearch('${inquirer.id}')" 
                    class="px-3 py-1 text-sm text-gray-700 hover:text-white hover:bg-red-600 border border-gray-300 rounded-md transition-colors"
                >
                    View
                </button>

            </div>
        `;
    });

    container.innerHTML = html;
}

// Function to update the new inquiries count with enhanced notifications
function updateNewInquiriesCount(count) {
    newInquiriesCount = count;
    const counterElement = document.querySelector('.new-inquiries-count');
    
    if (count > 0) {
        counterElement.textContent = `${count} new`;
        counterElement.classList.remove('hidden');
        
        // Show notification if count increased
        if (count > parseInt(counterElement.dataset.prevCount || 0)) {
            // For 5+ new inquiries, show the full modal notification
            if (count >= 5) {
                showFullPageNotification(`You have ${count} new inquiries waiting for your response!`);
            } 
            // For 1-4 new inquiries, show a browser-style notification
            else {
                showBrowserStyleNotification(`You have ${count} new inquiry${count > 1 ? 's' : ''}`);
            }
            
            // Flash the tab title to get attention
            flashTabTitle(`(${count}) New Inquiries`);
            
            // Play notification sound
            playNotificationSound();
        }
    } else {
        counterElement.classList.add('hidden');
        // Restore tab title if no new inquiries
        restoreTabTitle();
    }
    
    counterElement.dataset.prevCount = count;
}

// Enhanced notification functions
function showFullPageNotification(message) {
    // Remove any existing notifications first
    const existingNotif = document.querySelector('.enhanced-notification');
    if (existingNotif) existingNotif.remove();

    const notification = document.createElement('div');
    notification.className = 'enhanced-notification';
    notification.innerHTML = `
        <div class="enhanced-notification-content animate-pop-in">
            <div class="enhanced-notification-header">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <h3 class="font-bold">New Inquiries Alert</h3>
                </div>
                <button onclick="this.closest('.enhanced-notification').remove()" class="text-white hover:text-gray-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
            <div class="enhanced-notification-body">
                <p class="text-gray-700">${message}</p>
            </div>
            <div class="enhanced-notification-footer">
                <button onclick="this.closest('.enhanced-notification').remove()" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                    View Inquiries
                </button>
            </div>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Auto-remove after 15 seconds if not dismissed
    setTimeout(() => {
        if (notification.parentNode) {
            notification.querySelector('.enhanced-notification-content').classList.add('animate-fade-out');
            setTimeout(() => notification.remove(), 300);
        }
    }, 15000);
}

function showBrowserStyleNotification(message) {
    // Remove any existing notifications first
    const existingNotif = document.querySelector('.browser-notification');
    if (existingNotif) existingNotif.remove();

    const notification = document.createElement('div');
    notification.className = 'browser-notification animate-fade-in-up';
    notification.innerHTML = `
        <div class="flex items-start">
            <div class="relative">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <span class="notification-badge">${newInquiriesCount}</span>
            </div>
            <div class="ml-3 flex-1">
                <h4 class="text-sm font-medium text-gray-900">New Inquiries</h4>
                <p class="text-sm text-gray-500 mt-1">${message}</p>
                <div class="mt-2 flex space-x-3">
                    <button onclick="this.closest('.browser-notification').remove()" class="text-xs text-gray-500 hover:text-gray-700">
                        Dismiss
                    </button>
                    <button onclick="window.location.href='#inquiries'; this.closest('.browser-notification').remove()" class="text-xs text-red-600 hover:text-red-800 font-medium">
                        View Now
                    </button>
                </div>
            </div>
            <button onclick="this.closest('.browser-notification').remove()" class="text-gray-400 hover:text-gray-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Auto-remove after 10 seconds if not dismissed
    setTimeout(() => {
        if (notification.parentNode) {
            notification.classList.add('animate-fade-out');
            setTimeout(() => notification.remove(), 300);
        }
    }, 10000);
}

function playNotificationSound() {
    try {
        const audio = new Audio('https://assets.mixkit.co/sfx/preview/mixkit-alarm-digital-clock-beep-989.mp3');
        audio.volume = 0.3;
        audio.play().catch(e => console.log('Audio playback prevented:', e));
    } catch (e) {
        console.log('Error playing notification sound:', e);
    }
}

let tabTitleInterval;
let originalTitle = document.title;

function flashTabTitle(message) {
    clearInterval(tabTitleInterval);
    originalTitle = document.title.replace(/^\(\d+\)\s*/, '');
    
    let count = 0;
    tabTitleInterval = setInterval(() => {
        document.title = (count++ % 2) ? `${message} - ${originalTitle}` : originalTitle;
    }, 1000);
    
    // Stop after 15 seconds
    setTimeout(() => {
        clearInterval(tabTitleInterval);
        document.title = originalTitle;
    }, 15000);
}

function restoreTabTitle() {
    clearInterval(tabTitleInterval);
    document.title = originalTitle;
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
            
            showBrowserStyleNotification('All inquiries marked as read');
            restoreTabTitle();
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

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    loadInquiries();
    
    // Optionally poll for new inquiries every 30 seconds
    setInterval(() => {
        const searchInput = document.getElementById('inquiry-search');
        loadInquiries(searchInput.value);
    }, 30000);
});
</script>