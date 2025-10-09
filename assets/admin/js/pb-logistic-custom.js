(function ($) {
    "use strict";

    setInterval(function() {

        $('.testimonial_slide').each(function() {
            if (!$(this).hasClass('applied')) {
                $(this).slick({
                  dots: true,
                  arrows: false,
                  infinite: true,
                  speed: 300,
                  autoplay: false,
                  slidesToShow: 2,
                  slidesToScroll: 1,
                  responsive: [
                    {
                      breakpoint: 992,
                      settings: {
                        slidesToShow: 2,
                        slidesToScroll: 2,
                      }
                    },
                    {
                      breakpoint: 780,
                      settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1
                      }
                    },
                    {
                      breakpoint: 480,
                      settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1
                      }
                    }
                  ]
                });
                $(this).addClass('applied');
            }
        });


        $('.counter').each(function() {
            if (!$(this).hasClass('applied')) {            
              $(this).counterUp({
                  delay: 50,
                  time: 2000
              });
              $(this).addClass('applied');
            }
        });


        // accordion collapse on button click
        $(".accordion .card-header button").on('click', function() {
            $(this).parents('.card-header').next().collapse("toggle");
        });


    }, 5000);

}(jQuery));