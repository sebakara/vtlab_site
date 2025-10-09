<!DOCTYPE html>
<html lang="en">
   <head>
      <!--Start of Google Analytics script-->
      @if ($bs->is_analytics == 1)
      {!! $bs->google_analytics_script !!}
      @endif
      <!--End of Google Analytics script-->

      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0">

      <meta name="description" content="@yield('meta-description')">
      <meta name="keywords" content="@yield('meta-keywords')">

      <meta name="csrf-token" content="{{ csrf_token() }}">
      <title>{{$bs->website_title}} @yield('pagename')</title>
      <!-- favicon -->
      <link rel="shortcut icon" href="{{asset('assets/front/img/'.$bs->favicon)}}" type="image/x-icon">
      <!-- bootstrap css -->
      <link rel="stylesheet" href="{{asset('assets/front/css/bootstrap.min.css')}}">
      <!-- plugin css -->
      <link rel="stylesheet" href="{{asset('assets/front/css/plugin.min.css')}}">
      <!--default css-->
      <link rel="stylesheet" href="{{asset('assets/front/css/default.css')}}">
      <!-- main css -->
      <link rel="stylesheet" href="{{asset('assets/front/css/lawyer-style.css')}}">
      <!-- common css -->
      <link rel="stylesheet" href="{{asset('assets/front/css/common-style.css')}}">
      <!-- responsive css -->
      <link rel="stylesheet" href="{{asset('assets/front/css/responsive.css')}}">
      <!-- lawyer responsive css -->
      <link rel="stylesheet" href="{{asset('assets/front/css/lawyer-responsive.css')}}">

      @if ($bs->is_tawkto == 1 || $bex->is_whatsapp == 1)
      <style>
        #scroll_up {
            right: auto;
            left: 20px;
        }
      </style>
      @endif
      @if (count($langs) == 0)
      <style media="screen">
      .support-bar-area ul.social-links li:last-child {
          margin-right: 0px;
      }
      .support-bar-area ul.social-links::after {
          display: none;
      }
      </style>
      @endif

      <!-- common base color change -->
      <link href="{{url('/')}}/assets/front/css/common-base-color.php?color={{$bs->base_color}}" rel="stylesheet">
      <!-- base color change -->
      <link href="{{url('/')}}/assets/front/css/lawyer-base-color.php?color={{$bs->base_color}}" rel="stylesheet">


      @if ($rtl == 1)
      <!-- RTL css -->
      <link rel="stylesheet" href="{{asset('assets/front/css/rtl.css')}}">
      <link rel="stylesheet" href="{{asset('assets/front/css/lawyer-rtl.css')}}">
      <link rel="stylesheet" href="{{asset('assets/front/css/pb-rtl.css')}}">
      @endif
      @yield('styles')

      <!-- jquery js -->
      <script src="{{asset('assets/front/js/jquery-3.3.1.min.js')}}"></script>

      @if ($bs->is_appzi == 1)
      <!-- Start of Appzi Feedback Script -->
      <script async src="https://app.appzi.io/bootstrap/bundle.js?token={{$bs->appzi_token}}"></script>
      <!-- End of Appzi Feedback Script -->
      @endif

      <!-- Start of Facebook Pixel Code -->
      @if ($be->is_facebook_pexel == 1)
        {!! $be->facebook_pexel_script !!}
      @endif
      <!-- End of Facebook Pixel Code -->

      <!--Start of Appzi script-->
      @if ($bs->is_appzi == 1)
      {!! $bs->appzi_script !!}
      @endif
      <!--End of Appzi script-->
   </head>



   <body @if($rtl == 1) dir="rtl" @endif>

    <div class="top_header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <div class="top_left">
                        <span><i class="fas fa-headphones"></i><a href="tel:{{$bs->support_phone}}">{{$bs->support_phone}}</a></span>
                        <span><i class="far fa-envelope"></i><a href="mailto:{{$bs->support_email}}">{{$bs->support_email}}</a></span>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <div class="top_right d-flex align-items-center justify-content-end">
                        <ul class="social">
                            @foreach ($socials as $key => $social)
                                <li><a href="{{$social->url}}"><i class="{{$social->icon}}"></i></a></li>
                            @endforeach
                        </ul>

                        @if (!empty($currentLang) && count($langs) > 1)
                            <div class="dropdown">
                                <button type="button" class="btn dropdown-toggle" data-toggle="dropdown"><i class="fas fa-globe"></i>{{convertUtf8($currentLang->name)}}
                                </button>
                                <div class="dropdown-menu">
                                    @foreach ($langs as $key => $lang)
                                        <a class="dropdown-item" href='{{ route('changeLanguage', $lang->code) }}'>{{convertUtf8($lang->name)}}</a>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @guest
                        @if ($bex->is_user_panel == 1)
                            <ul class="login">
                                <li><a href="{{route('user.login')}}">{{__('Login')}}</a></li>
                            </ul>
                        @endif
                        @endguest
                        @auth
                        <div class="dropdown ml-4">
                            <button type="button" class="btn dropdown-toggle" data-toggle="dropdown"><i class="far fa-user mr-1"></i> {{Auth::user()->username}}
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="{{route('user-dashboard')}}">{{__('Dashboard')}}</a>
                                @if ($bex->recurring_billing == 1)
                                    <a class="dropdown-item" href="{{route('user-packages')}}">{{__('Packages')}}</a>
                                @endif
                                @if ($bex->is_shop == 1 && $bex->catalog_mode == 0)
                                    <a class="dropdown-item" href="{{route('user-orders')}}">{{__('Product Orders')}} </a>
                                @endif
                                @if ($bex->recurring_billing == 0)
                                    <a class="dropdown-item" href="{{route('user-package-orders')}}">{{__('Package Orders')}} </a>
                                @endif

                                @if ($bex->is_course == 1)
                                <a class="dropdown-item" href="{{route('user.course_orders')}}" >{{__('Courses')}}</a>
                                @endif

                                @if ($bex->is_event == 1)
                                <a class="dropdown-item" href="{{route('user-events')}}" >{{__('Event Bookings')}}</a>
                                @endif

                                @if ($bex->is_donation == 1)
                                <a class="dropdown-item" href="{{route('user-donations')}}" >{{__('Donations')}}</a>
                                @endif
                                @if ($bex->is_ticket == 1)
                                    <a class="dropdown-item" href="{{route('user-tickets')}}">{{__('Support Tickets')}}</a>
                                @endif
                                <a class="dropdown-item" href="{{route('user-profile')}}">{{__('Edit Profile')}}</a>
                                @if ($bex->is_shop == 1 && $bex->catalog_mode == 0)
                                    <a class="dropdown-item" href="{{route('shpping-details')}}">{{__('Shipping Details')}}</a>
                                    <a class="dropdown-item" href="{{route('billing-details')}}">{{__('Billing Details')}}</a>
                                    <a class="dropdown-item" href="{{route('user-reset')}}">{{__('Change Password')}}</a>
                                @endif
                                <a class="dropdown-item" href="{{route('user-logout')}}" target="_self">{{__('Logout')}}</a>
                            </div>
                        </div>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Start finlance_header area -->
    @includeIf('front.lawyer.partials.navbar')
    <!-- End finlance_header area -->


    @if (!request()->routeIs('front.index') && !request()->routeIs('front.packageorder.confirmation'))
        <!--   breadcrumb area start   -->
        <div class="breadcrumb-area lazy" data-bg="{{asset('assets/front/img/' . $bs->breadcrumb)}}" style="background-size:cover;">
            <div class="container">
            <div class="breadcrumb-txt">
                <div class="row">
                    <div class="col-xl-7 col-lg-8 col-sm-10">
                        <span>@yield('breadcrumb-title')</span>
                        <h1>@yield('breadcrumb-subtitle')</h1>
                        <ul class="breadcumb">
                        <li><a href="{{route('front.index')}}">{{__('Home')}}</a></li>
                        <li>@yield('breadcrumb-link')</li>
                        </ul>
                    </div>
                </div>
            </div>
            </div>
            <div class="breadcrumb-area-overlay" style="background-color: #{{$be->breadcrumb_overlay_color}};opacity: {{$be->breadcrumb_overlay_opacity}};"></div>
        </div>
        <!--   breadcrumb area end    -->
    @endif

    @yield('content')


    <!--    announcement banner section start   -->
    <a class="announcement-banner" href="{{asset('assets/front/img/'.$bs->announcement)}}"></a>
    <!--    announcement banner section end   -->


    <!-- Start lawyer_footer section -->
    <footer class="lawyer_footer footer_v1 dark_bg">
        @if (!($bex->home_page_pagebuilder == 0 && $bs->top_footer_section == 0))
        <div class="footer_top pt-120 pb-120">
            <div class="container">
                <div class="row">
                    <div class="col-lg-3 col-md-6 col-sm-12">
                        <div class="widget_box about_widget">
                            <img data-src="{{asset('assets/front/img/'.$bs->footer_logo)}}" class="img-fluid lazy" alt="">
                            <p>
                                @if (strlen(convertUtf8($bs->footer_text)) > 150)
                                   {{substr(convertUtf8($bs->footer_text), 0, 150)}}<span style="display: none;">{{substr(convertUtf8($bs->footer_text), 150)}}</span>
                                   <a href="#" class="see-more">{{__('see more')}}...</a>
                                @else
                                   {{convertUtf8($bs->footer_text)}}
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-12">
                        <div class="widget_box contact_widget">
                            <h4 class="widget_title">{{__('Contact Us')}}</h4>
                            <p>
                                <span><i class="fas fa-map-marker-alt"></i></span>
                                @php
                                $addresses = explode(PHP_EOL, $bex->contact_addresses);
                                @endphp

                                @foreach ($addresses as $address)
                                    {{$address}}
                                    @if (!$loop->last)
                                        |
                                    @endif
                                @endforeach
                            </p>
                            <p><span>{{__('Phone')}}:</span>
                                @php
                                $phones = explode(',', $bex->contact_numbers);
                                @endphp

                                @foreach ($phones as $phone)
                                    {{$phone}}
                                    @if (!$loop->last)
                                        ,
                                    @endif
                                @endforeach

                            </p>
                            <p><span>{{__('Email')}}:</span>
                                @php
                                    $mails = explode(',', $bex->contact_mails);
                                @endphp
                                @foreach ($mails as $mail)
                                    {{$mail}}
                                    @if (!$loop->last)
                                        ,
                                    @endif
                                @endforeach
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-12">
                        <div class="widget_box">
                            <h4 class="widget_title">{{__('Useful Links')}}</h4>
                            <ul class="widget_link">
                                @foreach ($ulinks as $key => $ulink)
                                    <li><a href="{{$ulink->url}}">{{convertUtf8($ulink->name)}}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-12">
                        <div class="widget_box newsletter_box">
                            <h4 class="widget_title">{{__('Newsletter')}}</h4>
                            <p>{{convertUtf8($bs->newsletter_text)}}</p>
                            <form id="footerSubscribeForm" action="{{route('front.subscribe')}}" method="post">
                                @csrf
                                <div class="form_group">
                                    <input type="email" class="form_control" placeholder="{{__('Enter Email Address')}}" name="email" required>
                                    <p id="erremail" class="text-danger mb-0 err-email"></p>
                                    <button class="lawyer_btn" type="submit">{{__('Subscribe')}}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if (!($bex->home_page_pagebuilder == 0 && $bs->copyright_section == 0))
        <div class="footer_bottom">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <div class="copyright_text">
                            <p>{!! replaceBaseUrl(convertUtf8($bs->copyright_text)) !!}</p>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="social_box">
                            <ul>
                                @foreach ($socials as $key => $social)
                                    <li><a target="_blank" href="{{$social->url}}"><i class="{{$social->icon}}"></i></a></li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </footer><!-- End lawyer_footer section -->

    @if ($bex->is_shop == 1 && $bex->catalog_mode == 0)
        <div id="cartIconWrapper">
            <a class="d-block" id="cartIcon" href="{{route('front.cart')}}">
                <div class="cart-length">
                    <i class="fas fa-cart-plus"></i>
                    <span class="length">{{cartLength()}} {{__('ITEMS')}}</span>
                </div>
                <div class="cart-total">
                    {{$bex->base_currency_symbol_position == 'left' ? $bex->base_currency_symbol : ''}}
                    {{cartTotal()}}
                    {{$bex->base_currency_symbol_position == 'right' ? $bex->base_currency_symbol : ''}}
                </div>
            </a>
        </div>
    @endif

    <!--====== PRELOADER PART START ======-->
    @if ($bex->preloader_status == 1)
    <div id="preloader">
        <div class="loader revolve">
            <img src="{{asset('assets/front/img/' . $bex->preloader)}}" alt="">
        </div>
    </div>
    @endif
    <!--====== PRELOADER PART ENDS ======-->

    <!--Scroll-up-->
    <a id="scroll_up" ><i class="fas fa-angle-up"></i></a>

    {{-- WhatsApp Chat Button --}}
    <div id="WAButton"></div>

    {{-- Cookie alert dialog start --}}
    @if ($be->cookie_alert_status == 1)
    @include('cookie-consent::index')
    @endif
    {{-- Cookie alert dialog end --}}

    {{-- Popups start --}}
    @includeIf('front.partials.popups')
    {{-- Popups end --}}

      @php
        $mainbs = [];
        $mainbs = json_encode($mainbs);
      @endphp
      <script>
        var mainbs = {!! $mainbs !!};
        var mainurl = "{{url('/')}}";
        var vap_pub_key = "{{env('VAPID_PUBLIC_KEY')}}";

        var rtl = {{ $rtl }};
      </script>
      <!-- popper js -->
      <script src="{{asset('assets/front/js/popper.min.js')}}"></script>
      <!-- bootstrap js -->
      <script src="{{asset('assets/front/js/bootstrap.min.js')}}"></script>
      <!-- Plugin js -->
      <script src="{{asset('assets/front/js/plugin.min.js')}}"></script>
      <!-- main js -->
      <script src="{{asset('assets/front/js/lawyer-main.js')}}"></script>
      <!-- pagebuilder custom js -->
      <script src="{{asset('assets/front/js/common-main.js')}}"></script>
      {{-- whatsapp init code --}}
      @if ($bex->is_whatsapp == 1)
        <script type="text/javascript">
            var whatsapp_popup = {{$bex->whatsapp_popup}};
            var whatsappImg = "{{asset('assets/front/img/whatsapp.svg')}}";
            $(function () {
                $('#WAButton').floatingWhatsApp({
                    phone: "{{$bex->whatsapp_number}}", //WhatsApp Business phone number
                    headerTitle: "{{$bex->whatsapp_header_title}}", //Popup Title
                    popupMessage: `{!! nl2br($bex->whatsapp_popup_message) !!}`, //Popup Message
                    showPopup: whatsapp_popup == 1 ? true : false, //Enables popup display
                    buttonImage: '<img src="' + whatsappImg + '" />', //Button Image
                    position: "right" //Position: left | right

                });
            });
        </script>
      @endif

      @yield('scripts')

      @if (session()->has('success'))
      <script>
         toastr["success"]("{{__(session('success'))}}");
      </script>
      @endif

      @if (session()->has('error'))
      <script>
         toastr["error"]("{{__(session('error'))}}");
      </script>
      @endif

      <!--Start of subscribe functionality-->
      <script>
        $(document).ready(function() {
          $("#subscribeForm, #footerSubscribeForm").on('submit', function(e) {
            // console.log($(this).attr('id'));

            e.preventDefault();

            let formId = $(this).attr('id');
            let fd = new FormData(document.getElementById(formId));
            let $this = $(this);

            $.ajax({
              url: $(this).attr('action'),
              type: $(this).attr('method'),
              data: fd,
              contentType: false,
              processData: false,
              success: function(data) {
                // console.log(data);
                if ((data.errors)) {
                  $this.find(".err-email").html(data.errors.email[0]);
                } else {
                  toastr["success"]("You are subscribed successfully!");
                  $this.trigger('reset');
                  $this.find(".err-email").html('');
                }
              }
            });
          });

            // lory slider responsive
            $(".gjs-lory-frame").each(function() {
                let id = $(this).parent().attr('id');
                $("#"+id).attr('style', 'width: 100% !important');
            });
        });
      </script>
      <!--End of subscribe functionality-->

      <!--Start of Tawk.to script-->
      @if ($bs->is_tawkto == 1)
      {!! $bs->tawk_to_script !!}
      @endif
      <!--End of Tawk.to script-->

      <!--Start of AddThis script-->
      @if ($bs->is_addthis == 1)
      {!! $bs->addthis_script !!}
      @endif
      <!--End of AddThis script-->
   </body>
</html>
