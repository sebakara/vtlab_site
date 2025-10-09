(function ($) {
    "use strict";

    setInterval(function() {

        // statistics jquery circle progressbar initialization           
        $('.round').each(function () {
            if (!$(this).hasClass('applied')) {                
                $(this).circleProgress({
                    animation: {
                        duration: 1500,
                        easing: "circleProgressEasing"
                    }
                }).on('circle-animation-progress', function (event, progress) {
                    $(this).find('strong').text(parseInt(progress * $(this).data('number')) + "+");
                });
                $(this).addClass('applied');
            }
        });   
    }, 5000);


}(jQuery));
