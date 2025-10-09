(function ($) {
    "use strict";

    setInterval(function() {
        // lazy load init
        var lazyLoadInstance = new LazyLoad();

    }, 2000);


    setInterval(function() {
        if ($(".cke_editable ul").length > 0) {
            $(".cke_editable ul").each(function(i) {
                if (!$(this).hasClass('pagebuilder-ul')) {
                    $(this).addClass('pagebuilder-ul');
                }
            });
        }

        if ($(".cke_editable ol").length > 0) {
            $(".cke_editable ol").each(function(i) {
                if (!$(this).hasClass('pagebuilder-ol')) {
                    $(this).addClass('pagebuilder-ol');
                }
            });
        }
    }, 1000);


}(jQuery));
