@extends("front.$version.layout")

@section('pagename')
- {{__('Gallery')}}
@endsection

@section('meta-keywords', "$be->gallery_meta_keywords")
@section('meta-description', "$be->gallery_meta_description")

@section('breadcrumb-title', $bs->gallery_title)
@section('breadcrumb-subtitle', $bs->gallery_subtitle)
@section('breadcrumb-link', __('GALLERY'))

@section('content')
<!--    Gallery section start   -->
<section class="gallery-area-v1" id="masonry-gallery">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-10">
        @if (count($categories) > 0 && $bex->gallery_category_status == 1)
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
      <div class="row">
        @if (count($galleries) == 0)
          <div class="col">
            <h3 class="text-center">{{ __('No Gallery Image Found!') }}</h3>
          </div>
        @else
          @foreach ($galleries as $gallery)
            @php
              $galleryCategory = $gallery->galleryImgCategory()->first();

              if (!empty($galleryCategory)) {
                $categoryName = Str::slug($galleryCategory->name);
              } else {
                $categoryName = "";
              }
            @endphp

            <div class="col-lg-4 col-md-6 col-sm-12 galery-column {{ $categoryName }}">
              <div class="gallery-item mb-30">
                <div class="gallery-img">
                  <a href="{{ asset('assets/front/img/gallery/' . $gallery->image) }}" class="img-popup">
                    <img src="{{ asset('assets/front/img/gallery/' . $gallery->image) }}" alt="gallery">
                  </a>
                </div>
              </div>
            </div>
          @endforeach
        @endif
      </div>
    </div>
  </div>
</section>
<!--    Gallery section end   -->
@endsection

@section('scripts')
  <script>
    $('#masonry-gallery').imagesLoaded( function() {
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
        itemSelector: '.galery-column',
        percentPosition: true,
        masonry: {
          columnWidth: 0
        }
      });
    });

    //===== Magnific Popup
    $('.img-popup').magnificPopup({
      type: 'image',
      gallery: {
        enabled: true
      }
    });
  </script>
@endsection
