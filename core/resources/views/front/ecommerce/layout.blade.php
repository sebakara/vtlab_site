<!DOCTYPE html>
<html lang="en" @if ($rtl == 1) dir="rtl" @endif>

<head>
  <!--Start of Google Analytics script-->
  @if ($bs->is_analytics == 1)
    {!! $bs->google_analytics_script !!}
  @endif
  <!--End of Google Analytics script-->

  <!--====== Required meta tags ======-->
  <meta charset="utf-8">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <meta name="description" content="">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <meta name="description" content="@yield('meta-description')">
  <meta name="keywords" content="@yield('meta-keywords')">

  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ $bs->website_title }} @yield('pagename')</title>
  <!-- favicon -->
  <link rel="shortcut icon" href="{{ asset('assets/front/img/' . $bs->favicon) }}" type="image/x-icon">
  <!-- bootstrap css -->
  <link rel="stylesheet" href="{{ asset('assets/front/css/bootstrap.min.css') }}">
  <!-- plugin css -->
  <link rel="stylesheet" href="{{ asset('assets/front/css/plugin.min.css') }}">
  <!--default css-->
  <link rel="stylesheet" href="{{ asset('assets/front/css/default.css') }}">
  <!-- common css -->
  <link rel="stylesheet" href="{{ asset('assets/front/css/common-style.css') }}">
  <!-- main css -->
  <link rel="stylesheet" href="{{ asset('assets/front/css/ecommerce-style.css') }}">
  <!-- responsive css -->
  <link rel="stylesheet" href="{{ asset('assets/front/css/responsive.css') }}">
  <!-- ecommerce responsive css -->
  <link rel="stylesheet" href="{{ asset('assets/front/css/ecommerce-responsive.css') }}">

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

    </style>
  @endif

  <!-- common base color change -->
  <link href="{{ url('/') }}/assets/front/css/common-base-color.php?color={{ $bs->base_color }}"
    rel="stylesheet">
  <!-- base color change -->
  <link href="{{ url('/') }}/assets/front/css/ecommerce-base-color.php?color={{ $bs->base_color }}"
    rel="stylesheet">


  @if ($rtl == 1)
    <!-- RTL css -->
    <link rel="stylesheet" href="{{ asset('assets/front/css/rtl.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/front/css/ecommerce-rtl.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/front/css/pb-rtl.css') }}">
  @endif
  @yield('styles')

  <!-- jquery js -->
  <script src="{{ asset('assets/front/js/jquery-3.3.1.min.js') }}"></script>

  @if ($bs->is_appzi == 1)
    <!-- Start of Appzi Feedback Script -->
    <script async src="https://app.appzi.io/bootstrap/bundle.js?token={{ $bs->appzi_token }}"></script>
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

<body>

  @includeIf('front.ecommerce.partials.header')

  @if (!request()->routeIs('front.index') && !request()->routeIs('front.packageorder.confirmation'))
    <!--   breadcrumb area start   -->
    <div class="breadcrumb-area lazy" data-bg="{{ asset('assets/front/img/' . $bs->breadcrumb) }}"
      style="background-size:cover;">
      <div class="container">
        <div class="breadcrumb-txt">
          <div class="row">
            <div class="col-xl-7 col-lg-8 col-sm-10">
              <span>@yield('breadcrumb-title')</span>
              <h1>@yield('breadcrumb-subtitle')</h1>
              <ul class="breadcumb">
                <li><a href="{{ route('front.index') }}">{{ __('Home') }}</a></li>
                <li>@yield('breadcrumb-link')</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
      <div class="breadcrumb-area-overlay"
        style="background-color: #{{ $be->breadcrumb_overlay_color }};opacity: {{ $be->breadcrumb_overlay_opacity }};">
      </div>
    </div>
    <!--   breadcrumb area end    -->
  @endif

  @yield('content')

  @includeIf('front.ecommerce.partials.footer')

  @if ($bex->is_shop == 1 && $bex->catalog_mode == 0)
    <div id="cartIconWrapper">
      <a class="d-block" id="cartIcon" href="{{ route('front.cart') }}">
        <div class="cart-length">
          <i class="fas fa-cart-plus"></i>
          <span class="length">{{ cartLength() }} {{ __('ITEMS') }}</span>
        </div>
        <div class="cart-total">
          {{ $bex->base_currency_symbol_position == 'left' ? $bex->base_currency_symbol : '' }}
          {{ cartTotal() }}
          {{ $bex->base_currency_symbol_position == 'right' ? $bex->base_currency_symbol : '' }}
        </div>
      </a>
    </div>
  @endif

  <!--====== back-to-top ======-->
  <a href="#" class="back-to-top"><i class="fas fa-angle-up"></i></a>

  <!--====== Start Preloader ======-->
  @if ($bex->preloader_status == 1)
    <div class="preloader">
      <div class="lds-ellipsis">
        <img src="{{ asset('assets/front/img/' . $bex->preloader) }}" alt="">
      </div>
    </div>
  @endif
  <!--====== End Preloader ======-->

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

  @includeIf('front.ecommerce.partials.scripts')

  @yield('scripts')
</body>

</html>
