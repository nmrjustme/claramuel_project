    <script>
        // Enhanced scroll functionality for each category
        document.querySelectorAll('.scroll-left, .scroll-right').forEach(button => {
            button.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const container = document.getElementById(targetId);
                const scrollAmount = container.clientWidth * 0.8; // Scroll 80% of container width
                
                if (this.classList.contains('scroll-left')) {
                    container.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
                } else {
                    container.scrollBy({ left: scrollAmount, behavior: 'smooth' });
                }
            });
        });

        // Enhanced scroll button visibility
        document.querySelectorAll('.rooms-scroll-container').forEach(container => {
            container.addEventListener('scroll', function() {
                const containerId = this.id;
                const leftButton = document.querySelector(`.scroll-left[data-target="${containerId}"]`);
                const rightButton = document.querySelector(`.scroll-right[data-target="${containerId}"]`);
                
                // Show/hide left button
                if (this.scrollLeft <= 10) {
                    leftButton.style.opacity = '0';
                    leftButton.style.pointerEvents = 'none';
                } else {
                    leftButton.style.opacity = '1';
                    leftButton.style.pointerEvents = 'auto';
                }
                
                // Show/hide right button
                if (this.scrollLeft + this.clientWidth >= this.scrollWidth - 10) {
                    rightButton.style.opacity = '0';
                    rightButton.style.pointerEvents = 'none';
                } else {
                    rightButton.style.opacity = '1';
                    rightButton.style.pointerEvents = 'auto';
                }
            });
            
            // Trigger initial scroll event
            const event = new Event('scroll');
            container.dispatchEvent(event);
        });

        // Netflix-style hover effect for room cards
        document.querySelectorAll('.room-card').forEach(card => {
            card.addEventListener('mouseenter', () => {
                // Ensure the card is fully visible when hovered
                const container = card.closest('.rooms-scroll-container');
                const cardRect = card.getBoundingClientRect();
                const containerRect = container.getBoundingClientRect();
                
                if (cardRect.right > containerRect.right - 50) {
                    // Card is partially off-screen to the right
                    container.scrollBy({
                        left: cardRect.right - containerRect.right + 50,
                        behavior: 'smooth'
                    });
                } else if (cardRect.left < containerRect.left + 50) {
                    // Card is partially off-screen to the left
                    container.scrollBy({
                        left: cardRect.left - containerRect.left - 50,
                        behavior: 'smooth'
                    });
                }
            });
        });
    </script>