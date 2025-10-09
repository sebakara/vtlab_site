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
      <link rel="stylesheet" href="{{asset('assets/front/css/cleaning-style.css')}}">
      <!-- common css -->
      <link rel="stylesheet" href="{{asset('assets/front/css/common-style.css')}}">
      <!-- responsive css -->
      <link rel="stylesheet" href="{{asset('assets/front/css/responsive.css')}}">
      <!-- cleaning responsive css -->
      <link rel="stylesheet" href="{{asset('assets/front/css/cleaning-responsive.css')}}">

      @if ($bs->is_tawkto == 1 || $bex->is_whatsapp == 1)
      <style>
        .scroll-to-top {
            right: auto;
            left: 40px;
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
      <link href="{{url('/')}}/assets/front/css/cleaning-base-color.php?color={{$bs->base_color}}&color1={{$bs->secondary_base_color}}" rel="stylesheet">

      @if ($rtl == 1)
      <!-- RTL css -->
      <link rel="stylesheet" href="{{asset('assets/front/css/rtl.css')}}">
      <link rel="stylesheet" href="{{asset('assets/front/css/cleaning-rtl.css')}}">
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


    <!-- Start finlance_header area -->
    @includeIf('front.cleaning.partials.navbar')
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


    <!-- FOOTER PART START -->
    <footer class="footer-area pt-120">
        @if (!($bex->home_page_pagebuilder == 0 && $bs->top_footer_section == 0))
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-6">
                    <div class="footer-wedget">
                        <div class="footer-logo">
                            <a href="{{route('front.index')}}">
                                <img class="lazy" data-src="{{asset('assets/front/img/'.$bs->footer_logo)}}" alt="">
                            </a>
                        </div>
                        <p class="footer-para">
                            @if (strlen(convertUtf8($bs->footer_text)) > 140)
                                {{substr(convertUtf8($bs->footer_text), 0, 140)}}<span style="display: none;">{{substr(convertUtf8($bs->footer_text), 140)}}</span>
                                <a href="#" class="see-more">{{__('see more')}}...</a>
                            @else
                                {{convertUtf8($bs->footer_text)}}
                            @endif
                        </p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="footer-wedget">
                        <h4>{{__('Contact Us')}}</h4>
                        <p>
                            <strong><i class="fas fa-map-marker-alt"></i></strong>
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
                        <p>
                            <strong>{{__('Phone')}}:</strong>
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
                        <p>
                            <strong>{{__('Email')}}:</strong>
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
                <div class="col-lg-3 col-md-6">
                    <div class="footer-wedget">
                        <h4>{{__('Useful Links')}}</h4>
                        <ul class="footer-links">
                            @foreach ($ulinks as $key => $ulink)
                                <li><a href="{{$ulink->url}}">{{convertUtf8($ulink->name)}}</a></li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="footer-wedget">
                        <h4>{{__('Newsletter')}}</h4>
                        <p>{{convertUtf8($bs->newsletter_text)}}</p>
                        <form id="footerSubscribeForm" action="{{route('front.subscribe')}}" method="post">
                            <input type="email" name="email" required placeholder="{{__('Enter Email Address')}}">
                            <button type="submit" class="main-btn footer-form-btn">{{__('Subscribe')}}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if (!($bex->home_page_pagebuilder == 0 && $bs->copyright_section == 0))
        <section class="bootom-footer-area">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        <div class="bootom-footer-text">
                            <p>{!! replaceBaseUrl(convertUtf8($bs->copyright_text)) !!}</p>
                        </div>
                    </div>
                    <div class="col-lg-6  col-md-6 text-right">
                        <ul class="footer-social-links">
                            @foreach ($socials as $key => $social)
                                <li><a target="_blank" href="{{$social->url}}"><i class="{{$social->icon}}"></i></a></li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </section>
        @endif
    </footer>
    <!-- FOOTER PART END -->

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

    <!--======  SCROLL-TO-TOP PART START ======-->
    <div class="scroll-to-top">
        <span id="return-to-top"><i class="fa fa-arrow-up"></i></span>
    </div>
    <!--======  SCROLL-TO-TOP PART END ======-->


    {{-- WhatsApp Chat Button --}}
    <div id="WAButton"></div>

    <!--====== PRELOADER PART START ======-->
    @if ($bex->preloader_status == 1)
    <div id="preloader">
        <div class="loader revolve">
            <img src="{{asset('assets/front/img/' . $bex->preloader)}}" alt="">
        </div>
    </div>
    @endif
    <!--====== PRELOADER PART ENDS ======-->


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
      <script src="{{asset('assets/front/js/cleaning-main.js')}}"></script>
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
