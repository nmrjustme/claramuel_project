<script>
        // Initialize guest dropdown
    function initializeGuestDropdown() {
        const guestDropdownToggle = document.getElementById('guestDropdownToggle');
        const guestDropdown = document.getElementById('guestDropdown');

        if (guestDropdownToggle && guestDropdown) {
            guestDropdownToggle.addEventListener('click', function (e) {
                e.preventDefault();
                guestDropdown.classList.toggle('hidden');

                const summarySection = document.getElementById('summarySection');
                if (!guestDropdown.classList.contains('hidden')) {
                    summarySection.style.marginTop = guestDropdown.offsetHeight + 'px';
                } else {
                    summarySection.style.marginTop = '0';
                }
            });

            document.addEventListener('click', function (e) {
                if (!guestDropdown.contains(e.target) && !guestDropdownToggle.contains(e.target)) {
                    guestDropdown.classList.add('hidden');
                    document.getElementById('summarySection').style.marginTop = '0';
                }
            });
        }
    }
</script>