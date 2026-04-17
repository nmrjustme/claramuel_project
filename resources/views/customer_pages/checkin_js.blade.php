<script>
    // Initialize all functionality when DOM is loaded
    document.addEventListener('DOMContentLoaded', function () {
        initializeFlatpickr();
        initializeGuestDropdown();
        updateSummary();
    });

    function initializeFlatpickr() {
        const checkinCalendar = flatpickr("#checkin", {
            dateFormat: "Y-m-d",
            minDate: "today",
            onChange: function (selectedDates, dateStr) {
                if (selectedDates.length > 0 && document.getElementById('checkout')) {
                    const nextDay = new Date(selectedDates[0]);
                    nextDay.setDate(nextDay.getDate() + 1);
                    checkoutCalendar.set('minDate', nextDay);
                }
                updateSummary();
            }
        });
    
        let checkoutCalendar;
        if (document.getElementById('checkout')) {
            checkoutCalendar = flatpickr("#checkout", {
                dateFormat: "Y-m-d",
                minDate: "today",
                onChange: function (selectedDates, dateStr) {
                    if (selectedDates.length > 0) {
                        const checkinDate = new Date(document.getElementById('checkin').value);
                        const checkoutDate = new Date(dateStr);
                        
                        // Ensure checkout is after checkin
                        if (checkoutDate <= checkinDate) {
                            checkoutCalendar.setDate(new Date(checkinDate.getTime() + 86400000)); // Add 1 day
                        }
                    }
                    updateSummary();
                }
            });
        }
    }

    // Initialize guest dropdown
    function initializeGuestDropdown() {
        const guestDropdownToggle = document.getElementById('guestDropdownToggle');
        const guestDropdown = document.getElementById('guestDropdown');

        if (guestDropdownToggle && guestDropdown) {
            guestDropdownToggle.addEventListener('click', function (e) {
                e.preventDefault();
                guestDropdown.classList.toggle('hidden');
            });

            document.addEventListener('click', function (e) {
                if (!guestDropdown.contains(e.target) && !guestDropdownToggle.contains(e.target)) {
                    guestDropdown.classList.add('hidden');
                }
            });
        }
    }

    // Update guest counts
    function updateGuest(type, delta) {
        const countSpan = document.getElementById(type + 'Count');
        const input = document.getElementById(type + 'Input');
        let value = parseInt(input.value, 10) + delta;
        if (value < 0) value = 0;
        input.value = value;
        countSpan.textContent = value;
        updateSummary();
    }
    
    // Update cottage quantities (only for Pool)
    function updateCottageQuantity(type, delta) {
        const countSpan = document.getElementById(type + '_count');
        const input = document.getElementById(type + '_input');
        let value = parseInt(input.value, 10) + delta;
        if (value < 0) value = 0;
        input.value = value;
        countSpan.textContent = value;
        updateSelectedCottages();
    }

    // Update selected cottages (only for Pool)
    function updateSelectedCottages() {
        const cottageTypes = ['small_cottage', 'medium_cottage', 'large_cottage'];
        let totalCottages = 0;
        let listHTML = '';
        
        cottageTypes.forEach(type => {
            const input = document.getElementById(type + '_input');
            if (input) {
                const count = parseInt(input.value, 10);
                if (count > 0) {
                    totalCottages += count;
                    const price = parseInt(input.dataset.price, 10);
                    const cottageName = type.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
                    listHTML += `<div class="flex justify-between py-1 border-b border-gray-200 dark:text-gray-800">
                                    <span>${count} x ${cottageName}</span>
                                    <span>₱${(count * price).toLocaleString()}</span>
                                 </div>`;
                }
            }
        });

        if (document.getElementById('selectedCottageCount')) {
            document.getElementById('selectedCottageCount').textContent = totalCottages;
        }
        if (document.getElementById('selectedCottagesList')) {
            document.getElementById('selectedCottagesList').innerHTML = totalCottages > 0 ? listHTML : 'No cottages selected';
        }
        updateSummary();
    }

    // Calculate and update the summary
    function updateSummary() {
        // Calculate base price (for Room/Village)
        let basePrice = 0;
        if (document.getElementById('basePrice')) {
            basePrice = parseFloat(document.getElementById('basePrice').value) || 0;
        }

        // Calculate guest costs
        let totalGuests = 0;
        let guestCosts = 0;
        document.querySelectorAll('input[type="hidden"][id$="Input"]').forEach(input => {
            const count = parseInt(input.value, 10) || 0;
            const price = parseFloat(input.dataset?.price) || 0;
            totalGuests += count;
            guestCosts += count * price;
        });

        // Calculate cottage costs (only for Pool)
        let cottageCosts = 0;
        @if ($category == 'Pool')
            const cottageTypes = ['small_cottage', 'medium_cottage', 'large_cottage'];
            cottageTypes.forEach(type => {
                const input = document.getElementById(type + '_input');
                if (input) {
                    const count = parseInt(input.value, 10) || 0;
                    const price = parseFloat(input.dataset?.price) || 0;
                    cottageCosts += count * price;
                }
            });
        @endif

        // Calculate number of nights
        let numNights = 1;
        const checkin = document.getElementById('checkin')?.value;
        const checkout = document.getElementById('checkout')?.value;
        
        if (checkin && checkout) {
            const inDate = new Date(checkin);
            const outDate = new Date(checkout);
            const diffTime = outDate - inDate;
            numNights = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            numNights = numNights > 0 ? numNights : 1; // Ensure at least 1 night
        }

        // Calculate total price
        let totalPrice = 0;
        
        @if ($category == 'Pool' || $category == 'Park')
            // For Pool/Park: (Guest Costs + Cottage Costs) × 1 (no nightly rate)
            totalPrice = guestCosts + cottageCosts;
        @elseif ($category == 'Room' || $category == 'Village')
            // For Room/Village: (Base Price + Guest Costs) × Number of Nights
            totalPrice = (basePrice + guestCosts) * numNights;
        @endif

        // Update summary display
        if (document.getElementById('totalGuests')) {
            document.getElementById('totalGuests').textContent = totalGuests;
        }
        
        if (document.getElementById('totalNights')) {
            document.getElementById('totalNights').textContent = numNights;
        }
        
        if (document.getElementById('totalPrice')) {
            document.getElementById('totalPrice').textContent = '₱' + totalPrice.toLocaleString();
        }
    }

    // Show confirmation modal
    function showConfirmationModal() {
        // Get selected cottages (only for Pool)
        @if ($category == 'Pool')
            const cottageTypes = ['small_cottage', 'medium_cottage', 'large_cottage'];
            let cottagesHTML = '';
            
            cottageTypes.forEach(type => {
                const input = document.getElementById(type + '_input');
                if (input) {
                    const count = parseInt(input.value, 10);
                    if (count > 0) {
                        const price = parseInt(input.dataset.price, 10);
                        const cottageName = type.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
                        cottagesHTML += `<div class="flex justify-between">
                                            <span>${count} x ${cottageName}</span>
                                            <span>₱${(count * price).toLocaleString()}</span>
                                        </div>`;
                    }
                }
            });

            document.getElementById('modalCottagesList').innerHTML = cottagesHTML || 'No cottages selected';
        @endif

        // Get guest counts
        const guestInputs = document.querySelectorAll('input[type="hidden"][id$="Input"]');
        let guestsHTML = '';
        guestInputs.forEach(input => {
            const count = input.value;
            if (count > 0) {
                const type = input.id.replace('Input', '').replace(/_/g, ' ');
                const price = input.dataset.price;
                guestsHTML += `<div class="flex justify-between">
                                    <span>${type} (${count})</span>
                                    <span>₱${(count * price).toLocaleString()}</span>
                                </div>`;
            }
        });
        
        document.getElementById('modalGuestsList').innerHTML = guestsHTML || 'No guests selected';

        // Get dates
        const checkinDate = document.getElementById('checkin').value;
        let checkoutDate = '';
        if (document.getElementById('checkout')) {
            checkoutDate = document.getElementById('checkout').value;
        }
        let datesHTML = `<div>Check-in: ${checkinDate}</div>`;
        if (checkoutDate) {
            datesHTML += `<div>Check-out: ${checkoutDate}</div>`;
        }
        document.getElementById('modalDates').innerHTML = datesHTML;

        // Get nights
        const nights = document.getElementById('totalNights').textContent;
        document.getElementById('modalNights').textContent = nights;

        // Get total price
        const totalPrice = document.getElementById('totalPrice').textContent;
        document.getElementById('modalTotalPrice').textContent = totalPrice;

        // Show modal
        document.getElementById('confirmationModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('confirmationModal').classList.add('hidden');
    }

    function submitForm() {
        document.getElementById('reservationForm').submit();
    }

    // Image viewer functions
    function showFullscreenImage(src) {
        document.getElementById('fullscreenImage').src = src;
        document.getElementById('fullscreenOverlay').classList.remove('hidden');
    }

    function closeFullscreenImage() {
        document.getElementById('fullscreenOverlay').classList.add('hidden');
    }
</script>