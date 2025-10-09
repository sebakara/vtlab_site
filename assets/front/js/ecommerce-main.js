
$(function($) {

    "use strict";

    //===== 01. Main Menu
    function mainMenu() {
        // Variables
        var var_window = $(window),
            navContainer = $('.nav-container'),
            pushedWrap = $('.nav-pushed-item'),
            pushItem = $('.nav-push-item'),
            pushedHtml = pushItem.html(),
            pushBlank = '',
            navbarToggler = $('.navbar-toggler'),
            navMenu = $('.nav-menu'),
            navMenuLi = $('.nav-menu ul li ul li'),
            closeIcon = $('.navbar-close');
        // navbar toggler
        navbarToggler.on('click', function() {
            navbarToggler.toggleClass('active');
            navMenu.toggleClass('menu-on');
        });
        // close icon
        closeIcon.on('click', function() {
            navMenu.removeClass('menu-on');
            navbarToggler.removeClass('active');
        });

        // adds toggle button to li items that have children
        navMenu.find('li a').each(function() {
            if ($(this).next().length > 0) {
                $(this)
                    .parent('li')
                    .append(
                        '<span class="dd-trigger"><i class="fas fa-angle-down"></i></span>'
                    );
            }
        });
        // expands the dropdown menu on each click
        navMenu.find('li .dd-trigger').on('click', function(e) {
            e.preventDefault();
            $(this)
                .parent('li')
                .children('ul')
                .stop(true, true)
                .slideToggle(350);
            $(this).parent('li').toggleClass('active');
        });

        // check browser width in real-time
        function breakpointCheck() {
            var screenWidth = screen.width;
            if (screenWidth <= 991) {
                navContainer.addClass('breakpoint-on');

                pushedWrap.html(pushedHtml);
                pushItem.hide();
            } else {
                navContainer.removeClass('breakpoint-on');

                pushedWrap.html(pushBlank);
                pushItem.show();
            }
        }

        breakpointCheck();
        var_window.on('resize', function() {
            breakpointCheck();
        });
    };
    // Document Ready
    $(document).ready(function() {
        mainMenu();
    });


    //===== Sticky
    $(window).on('scroll', function(event) {
        var scroll = $(window).scrollTop();
        if (scroll < 190) {
            $(".header-navigation").removeClass("sticky");
        } else {
            $(".header-navigation").addClass("sticky");
        }
    });

    //===== Back to top
    $(window).on('scroll', function(event) {
        if ($(this).scrollTop() > 600) {
            $('.back-to-top').fadeIn(200)
        } else {
            $('.back-to-top').fadeOut(200)
        }
    });

    //Animate the scroll to top
    $('.back-to-top').on('click', function(event) {
        event.preventDefault();
        $('html, body').animate({
            scrollTop: 0,
        }, 1500);
    });
    
    //=====  Slick Slider js
    var sliderArrows= $('.hero-arrows');
    $('.hero-slide').slick({
        dots: false,
        arrows: true,
        autoplay: true,
        appendArrows: sliderArrows,
        nextArrow: '<div class="next"><i class="fas fa-long-arrow-alt-right"></i></div>',
        prevArrow: '<div class="prev"><i class="fas fa-long-arrow-alt-left"></i></div>',
        fade: true,
        Speed: 2500,
        slidesToShow: 1,
        slidesToScroll: 1,
        rtl: rtl == 1 ? true : false
    });
    $('.features-wrapper').slick({
        dots: false,
        arrows: false,
        autoplay: true,
        Speed: 2500,
        slidesToShow: 5,
        slidesToScroll: 1,
        rtl: rtl == 1 ? true : false,
        responsive: [
            {
                breakpoint: 1700,
                settings: {
                    slidesToShow: 4
                }
            },
            {
                breakpoint: 1100,
                settings: {
                    slidesToShow: 3
                }
            },
            {
                breakpoint: 991,
                settings: {
                    slidesToShow: 3
                }
            },
            {
                breakpoint: 790,
                settings: {
                    slidesToShow: 2
                }
            },
            {
                breakpoint: 700,
                settings: {
                    slidesToShow: 1
                }
            }
        ]
    });
    $('.categories-slide').slick({
        dots: false,
        arrows: false,
        autoplay: true,
        Speed: 2500,
        slidesToShow: 8,
        slidesToScroll: 1,
        rtl: rtl == 1 ? true : false,
        responsive: [
            {
                breakpoint: 1700,
                settings: {
                    slidesToShow: 5
                }
            },
            {
                breakpoint: 1100,
                settings: {
                    slidesToShow: 4
                }
            },
            {
                breakpoint: 991,
                settings: {
                    slidesToShow: 3
                }
            },
            {
                breakpoint: 768,
                settings: {
                    slidesToShow: 2
                }
            },
            {
                breakpoint: 500,
                settings: {
                    slidesToShow: 1
                }
            }
        ]
    });

    $('.featured-slide').slick({
        dots: false,
        arrows: true,
        autoplay: true,
        nextArrow: '<div class="next"><span>' + next + '</span><i class="fas fa-angle-right"></i></div>',
        prevArrow: '<div class="prev"><i class="fas fa-angle-left"></i><span>' + prev + '</span></div>',
        Speed: 2500,
        slidesToShow: 6,
        slidesToScroll: 1,
        rtl: rtl == 1 ? true : false,
        responsive: [
            {
                breakpoint: 1700,
                settings: {
                    slidesToShow: 4
                }
            },
            {
                breakpoint: 1100,
                settings: {
                    slidesToShow: 3
                }
            },
            {
                breakpoint: 991,
                settings: {
                    slidesToShow: 3
                }
            },
            {
                breakpoint: 768,
                settings: {
                    arrows: false,
                    slidesToShow: 2
                }
            },
            {
                breakpoint: 500,
                settings: {
                    arrows: false,
                    slidesToShow: 1
                }
            }
        ]
    });
    // Slick in multiple tabs
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        $('.featured-slide').slick('setPosition');
    })
    $('.shop-categories-slide').slick({
        dots: false,
        arrows: true,
        autoplay: true,
        nextArrow: '<div class="next"><i class="fas fa-angle-right"></i></div>',
        prevArrow: '<div class="prev"><i class="fas fa-angle-left"></i></div>',
        Speed: 2500,
        slidesToShow: 6,
        slidesToScroll: 1,
        rtl: rtl == 1 ? true : false,
        responsive: [
            {
                breakpoint: 1700,
                settings: {
                    slidesToShow: 4
                }
            },
            {
                breakpoint: 1100,
                settings: {
                    slidesToShow: 3
                }
            },
            {
                breakpoint: 991,
                settings: {
                    arrows: false,
                    slidesToShow: 3
                }
            },
            {
                breakpoint: 768,
                settings: {
                    arrows: false,
                    slidesToShow: 2
                }
            },
            {
                breakpoint: 500,
                settings: {
                    arrows: false,
                    slidesToShow: 1
                }
            }
        ]
    });
    $('.blog-slide').slick({
        dots: false,
        arrows: false,
        autoplay: true,
        Speed: 2500,
        slidesToShow: 4,
        slidesToScroll: 1,
        rtl: rtl == 1 ? true : false,
        responsive: [
            {
                breakpoint: 1190,
                settings: {
                    arrows: false,
                    slidesToShow: 3
                }
            },
            {
                breakpoint: 991,
                settings: {
                    arrows: false,
                    slidesToShow: 2
                }
            },
            {
                breakpoint: 768,
                settings: {
                    arrows: false,
                    slidesToShow: 1
                }
            }
        ]
    });
    $('.sponsor-slide').slick({
        dots: false,
        arrows: false,
        autoplay: true,
        Speed: 2500,
        slidesToShow: 6,
        slidesToScroll: 1,
        rtl: rtl == 1 ? true : false,
        responsive: [
            {
                breakpoint: 1365,
                settings: {
                    slidesToShow: 3
                }
            },
            {
                breakpoint: 1190,
                settings: {
                    slidesToShow: 3
                }
            },
            {
                breakpoint: 991,
                settings: {
                    arrows: false,
                    slidesToShow: 2
                }
            },
            {
                breakpoint: 768,
                settings: {
                    arrows: false,
                    slidesToShow: 1
                }
            }
        ]
    });

    // particles effect initialization for home 3
    if ($("#particles-js").length > 0) {
        particlesJS.load('particles-js', 'assets/front/js/particles.json');
    }

    // background video initialization for home 5
    if ($("#bgndVideo").length > 0) {
        $("#bgndVideo").YTPlayer();
    }

    // ripple effect initialization for home 4
    if ($("#heroHome4").length > 0) {
        $('#heroHome4').ripples({
            resolution: 500,
            dropRadius: 20,
            perturbance: 0.04
        });
    }

    $("#langForm select").on('change', function() {
        window.location = mainurl + '/changelanguage/' + $(this).val();
    });

    new LazyLoad();
});

//===== Prealoder
$(window).on('load', function(event) {
    $('.preloader').fadeOut('500');
})
