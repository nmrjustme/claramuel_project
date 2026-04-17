<script>
    // Initialize all functionality when DOM is loaded
    document.addEventListener('DOMContentLoaded', function () {
        initializeFlatpickr();
        initializeGuestDropdown();
        initializeSummary();
    });

    // Initialize date pickers
    function initializeFlatpickr() {
        const checkinCalendar = flatpickr("#checkin", {
            dateFormat: "Y-m-d",
            minDate: "today",
            onChange: function (selectedDates, dateStr) {
                if (document.getElementById('checkout')) {
                    checkoutCalendar.set('minDate', dateStr);
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
                    checkinCalendar.set('maxDate', dateStr);
                    updateSummary();
                }
            });
        }
    }
</script>