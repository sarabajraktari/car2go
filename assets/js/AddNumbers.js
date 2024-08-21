document.addEventListener('DOMContentLoaded', function () {
    const numbers = document.querySelectorAll('.number-title');
    const duration = 2000;

    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const target = entry.target;
                const endValue = parseInt(target.getAttribute('data-target'), 10);
                const index = parseInt(target.getAttribute('data-index'), 10);
                animateCounter(target, endValue, duration, index);
                observer.unobserve(target); 
            }
        });
    });

    numbers.forEach(number => {
        observer.observe(number);
    });

    function animateCounter(element, endValue, duration, index) {
        let startValue = 0;
        const increment = endValue / (duration / 20); 

        const timer = setInterval(() => {
            startValue += increment;
            if (startValue >= endValue) {
                clearInterval(timer);
                if (index === 1) {
                    element.innerHTML = `<span class="number-value">${endValue}K</span><span class="plus-sign">+</span>`; 
                } else {
                    element.innerHTML = `<span class="number-value">${endValue}</span><span class="plus-sign">+</span>`; 
                }
            } else {
                if (index === 1) {
                    element.innerHTML = `<span class="number-value">${Math.floor(startValue)}K</span><span class="plus-sign">+</span>`; 
                } else {
                    element.innerHTML = `<span class="number-value">${Math.floor(startValue)}</span><span class="plus-sign">+</span>`; 
                }
            }
        }, 20);
    }
});
