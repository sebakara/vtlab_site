@extends('front.car.layout')

@section('pagename')
- {{__('Packages')}}
@endsection

@section('meta-keywords', "$be->packages_meta_keywords")
@section('meta-description', "$be->packages_meta_description")

@section('breadcrumb-title', convertUtf8($be->pricing_title))
@section('breadcrumb-subtitle', $be->pricing_subtitle)
@section('breadcrumb-link', __('Packages'))

@section('content')


<!-- Start finlance_pricing section -->
<section class="finlance_pricing pricing_v1 pt-115 pb-80" id="masonry-package">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-10">
        @if (count($categories) > 0 && $bex->package_category_status == 1)
          <div class="filter-nav text-center mb-15">
            <ul class="filter-btn">
              <li data-filter="*" class="active">{{__('All')}}</li>
              @foreach ($categories as $category)
                @php
                    $filterValue = "." . Str::slug($category->name);
                @endphp

                <li data-filter="{{ $filterValue }}">{{ convertUtf8($category->name) }}</li>
              @endforeach
            </ul>
          </div>
        @endif
      </div>
    </div>

    <div class="masonry-row">
      <div class="pricing_slide">
        <div class="row">
          @if (count($packages) == 0)
            <div class="col">
              <h3 class="text-center">{{ __('No Package Found!') }}</h3>
            </div>
          @else
            @foreach ($packages as $key => $package)
                @php
                $packageCategory = $package->packageCategory()->first();
                if (!empty($packageCategory)) {
                    $categoryName = Str::slug($packageCategory->name);
                } else {
                    $categoryName = "";
                }
                @endphp

              <div class="col-lg-4 mb-5 package-column {{ $categoryName }}">
                <div class="pricing_box text-center">
                  <div class="pricing_title">
                    <h3>{{convertUtf8($package->title)}}</h3>
                  </div>
                  <div class="pricing_price">
                    <h2>{{$bex->base_currency_symbol_position == 'left' ? $bex->base_currency_symbol : ''}}
                      {{$package->price}} {{$bex->base_currency_symbol_position == 'right' ? $bex->base_currency_symbol : ''}}
                    </h2>
                    @if ($bex->recurring_billing == 1)
                      <p class="text-capitalize">{{$package->duration == 'monthly' ? __('Monthly') : __('Yearly')}}</p>
                    @endif
                  </div>
                  <div class="pricing_body">
                    {!! replaceBaseUrl(convertUtf8($package->description)) !!}
                  </div>
                  @if ($bex->recurring_billing == 1)
                    @auth
                      @if ($activeSub->count() > 0 && empty($activeSub->first()->next_package_id))
                        @if ($activeSub->first()->current_package_id == $package->id)
                          <a href="{{route('front.packageorder.index',$package->id)}}" class="finlance_btn">{{__('Extend')}}</a>
                        @else
                          <a href="{{route('front.packageorder.index',$package->id)}}" class="finlance_btn">{{__('Change')}}</a>
                        @endif
                      @elseif ($activeSub->count() == 0)
                        <a href="{{route('front.packageorder.index',$package->id)}}" class="finlance_btn">{{__('Purchase')}}</a>
                      @endif
                    @endauth
                    @guest
                      <a href="{{route('front.packageorder.index',$package->id)}}" class="finlance_btn">{{__('Purchase')}}</a>
                    @endguest
                  @else
                    @if ($package->order_status != 0)
                      @php
                        if($package->order_status == 1) {
                          $link = route('front.packageorder.index', $package->id);
                        } elseif ($package->order_status == 2) {
                          $link = $package->link;
                        }
                      @endphp
                      <div class="pricing_button">
                        <a href="{{ $link }}" @if($package->order_status == 2) target="_blank" @endif class="finlance_btn">{{__('Place Order')}}</a>
                      </div>
                    @endif
                  @endif
                </div>
              </div>
            @endforeach
          @endif
        </div>
      </div>
    </div>
  </div>
</section>
<!-- End finlance_pricing section -->
@endsection

@section('scripts')
<script>
  $('#masonry-package').imagesLoaded( function() {
    // items on button click
    $('.filter-btn').on('click', 'li', function () {
      var filterValue = $(this).attr('data-filter');
      $grid.isotope({
        filter: filterValue
      });
    });
    // menu active class
    $('.filter-btn li').on('click', function (e) {
      $(this).siblings('.active').removeClass('active');
      $(this).addClass('active');
      e.preventDefault();
    });
    var $grid = $('.masonry-row').isotope({
      itemSelector: '.package-column',
      percentPosition: true,
      masonry: {
        columnWidth: 0
      }
    });
  });
</script>
@endsection
