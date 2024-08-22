document.addEventListener('DOMContentLoaded', () => {
    const scrollToTopButton = document.getElementById('scroll-to-top');

    if (scrollToTopButton) {
        // Show button when user scrolls down
        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 100) {
                scrollToTopButton.style.display = 'block';
            } else {
                scrollToTopButton.style.display = 'none';
            }
        });

        // Define scrollToTop function in the global scope
        window.scrollToTop = function () {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        };
    } else {
        console.error('Scroll-to-top button not found.');
    }
});
