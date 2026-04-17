    <script>
        // Phone number formatting and validation functions
        function formatPhone(input) {
            // Remove all non-digit characters
            let value = input.value.replace(/\D/g, '');
            
            // Limit to 10 digits and ensure it starts with 9
            if (value.length > 10) {
                value = value.substring(0, 10);
            }
            if (value.length > 0 && value[0] !== '9') {
                value = '9' + value.substring(0, 9); // Force first digit to be 9
            }
            
            // Apply formatting
            let formatted = value;
            if (value.length > 4) {
                formatted = value.substring(0, 4) + ' ' + value.substring(4, 7);
            }
            if (value.length > 7) {
                formatted = value.substring(0, 4) + ' ' + value.substring(4, 7) + ' ' + value.substring(7, 10);
            }
            
            input.value = formatted;
            validatePhone(input); // Validate as user types
        }
    
        function validatePhone(input) {
            const errorElement = document.getElementById('phone-error');
            const digits = input.value.replace(/\D/g, '');
            const formattedValue = input.value.trim();
            
            // Regular expression to check the exact format: 9999 999 999
            const phoneRegex = /^9\d{3} \d{3} \d{3}$/;
            
            if (digits.length === 0) {
                errorElement.textContent = 'Phone number is required';
                errorElement.style.display = 'block';
                input.classList.add('input-error');
                input.classList.remove('input-success');
                return false;
            } 
            else if (digits[0] !== '9') {
                errorElement.textContent = 'Phone number must start with 9';
                errorElement.style.display = 'block';
                input.classList.add('input-error');
                input.classList.remove('input-success');
                return false;
            }
            else if (!phoneRegex.test(formattedValue)) {
                errorElement.textContent = 'Phone number must be 10 digits (format: 9999 999 999)';
                errorElement.style.display = 'block';
                input.classList.add('input-error');
                input.classList.remove('input-success');
                return false;
            }
            else {
                errorElement.style.display = 'none';
                input.classList.remove('input-error');
                input.classList.add('input-success');
                return true;
            }
        }

        // Show notification function
        function showNotification(message, isError = false) {
            const notification = document.getElementById('notification');
            const messageElement = document.getElementById('notification-message');
            
            messageElement.textContent = message;
            notification.className = 'notification';
            
            if (isError) {
                notification.classList.add('error');
            }
            
            notification.classList.add('show');
            
            setTimeout(() => {
                notification.classList.remove('show');
            }, 3000);
        }
    
        document.addEventListener('DOMContentLoaded', () => {
            window.bookingSystem = new BookingSystem();
        });
    </script>