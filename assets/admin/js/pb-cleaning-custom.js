(function ($) {
    "use strict";

    setInterval(function() {


        //===== testimonial slick slider
        $('.testimonial-active').each(function() {
            if (!$(this).hasClass('applied')) {
                $(this).slick({
                    dots: true,
                    infinite: true,
                    autoplay: false,
                    autoplaySpeed: 3000,
                    arrows: false,
                    prevArrow: '<span class="prev"><i class="fas fa-arrow-left"></i></span>',
                    nextArrow: '<span class="next"><i class="fas fa-arrow-right"></i></span>',
                    speed: 1500,
                    slidesToShow: 2,
                    slidesToScroll: 1,
                    responsive: [
                        {
                            breakpoint: 1201,
                            settings: {
                                slidesToShow: 2,
                            }
                        },
                        {
                            breakpoint: 992,
                            settings: {
                                slidesToShow: 1,
                            }
                        }
                    ]
                });
                $(this).addClass('applied');
            }
        });


        //===== counter up
        $('.count').each(function() {
            if (!$(this).hasClass('applied')) {
                $(this).counterUp({
                    delay: 10,
                    time: 2000
                });
                $(this).addClass('applied');
            }
        });

    }, 5000);


}(jQuery));
