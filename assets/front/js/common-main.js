

function popupAnnouncement($this) {
    let closedPopups = [];
    if (sessionStorage.getItem('closedPopups')) {
        closedPopups = JSON.parse(sessionStorage.getItem('closedPopups'));
    }
    
    // if the popup is not in closedPopups Array
    if (closedPopups.indexOf($this.data('popup_id')) == -1) {
        // console.log($this.data('popup_id'));
        $('#' + $this.attr('id')).show();
        let popupDelay = $this.data('popup_delay');

        setTimeout(function() {
            jQuery.magnificPopup.open({
                items: {src: '#' + $this.attr('id')},
                type: 'inline',
                callbacks: {
                    afterClose: function() {
                        // after the popup is closed, store it in the sessionStorage & show next popup
                        closedPopups.push($this.data('popup_id'));
                        sessionStorage.setItem('closedPopups', JSON.stringify(closedPopups));
    
                        // console.log('closed', $this.data('popup_id'));
                        if ($this.next('.popup-wrapper').length > 0) {
                            popupAnnouncement($this.next('.popup-wrapper'));
                        }
                    }
                }
            }, 0);
        }, popupDelay);
    } else {
        if ($this.next('.popup-wrapper').length > 0) {
            popupAnnouncement($this.next('.popup-wrapper'));
        }
    }
}

$(window).on('load', function() {

    if ($(".popup-wrapper").length > 0) {
        $firstPopup = $(".popup-wrapper").eq(0);
        popupAnnouncement($firstPopup);
    }
    initSW();
});


// push notification start
function initSW() {
    if (!"serviceWorker" in navigator) {
        //service worker isn't supported
        return;
    }

    //don't use it here if you use service worker
    //for other stuff.
    if (!"PushManager" in window) {
        //push isn't supported
        return;
    }

    //register the service worker
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('./sw.js')
            .then(() => {
                // console.log('serviceWorker registered!')
                initPush();
            })
            .catch((err) => {
                // console.log(err)
            });
    }
}


function initPush() {
    if (!navigator.serviceWorker.ready) {
        return;
    }

    new Promise(function(resolve, reject) {
            const permissionResult = Notification.requestPermission().then(function(result) {
                resolve(result);
            });

            if (permissionResult) {
                permissionResult.then(resolve, reject);
            }
        })
        .then((permissionResult) => {
            if (permissionResult !== 'granted') {
                throw new Error('We weren\'t granted permission.');
            }
            subscribeUser();
        });
}

function subscribeUser() {
    navigator.serviceWorker.ready
        .then((registration) => {
            const subscribeOptions = {
                userVisibleOnly: true,
                applicationServerKey: urlBase64ToUint8Array(
                    vap_pub_key
                )
            };

            return registration.pushManager.subscribe(subscribeOptions);
        })
        .then((pushSubscription) => {
            // console.log('Received PushSubscription: ', JSON.stringify(pushSubscription));
            storePushSubscription(pushSubscription);
        })
        .catch(err => {
            // console.log(err);
        });
}

function urlBase64ToUint8Array(base64String) {
    var padding = '='.repeat((4 - base64String.length % 4) % 4);
    var base64 = (base64String + padding)
        .replace(/\-/g, '+')
        .replace(/_/g, '/');

    var rawData = window.atob(base64);
    var outputArray = new Uint8Array(rawData.length);

    for (var i = 0; i < rawData.length; ++i) {
        outputArray[i] = rawData.charCodeAt(i);
    }
    return outputArray;
}

function storePushSubscription(pushSubscription) {
    const token = document.querySelector('meta[name=csrf-token]').getAttribute('content');
    // console.log(mainurl + '/push');
    fetch(mainurl + '/push', {
            method: 'POST',
            body: JSON.stringify(pushSubscription),
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-Token': token
            }
        })
        .then((res) => {
            return res.json();
        })
        .then((res) => {
            // console.log(res)
        })
        .catch((err) => {
            // console.log(err)
        });
}

