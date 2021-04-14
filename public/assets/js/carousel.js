jQuery(function($) {
    $('.owl-carousel').owlCarousel({
        loop: false,
        margin: 10,
        touchDrag: true,
        mouseDrag: true,
        dots: true,
        responsive: {
            0: {
                items: 1,
                dots: true
            },
            600: {
                items: 3,
                dots: true
            },
            1000: {
                items: 5,
                dots: true
            }
        }
    });
});