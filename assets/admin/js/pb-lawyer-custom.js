(function ($) {
    "use strict";

    setInterval(function () {


        $('.counter').each(function () {
            if (!$(this).hasClass('applied')) {
                $(this).counterUp({
                    delay: 50,
                    time: 2000
                });
                $(this).addClass('applied');
            }
        });

    }, 5000);

}(jQuery));