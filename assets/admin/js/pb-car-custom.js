(function ($) {
    "use strict";

    setInterval(function() {
        $('.project_slick').each(function() {
            if (!$(this).hasClass('applied')) {                
                $(this).slick({
                    dots: false,
                    arrows: true,
                    infinite: true,
                    speed: 300,
                    centerMode: true,
                    variableWidth: true,
                    slidesToShow: 1,
                    slidesToScroll: 1
                });      
                $(this).addClass('applied');  
            }
        });

        $('.testimonial_slide').each(function() {
            if (!$(this).hasClass('applied')) {                
                $(this).slick({
                    dots: false,
                    arrows: true,
                    infinite: true,
                    speed: 300,
                    autoplay: false,
                    slidesToShow: 2,
                    slidesToScroll: 1,
                    responsive: [
                      {
                        breakpoint: 1100,
                        settings: {
                          slidesToShow: 1
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
