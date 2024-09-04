$(document).ready(function(){
    $('.owl-carousel').owlCarousel({
        loop: true,
        margin: 10,
        autoplay: true,
        autoplayTimeout: 2000,
        autoplayHoverPause: true,
        responsive: {
            320: {
                items: 1
            },
            770: {
                items: 2
            },
            1180: {
                items: 3
            }
        }
    });
    
    
});