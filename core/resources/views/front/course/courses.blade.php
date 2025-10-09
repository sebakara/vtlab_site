@extends("front.$version.layout")

@section('pagename')
  - {{__('All Courses')}}
@endsection

@section('styles')
  <link
    rel="stylesheet"
    href="{{ asset('assets/front/css/jquery-ui.min.css') }}"
  >

  <link
    rel="stylesheet"
    href="{{ asset('assets/front/css/nice-select.css') }}"
  >
  <style>
      .nice-select {
          width: 100%;
      }
  </style>
@endsection

@section('breadcrumb-title', $bex->course_title)
@section('breadcrumb-subtitle', $bex->course_subtitle)
@section('breadcrumb-link', __('Courses'))

@section('content')

  {{-- featured course start --}}
  @if (count($featured_courses) > 0)
  <section
    class="course-area-v1 pt-80 pb-120 bg_cover"
    style="background-image: url(assets/front/img/counter-bg-1.png);"
  >
    <div class="container">
      @if (count($featured_courses) == 0)
        <div class="row">
          <div class="col">
            <div class="py-5 text-center">
              <h3 class="text-light">{{__('No Featured Course Found!')}}</h3>
            </div>
          </div>
        </div>
      @else
        <div class="row">
            <div class="col">
                <div class="py-5 text-center">
                    <h2 class="text-light">{{__('OUR FEATURED COURSES')}}</h2>
                </div>
            </div>
        </div>
        <div class="course-slide row">
          @foreach ($featured_courses as $featured_course)
            <div class="course-item col-lg-4">
                <div class="course-img">
                    <img data-src="{{ asset('assets/front/img/courses/' . $featured_course->course_image) }}" class="img-fluid lazy" alt="" >
                    <a href="{{ route('course_details', ['slug' => $featured_course->slug]) }}" class="course-overlay">
                        @if ($bex->is_course_rating == 1)
                            <div class="rating">
                                <p><i class="fas fa-star"></i>{{round($featured_course->review()->avg('rating'), 2)}} <span>({{$featured_course->review()->count()}})</span></p>
                            </div>
                        @endif
                        <div class="categorie-box">
                            <a
                                href="{{route('courses', ['category_id' => $featured_course->course_category_id])}}"
                                class="main-btn"
                            >{{ $featured_course->courseCategory->name }}</a>
                        </div>
                    </a>
                </div>

              <div class="course-content">
                <h3>
                  <a href="{{ route('course_details', ['slug' => $featured_course->slug]) }}">
                    {{strlen($featured_course->title) > 30 ? mb_substr($featured_course->title, 0, 30, 'utf-8') . '...' : $featured_course->title}}
                  </a>
                </h3>
                <div class="course-admin-price">
                    <div class="admin">
                      <img src="{{asset('assets/front/img/instructors/' . $featured_course->instructor_image)}}" class="img-fluid" alt="">
                      <span><a>{{$featured_course->instructor_name}}</a></span>
                    </div>
                    <div class="price">
                      @if (empty($featured_course->current_price))
                        <span>{{ __('Free') }}</span>
                      @else
                          <span>{{ $bse->base_currency_symbol_position == 'left' ? $bse->base_currency_symbol : '' }} {{ $featured_course->current_price }} {{ $bse->base_currency_symbol_position == 'right' ? $bse->base_currency_symbol : '' }}</span>
                          @if (!empty($featured_course->previous_price))
                              <span class="pre-price"><del>{{ $bse->base_currency_symbol_position == 'left' ? $bse->base_currency_symbol : '' }} {{ $featured_course->previous_price }} {{ $bse->base_currency_symbol_position == 'right' ? $bse->base_currency_symbol : '' }}</del></span>
                          @endif
                      @endif
                    </div>
                  </div>
                <div class="course-meta">
                  <span><i class="fas fa-users"></i>{{$featured_course->coursePurchase()->where('payment_status', 'Completed')->count()}} {{__('Students')}}</span>
                  <span><i class="far fa-clock"></i>{{ $featured_course->duration }}</span>
                </div>
              </div>
            </div>
          @endforeach
        </div>
      @endif
    </div>
  </section>
  {{-- featured course end --}}
  @endif

  {{-- all courses start --}}
  <section class="courses-grid-style pt-120 pb-80">
    <div class="container">
      <div class="row">
        <div class="col-lg-4">
          <div class="courses-sidebar">
            <div class="widget-box courses-type-widget">
              <h4>{{ __('Course Type') }}</h4>
              <div class="single_radio">
                <input
                  type="radio"
                  id="radio1"
                  class="course-type"
                  name="checked_value"
                  value="all"
                  {{ request()->input('checked_value') == 'all' || empty(request()->input('checked_value')) ? 'checked' : '' }}
                >
                <label for="radio1"><span class="circle"></span><span class="text">{{ __('All Courses') }}</span></label>
              </div>
              <div class="single_radio">
                <input
                  type="radio"
                  id="radio2"
                  class="course-type"
                  name="checked_value"
                  value="free"
                  {{ request()->input('checked_value') == 'free' ? 'checked' : '' }}
                >
                <label for="radio2"><span class="circle"></span><span class="text">{{ __('Free Courses') }}</span></label>
              </div>
              <div class="single_radio">
                <input
                  type="radio"
                  id="radio3"
                  class="course-type"
                  name="checked_value"
                  value="premium"
                  {{ request()->input('checked_value') == 'premium' ? 'checked' : '' }}
                >
                <label for="radio3"><span class="circle"></span><span class="text">{{ __('Premium Courses') }}</span></label>
              </div>
            </div>

            <div class="widget-box categories-widget">
              <h4>{{ __('Categories') }}</h4>
              <ul>
                <li class="{{ empty(request()->input('category_id')) ? 'active-search' : '' }}">
                    <a
                      data-href=""
                      id="categoryId"
                    >{{__('All')}} <span>{{ '(' . $courseCount . ')' }}</span></a>
                </li>
                @foreach ($course_categories as $course_category)
                <li class="{{ request()->input('category_id') == $course_category->id ? 'active-search' : '' }}">
                  <a
                    data-href="{{ $course_category->id }}"
                    id="categoryId"
                  >{{ convertUtf8($course_category->name) }} <span>{{ '(' . $course_category->courses()->count() . ')' }}</span></a>
                </li>
                @endforeach
              </ul>
            </div>


            @if ($bex->is_course_rating == 1)
            <div class="widget-box courses-type-widget">
              <h4>{{ __('Filter By Rating') }}</h4>
              <div class="single_radio">
                <input
                  type="radio"
                  id="ratingAll"
                  class="rating-filter"
                  name="radio"
                  value="all"
                  {{ request()->input('rating') == 'all' || request()->input('rating') == '' ? 'checked' : '' }}
                >
                <label for="ratingAll"><span class="circle"></span><span class="text">{{ __('Show All') }}</span></label>
              </div>
              <div class="single_radio">
                <input
                  type="radio"
                  id="rating5"
                  class="rating-filter"
                  name="radio"
                  value="5"
                  {{ request()->input('rating') == 5 ? 'checked' : '' }}
                >
                <label for="rating5"><span class="circle"></span><span class="text">{{ __('5 Star') }}</span></label>
              </div>
              <div class="single_radio">
                <input
                  type="radio"
                  id="rating4"
                  class="rating-filter"
                  name="radio"
                  value="4"
                  {{ request()->input('rating') == 4 ? 'checked' : '' }}
                >
                <label for="rating4"><span class="circle"></span><span class="text">{{ __('4 Star and higher') }}</span></label>
              </div>
              <div class="single_radio">
                <input
                  type="radio"
                  id="rating3"
                  class="rating-filter"
                  name="radio"
                  value="3"
                  {{ request()->input('rating') == 3 ? 'checked' : '' }}
                >
                <label for="rating3"><span class="circle"></span><span class="text">{{ __('3 Star and higher') }}</span></label>
              </div>
              <div class="single_radio">
                <input
                  type="radio"
                  id="rating2"
                  class="rating-filter"
                  name="radio"
                  value="2"
                  {{ request()->input('rating') == 2 ? 'checked' : '' }}
                >
                <label for="rating2"><span class="circle"></span><span class="text">{{ __('2 Star and higher') }}</span></label>
              </div>
              <div class="single_radio">
                <input
                  type="radio"
                  id="rating1"
                  class="rating-filter"
                  name="radio"
                  value=1
                  {{ request()->input('rating') == 1 ? 'checked' : '' }}
                >
                <label for="rating1"><span class="circle"></span><span class="text">{{ __('1 Star and higher') }}</span></label>
              </div>
            </div>
            @endif

            <div class="widget-box price-range-widget">
              <h4>{{ __('Filter By Price') }}</h4>
              <div id="slider-range"></div>
              <label for="amount">{{ __('Price') . ':' }}</label>
              <input
                type="text"
                id="amount"
              >
            </div>
          </div>
        </div>

        <div class="col-lg-8">
          <div class="course-filter mb-50">
            <div class="row">
              <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="form_group">
                  <select id="filterType">
                    <option selected disabled>{{ __('Filter') }}</option>
                    <option value="new" {{ request()->input('filterValue') == 'new' ? 'selected' : '' }}>{{ __('New Courses') }}</option>
                    <option value="old" {{ request()->input('filterValue') == 'old' ? 'selected' : '' }}>{{ __('Old Courses') }}</option>

                    @if ($bex->is_course_rating == 1)
                    <option value="high-to-low-rating" {{ request()->input('filterValue') == 'high-to-low-rating' ? 'selected' : '' }}>{{ __('High to Low Rating') }}</option>
                    @endif

                    <option value="high-to-low" {{ request()->input('filterValue') == 'high-to-low' ? 'selected' : '' }}>{{ __('High To Low Price') }}</option>
                    <option value="low-to-high" {{ request()->input('filterValue') == 'low-to-high' ? 'selected' : '' }}>{{ __('Low To High Price') }}</option>
                  </select>
                </div>
              </div>

              <div class="col-lg-9 col-md-6 col-sm-12">
                <div class="form_group search_group {{$rtl == 1 ? 'float-left' : ''}}">
                  <i
                    class="fas fa-search"
                    id="search-input-btn"
                  ></i>
                  <input
                    type="search"
                    class="form_control"
                    id="searchInput"
                    placeholder="{{ __('Search By Course Name') }}"
                    name="search"
                    value="{{ request()->input('search') ? request()->input('search') : '' }}"
                  >
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            @if (count($courses) == 0)
              <div class="col-md-12">
                <div class="py-5">
                  <h3 class="text-center">{{__('No Course Found!')}}</h3>
                </div>
              </div>
            @else
              @foreach ($courses as $course)
              <div class="col-md-6 col-sm-12">
                <div class="course-item">
                  <div class="course-img">
                    <img data-src="{{ asset('assets/front/img/courses/' . $course->course_image) }}" class="img-fluid lazy" alt="" >
                    <a href="{{ route('course_details', ['slug' => $course->slug]) }}" class="course-overlay">

                        @if ($bex->is_course_rating == 1)
                        <div class="rating">
                            <p><i class="fas fa-star"></i>{{round($course->review()->avg('rating'), 2)}} <span>({{$course->review()->count()}})</span></p>
                        </div>
                        @endif
                        <div class="categorie-box">
                            <a
                                href="{{route('courses', ['category_id' => $course->course_category_id])}}"
                                class="main-btn"
                            >{{ $course->courseCategory->name }}</a>
                        </div>
                    </a>
                  </div>
                  <div class="course-content">
                    <h3>
                      <a href="{{ route('course_details', ['slug' => $course->slug]) }}">
                        {{strlen($course->title) > 30 ? mb_substr($course->title, 0, 30, 'utf-8') . '...' : $course->title}}
                      </a>
                    </h3>
                    <div class="course-admin-price">
                      <div class="admin">
                        <img src="{{asset('assets/front/img/instructors/' . $course->instructor_image)}}" class="img-fluid" alt="">
                        <span><a>{{$course->instructor_name}}</a></span>
                      </div>
                      <div class="price">
                        @if (empty($course->current_price))
                          <span>{{ __('Free') }}</span>
                        @else
                            <span>{{ $bse->base_currency_symbol_position == 'left' ? $bse->base_currency_symbol : '' }} {{ $course->current_price }} {{ $bse->base_currency_symbol_position == 'right' ? $bse->base_currency_symbol : '' }}</span>
                            @if (!empty($course->previous_price))
                                <span class="pre-price"><del>{{ $bse->base_currency_symbol_position == 'left' ? $bse->base_currency_symbol : '' }} {{ $course->previous_price }} {{ $bse->base_currency_symbol_position == 'right' ? $bse->base_currency_symbol : '' }}</del></span>
                            @endif
                        @endif
                      </div>
                    </div>
                    <div class="course-meta">
                      <span><i class="fas fa-users"></i>{{$course->coursePurchase()->where('payment_status', 'Completed')->count()}} {{__('Students')}}</span>
                      <span><i class="far fa-clock"></i>{{ $course->duration }}</span>
                    </div>
                  </div>
                </div>
              </div>
              @endforeach
            @endif
          </div>

          <div class="row">
            <div class="col-md-12">
              <nav class="pagination-nav mb-3">
                {{ $courses->appends([
                  'search' => request()->input('search'),
                  'category_id' => request()->input('category_id'),
                  'checked_value' => request()->input('checked_value'),
                  'rating' => request()->input('rating'),
                  'minValue' => request()->input('minValue'),
                  'maxValue' => request()->input('maxValue'),
                  'filterValue' => request()->input('filterValue')
                ])->links() }}
              </nav>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  {{-- all courses end --}}

  {{-- search form --}}
  <form id="searchForm" class="d-none" action="{{ route('courses') }}" method="GET">
    <input
      type="hidden"
      id="searchKey"
      name="search"
      value="{{ !empty(request()->input('search')) ? request()->input('search') : '' }}"
    >
    <input
      type="hidden"
      id="categoryKey"
      name="category_id"
      value="{{ !empty(request()->input('category_id')) ? request()->input('category_id') : '' }}"
    >

    @if($bex->is_course_rating == 1)
    <input
      type="hidden"
      id="ratingKey"
      name="rating"
      value="{{ !empty(request()->input('rating')) ? request()->input('rating') : '' }}"
    >
    @endif

    <input
      type="hidden"
      id="checkedKey"
      name="checked_value"
      value="{{ !empty(request()->input('checked_value')) ? request()->input('checked_value') : '' }}"
    >
    <input
      type="hidden"
      id="minPriceId"
      name="minValue"
      value="{{ !empty(request()->input('minValue')) ? request()->input('minValue') : '' }}"
    >
    <input
      type="hidden"
      id="maxPriceId"
      name="maxValue"
      value="{{ !empty(request()->input('maxValue')) ? request()->input('maxValue') : '' }}"
    >
    <input
      type="hidden"
      id="typeId"
      name="filterValue"
      value="{{ !empty(request()->input('filterValue')) ? request()->input('filterValue') : '' }}"
    >
    <button type="submit" id="submitBtn"></button>
  </form>
  {{-- end of search form --}}

  @php
    $maxPrice = App\Course::max('current_price');
    $minPrice = App\Course::min('current_price');
  @endphp