// push notification end

(function ($) {
    "use strict";

    $('.offer-timer').each(function() {
        let $this = $(this);
        let d = new Date($this.data('end_date'));
        let ye = parseInt(new Intl.DateTimeFormat('en', {year: 'numeric'}).format(d));
        let mo = parseInt(new Intl.DateTimeFormat('en', {month: 'numeric'}).format(d));
        let da = parseInt(new Intl.DateTimeFormat('en', {day: '2-digit'}).format(d));
        let t = $this.data('end_time');
        let time = t.split(":");
        let hr = parseInt(time[0]);
        let min = parseInt(time[1]);
        $this.syotimer({
            year: ye,
            month: mo,
            day: da,
            hour: hr,
            minute: min,
        });
    });

    $(".datepicker").datepicker({ autoclose: !0 }),
        $("input.timepicker").timepicker(),
        $(".course-slide").slick({
            dots: !1,
            arrows: !0,
            infinite: !0,
            autoplay: !1,
            autoplaySpeed: 2500,
            prevArrow: '<div class="prev"><i class="fas fa-angle-left"></i></div>',
            nextArrow: '<div class="next"><i class="fas fa-angle-right"></i></div>',
            slidesToShow: 3,
            slidesToScroll: 1,
            rtl: 1 == rtl,
            responsive: [
                { breakpoint: 1024, settings: { arrows: !1, slidesToShow: 2 } },
                { breakpoint: 992, settings: { arrows: !1, slidesToShow: 1 } },
                { breakpoint: 480, settings: { arrows: !1, slidesToShow: 1 } },
            ],
        });

    $('a.see-more').on('click', function(e) {
        e.preventDefault();
        $(this).prev('span').addClass('d-inline');
        $(this).hide();
    })
    
    $(".gjs-lory-frame").each(function () {
        let e = $(this).parent().attr("id");
        $("#" + e).attr("style", "width: 100% !important");
    }),
        $(window).on("load", function () {
            $("#preloader").fadeOut(500);
        });


    $(".mega-dropdown").hover(function() {
        // show all tab contents
        $(".mega-tab").removeClass('d-none');
        $(".mega-tab").addClass('d-block');

        // make 'All' link active
        $(".megamenu-cats ul li").removeClass('active');
        $(".megamenu-cats ul li:first-child").addClass('active');
    });
    
    $(".megamenu-cats li a").hover(function(e) {
        e.preventDefault();
        let tabid = $(this).data('tabid');
        
        // make selected anchor tag active
        $(".megamenu-cats li").removeClass('active');
        $(this).parent('li').addClass('active');

        if(tabid == 'all') {
            // show all tab contents
            $(".mega-tab").removeClass('d-none');
            $(".mega-tab").addClass('d-block');
        } else {
            // hide all tab contents
            $(".mega-tab").removeClass('d-block');
            $(".mega-tab").addClass('d-none');
    
            // show the tab content of selected category
            $(tabid).removeClass('d-none');
            $(tabid).addClass('d-block');
        }

    });

    $('.single-ss').on('click', function(e) {
        e.preventDefault();
        let id = $(this).data('id');
        $("#singleMagnificSs"+id).trigger('click');
    });

    // project carousel
    $('.single-magnific-ss').magnificPopup({
        type: 'image',
        gallery:{
            enabled:true
        }
    });

    // Project ss carousel
    var projectCarousel = $('.project-ss-carousel');
    projectCarousel.owlCarousel({
        loop: true,
        dots: true,
        nav: true,
        navText: ["<i class='flaticon-left-arrow'></i>", "<i class='flaticon-right-arrow'></i>"],
        autoplay: false,
        autoplayTimeout: 5000,
        smartSpeed: 1500,
        rtl: rtl == 1 ? true : false,
        items: 1
    });
})(jQuery);
