(function ($) {
    "use strict";

    setInterval(function() {

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
