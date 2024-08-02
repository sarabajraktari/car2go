document.addEventListener('DOMContentLoaded', function () {
    var splide = new Splide('.splide', {
        type   : 'loop',
        perPage: 5,
        perMove: 1,
        autoplay: true,
        breakpoints: {
            1024: {
                perPage: 3,
            },
            640: {
                perPage: 1,
            },
        },
    });
    splide.mount();
});
