document.addEventListener("DOMContentLoaded", function () {
    const loadButton = document.querySelector('.load-all-cards');
    const loadButtonContainer = document.querySelector('.load-more-button-container');
    const items = document.querySelectorAll('#cards-grid .card-item');
    const increment = 3; 

    let hiddenItems = Array.from(items).filter(item => item.classList.contains('hidden'));

    if (hiddenItems.length === 0) {
        loadButton.classList.add('hidden');
        loadButtonContainer.classList.add('hidden');
    }

    if (loadButton) {
        loadButton.addEventListener('click', function (event) {
            if (event.target.closest('.load-all-cards')) {
                hiddenItems.slice(0, increment).forEach(item => {
                    item.classList.remove('hidden');
                    item.classList.add('transition', 'transform', 'duration-300', 'ease-in-out', 'opacity-0', 'translate-y-4');
                    
                    requestAnimationFrame(() => {
                        item.classList.remove('opacity-0', 'translate-y-4');
                        item.classList.add('opacity-100', 'translate-y-0');
                    });
                });

                hiddenItems = Array.from(items).filter(item => item.classList.contains('hidden'));

                if (hiddenItems.length === 0) {
                    loadButton.classList.add('hidden');
                    loadButtonContainer.classList.add('hidden');
                }
            }
        });
    }
});