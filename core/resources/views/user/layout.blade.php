<!DOCTYPE html>
<html lang="en">
  <head>
    <meta
      http-equiv="Content-Type"
      content="text/html; charset=UTF-8"
    >
    <meta
      http-equiv="X-UA-Compatible"
      content="IE=edge"
    />
    <meta
      http-equiv="X-UA-Compatible"
      content="ie=edge"
    >
    <meta
      name="csrf-token"
      content="{{ csrf_token() }}"
    >
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0"
    >
    <title>{{$bs->website_title}} @yield('pagename')</title>
    <!-- favicon -->
    <link
      rel="shortcut icon"
      href="{{asset('assets/front/img/' . $bs->favicon)}}"
      type="image/x-icon"
    >
    <!-- bootstrap css -->
    <link
      rel="stylesheet"
      href="{{asset('assets/user/css/bootstrap.min.css')}}"
    >
    <!-- fontawesome css -->
    <link
      rel="stylesheet"
      href="{{asset('assets/user/css/fontawesome.min.css')}}"
    >
    <!-- flaticon css -->
    <link
      rel="stylesheet"
      href="{{asset('assets/user/css/flaticon.css')}}"
    >
    <!-- magnific popup css -->
    <link
      rel="stylesheet"
      href="{{asset('assets/user/css/magnific-popup.css')}}"
    >
    <!-- owl carousel css -->
    <link
      rel="stylesheet"
      href="{{asset('assets/user/css/owl.carousel.min.css')}}"
    >
    <!-- owl carousel theme css -->
    <link
      rel="stylesheet"
      href="{{asset('assets/user/css/owl.theme.default.min.css')}}"
    >
    <!-- slick css -->
    <link
      rel="stylesheet"
      href="{{asset('assets/user/css/slick.css')}}"
    >
    <link
      rel="stylesheet"
      href="{{asset('assets/user/css/nice-select.css')}}"
    >
    <!-- slicknav css -->
    <link
      rel="stylesheet"
      href="{{asset('assets/user/css/toastr.min.css')}}"
    >
    <!-- slicknav css -->
    <link
      rel="stylesheet"
      href="{{asset('assets/user/css/slicknav.css')}}"
    >
    <!-- datatables css -->
    <link
      rel="stylesheet"
      href="{{asset('assets/user/css/datatables.min.css')}}"
    >
    <link
      rel="stylesheet"
      href="{{asset('assets/user/css/dataTables.bootstrap4.css')}}"
    >
    <link
      rel="stylesheet"
      href="{{asset('assets/admin/css/summernote-bs4.css')}}"
    >
    <!-- dashboard css -->
    <link
      rel="stylesheet"
      href="{{asset('assets/user/css/dashboard.css')}}"
    >
    <!-- product css -->
    <link rel="stylesheet" href="{{asset('assets/user/css/product.css')}}">

   <!-- main css -->
   <link rel="stylesheet" href="{{asset('assets/front/css/common-style.css')}}">
   <link rel="stylesheet" href="{{asset('assets/front/css/style.css')}}">
    <!-- responsive css -->
    <link
      rel="stylesheet"
      href="{{asset('assets/user/css/responsive.css')}}"
    >
    @if ($rtl == 1)
    <!-- RTL css -->
    <link
      rel="stylesheet"
      href="{{asset('assets/front/css/rtl.css')}}"
    >
    @endif

    <!-- common base color change -->
    <link href="{{url('/')}}/assets/front/css/common-base-color.php?color={{$bs->base_color}}" rel="stylesheet">
    <!-- base color change -->
    <link href="{{url('/')}}/assets/front/css/base-color.php?color={{$bs->base_color}}{{$be->theme_version != 'dark' ? "&color1=" . $bs->secondary_base_color : ""}}" rel="stylesheet">

    @if ($be->theme_version == 'dark')
    <!-- dark version css -->
    <link
      rel="stylesheet"
      href="{{asset('assets/front/css/dark.css')}}"
    >
    <!-- dark version base color change -->
    <link
      href="{{url('/')}}/assets/front/css/dark-base-color.php?color={{$bs->base_color}}"
      rel="stylesheet"
    >
    @endif
    @yield('styles')

    <!-- jquery js -->
    <script src="{{asset('assets/user/js/jquery-3.3.1.min.js')}}"></script>
  </head>

  <body
    @if($rtl == 1)
      dir="rtl"
    @endif
  >
    <!--   header area start   -->
    <div class="header-area header-absolute @yield('no-breadcrumb')">
      <div class="container">
        <div class="support-bar-area">
          <div class="row">
            <div class="col-lg-6 support-contact-info">
              <span class="address"><i class="far fa-envelope"></i> {{$bs->support_email}}</span>
              <span class="phone"><i class="flaticon-chat"></i> {{$bs->support_phone}}</span>
            </div>
            <div class="col-lg-6 {{$rtl == 1 ? 'text-left' : 'text-right'}}">
              <ul class="social-links">
                @foreach ($socials as $key => $social)
                  <li>
                    <a target="_blank" href="{{$social->url}}">
                      <i class="{{$social->icon}}"></i>
                    </a>
                  </li>
                @endforeach
              </ul>

              @if (!empty($currentLang))
              <div class="language">
                <a class="language-btn" href="#">
                  <i class="flaticon-worldwide"></i> {{convertUtf8($currentLang->name)}}
                </a>
                <ul class="language-dropdown">
                  @foreach ($langs as $key => $lang)
                    <li><a href='{{ route('changeLanguage', $lang->code) }}'>{{convertUtf8($lang->name)}}</a></li>
                  @endforeach
                </ul>
              </div>
              @endif

              @auth
                <div class="language dashboard">
                    <a class="language-btn" href="#">
                        <i class="far fa-user"></i> {{Auth::user()->username}}
                    </a>
                    <ul class="language-dropdown">
                        <li>
                            <a href="{{route('user-dashboard')}}">{{__('Dashboard')}}</a>
                        </li>

                        @if ($bex->recurring_billing == 1)
                            <li><a href="{{route('user-packages')}}">{{__('Packages')}}</a></li>
                        @endif

                        @if ($bex->is_shop == 1 && $bex->catalog_mode == 0)
                            <li><a href="{{route('user-orders')}}">{{__('Product Orders')}} </a></li>
                        @endif

                        @if ($bex->recurring_billing == 0)
                            <li><a href="{{route('user-package-orders')}}">{{__('Package Orders')}} </a></li>
                        @endif

                        @if ($bex->is_course == 1)
                        <li>
                            <a href="{{route('user.course_orders')}}" >{{__('Courses')}}</a>
                        </li>
                        @endif

                        @if ($bex->is_event == 1)
                        <li>
                            <a href="{{route('user-events')}}">{{__('Event Bookings')}}</a>
                        </li>
                        @endif


                        @if ($bex->is_donation == 1)
                        <li>
                            <a href="{{route('user-donations')}}" >{{__('Donations')}}</a>
                        </li>
                        @endif

                        @if ($bex->is_ticket == 1)
                        <li>
                            <a href="{{route('user-tickets')}}">{{__('Support Tickets')}}</a>
                        </li>
                        @endif

                        <li>
                            <a href="{{route('user-profile')}}">{{__('Edit Profile')}}</a>
                        </li>

                        @if ($bex->is_shop == 1 && $bex->catalog_mode == 0)
                            <li>
                                <a href="{{route('shpping-details')}}">{{__('Shipping Details')}}</a>
                            </li>
                            <li>
                                <a href="{{route('billing-details')}}">{{__('Billing Details')}}</a>
                            </li>
                            <li>
                                <a href="{{route('user-reset')}}">{{__('Change Password')}}</a>
                            </li>
                        @endif
                        <li>
                            <a href="{{route('user-logout')}}" target="_self">{{__('Logout')}}</a>
                        </li>
                    </ul>
                </div>
              @endauth
            </div>
          </div>
        </div>
        @includeIf('front.default.partials.navbar')
      </div>
    </div>
    <!--   header area end   -->

    @yield('content')

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



    <!-- back to top area start -->
    <div class="back-to-top">
      <i class="fas fa-chevron-up"></i>
    </div>
    <!-- back to top area end -->


    {{-- Loader --}}
    <div class="request-loader">
      <img
        src="{{asset('assets/admin/img/loader.gif')}}"
        alt=""
      >
    </div>
    {{-- Loader --}}

    <!-- popper js -->
    <script src="{{asset('assets/user/js/popper.min.js')}}"></script>
    <!-- bootstrap js -->
    <script src="{{asset('assets/user/js/bootstrap.min.js')}}"></script>
    <!-- owl carousel js -->
    <script src="{{asset('assets/user/js/owl.carousel.min.js')}}"></script>
    <!-- slicknav js -->
    <script src="{{asset('assets/user/js/jquery.slicknav.min.js')}}"></script>
    <!-- slick js -->
    <script src="{{asset('assets/user/js/slick.min.js')}}"></script>
    <!-- isotope js -->
    <script src="{{asset('assets/user/js/isotope.pkgd.min.js')}}"></script>
    <!-- magnific popup js -->
    <script src="{{asset('assets/user/js/jquery.magnific-popup.min.js')}}"></script>
    <!-- nice select js -->
    <script src="{{asset('assets/user/js/datatables.min.js')}}"></script>
    <script src="{{asset('assets/user/js/dataTables.bootstrap4.js')}}"></script>
    <script src="{{asset('assets/user/js/toastr.min.js')}}"></script>
    <script src="{{asset('assets/user/js/lazyload.min.js')}}"></script>
    <!-- Summernote JS -->
    <script src="{{asset('assets/admin/js/plugin/summernote/summernote-bs4.js')}}"></script>

    <!-- main js -->
    <script src="{{asset('assets/user/js/main.js')}}"></script>

    <script>
      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });
    </script>

    <script>
      var imgupload = "{{route('user.summernote.upload')}}";
    </script>

    <!-- custom js -->
    <script src="{{asset('assets/user/js/custom.js')}}"></script>

    @yield('scripts')

    @if (session()->has('success'))
      <script>
        toastr["success"]("{{__(session()->get('success'))}}");
      </script>
    @endif

    @if (session()->has('error'))
      <script>
        toastr["error"]("{{__(session('error'))}}");
      </script>
    @endif

    <script>
      $(document).ready(function() {
        $('#example').DataTable({
          responsive: true
        });
      });
    </script>
  </body>
</html>
