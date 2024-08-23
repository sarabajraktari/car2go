document.addEventListener('DOMContentLoaded', () => {
    const lightbox = document.getElementById('lightbox');
    const lightboxImage = document.getElementById('lightbox-image');
    const lightboxClose = document.getElementById('lightbox-close');
    const lightboxPrev = document.getElementById('lightbox-prev');
    const lightboxNext = document.getElementById('lightbox-next');
    const galleryItems = document.querySelectorAll('.open-lightbox');
    const galleryData = document.getElementById('gallery-data');

    // Check if the gallery elements exist before proceeding
    if (lightbox && lightboxImage && lightboxClose && lightboxPrev && lightboxNext && galleryData) {
        const images = JSON.parse(galleryData.textContent);
        let currentIndex = 0;

        function openLightbox(index) {
            currentIndex = index;
            lightboxImage.src = images[index].images.url;
            lightboxImage.alt = images[index].images.alt;
            lightbox.classList.remove('hidden');
            updateNavigationButtons();
        }

        function closeLightbox() {
            lightbox.classList.add('hidden');
        }

        function updateImage(index) {
            if (index < 0) {
                index = images.length - 1; // Wrap around to the last image
            } else if (index >= images.length) {
                index = 0; // Wrap around to the first image
            }
            currentIndex = index;
            lightboxImage.src = images[index].images.url;
            lightboxImage.alt = images[index].images.alt;
            updateNavigationButtons();
        }

        function updateNavigationButtons() {
            const shouldDisable = images.length <= 1;
            lightboxPrev.disabled = shouldDisable;
            lightboxNext.disabled = shouldDisable;
        }

        galleryItems.forEach((item, index) => {
            item.addEventListener('click', (event) => {
                event.preventDefault();
                openLightbox(index);
            });
        });

        lightboxClose.addEventListener('click', closeLightbox);
        lightboxPrev.addEventListener('click', () => {
            updateImage(currentIndex - 1); // Go to the previous image
        });
        lightboxNext.addEventListener('click', () => {
            updateImage(currentIndex + 1); // Go to the next image
        });

        lightbox.addEventListener('click', (event) => {
            if (event.target === lightbox) {
                closeLightbox();
            }
        });

        // Scroll-triggered animation
        const galleryItemsForAnimation = document.querySelectorAll('.gallery-item');

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target); // Stop observing once the item is visible
                }
            });
        }, {
            threshold: 0.1 // Trigger when 10% of the item is visible
        });

        galleryItemsForAnimation.forEach(item => {
            observer.observe(item);
        });
    }
});