@endsection

@section('scripts')
  <script src="{{ asset('assets/front/js/jquery.ui.js') }}"></script>
  <script src="{{ asset('assets/front/js/jquery.nice-select.min.js') }}"></script>

  <script>
    $(document).ready(function() {
      // jquery nice select js
      $('select').niceSelect();

      var position = '{{$bse->base_currency_symbol_position}}';
      var symbol = '{{$bse->base_currency_symbol}}';

      // slider range js
      $('#slider-range').slider({
        range: true,
        min: 0,
        max: {{$maxPrice}},
        values: [
          {{!empty(request()->input('minValue')) ? request()->input('minValue') : 0}},
          {{!empty(request()->input('maxValue')) ? request()->input('maxValue') : $maxPrice}}
        ],
        slide: function(event, ui) {
          //while sliding the price range, this function will show that value
          $('#amount').val((position == 'left' ? symbol : '') + ui.values[0] + (position == 'right' ? symbol : '') + ' - ' + (position == 'left' ? symbol : '') + ui.values[1] + (position == 'right' ? symbol : ''));
        }
      });

      // initially this is showing the price range value beside the 'Price' label
      $('#amount').val((position == 'left' ? symbol : '') + ' ' + $('#slider-range').slider('values', 0) + ' ' + (position == 'right' ? symbol : '') + ' - ' + (position == 'left' ? symbol : '') + ' ' + $('#slider-range').slider('values', 1) + ' ' + (position == 'right' ? symbol : ''));

      // function for input search
      $(document).on('keyup', '#searchInput', function(event) {
        if (event.keyCode === 13) {
            event.preventDefault();
            let searchVal = $('#searchInput').val();
            $('#searchKey').val(searchVal);
            $('#submitBtn').click();
        }
      });

      // function for select a option search
      $(document).on('click', '#categoryId', function() {
        let id;

        if($(this).attr('data-href') != 0) {
          id = $(this).attr('data-href');
        }

        $('#categoryKey').val(id);
        $('#submitBtn').click();
      });

      // function for checkbox search
      $(document).on('click', '.course-type', function() {
        let checkedVal = $('.course-type:checked').val();
        $('#checkedKey').val(checkedVal);
        $('#submitBtn').click();
      });

    // function for checkbox search
    $(document).on('click', '.rating-filter', function() {
        let checkedVal = $('.rating-filter:checked').val();
        $('#ratingKey').val(checkedVal);
        $('#submitBtn').click();
    });

      // function for slider search
      $(document).on('slidestop', '#slider-range', function() {
        let filterPrice = $('#amount').val();
        filterPrice = filterPrice.split('-');
        let min_price = parseInt(filterPrice[0].replace('$', ''));
        let max_price = parseInt(filterPrice[1].replace('$', ''));
        $('#minPriceId').val(min_price);
        $('#maxPriceId').val(max_price);
        $('#submitBtn').click();
      });

      // function for select a option (filter) search
      $(document).on('change', '#filterType', function() {
        let typeVal = $('#filterType').val();
        $('#typeId').val(typeVal);
        $('#submitBtn').click();
      });
    });
  </script>
@endsection
