@extends("front.$version.layout")

@section('pagename')
  - {{__('Client Feedback')}}
@endsection

@section('styles')
  <link rel="stylesheet" href="{{ asset('assets/front/css/jquery-ui.min.css') }}">

  <link rel="stylesheet" href="{{ asset('assets/front/css/nice-select.css') }}">
@endsection

@section('breadcrumb-title', $bex->client_feedback_title)
@section('breadcrumb-subtitle', $bex->client_feedback_subtitle)
@section('breadcrumb-link', __('Client Feedback'))

@section('content')
  <section class="feedback-area-v1 pt-120 pb-120">
    <div class="container">
      <div class="row">

        <div class="col-lg-2"></div>
        <div class="col-lg-8">
          <div class="feedback-form">
            <form action="{{ route('store_feedback') }}" method="POST">
              @csrf
              <div class="row">
                <input type="hidden" id="ratingId" name="rating">

                <div class="col-lg-12">
                  <div class="form_group">
                    <div class="rating-box">
                      <ul class="feedback-rating rating-1">
                        <li>
                          <a class="cursor-pointer" data-ratingVal="1"><i class="far fa-star"></i></a>
                        </li>
                      </ul>
                      <ul class="feedback-rating rating-2">
                        <li>
                          <a class="cursor-pointer" data-ratingVal="2"><i class="far fa-star"></i></a>
                        </li>
                        <li>
                          <a class="cursor-pointer" data-ratingVal="2"><i class="far fa-star"></i></a>
                        </li>
                      </ul>
                      <ul class="feedback-rating rating-3">
                        <li>
                          <a class="cursor-pointer" data-ratingVal="3"><i class="far fa-star"></i></a>
                        </li>
                        <li>
                          <a class="cursor-pointer" data-ratingVal="3"><i class="far fa-star"></i></a>
                        </li>
                        <li>
                          <a class="cursor-pointer" data-ratingVal="3"><i class="far fa-star"></i></a>
                        </li>
                      </ul>
                      <ul class="feedback-rating rating-4">
                        <li>
                          <a class="cursor-pointer" data-ratingVal="4"><i class="far fa-star"></i></a>
                        </li>
                        <li>
                          <a class="cursor-pointer" data-ratingVal="4"><i class="far fa-star"></i></a>
                        </li>
                        <li>
                          <a class="cursor-pointer" data-ratingVal="4"><i class="far fa-star"></i></a>
                        </li>
                        <li>
                          <a class="cursor-pointer" data-ratingVal="4"><i class="far fa-star"></i></a>
                        </li>
                      </ul>
                      <ul class="feedback-rating rating-5">
                        <li>
                          <a class="cursor-pointer" data-ratingVal="5"><i class="far fa-star"></i></a>
                        </li>
                        <li>
                          <a class="cursor-pointer" data-ratingVal="5"><i class="far fa-star"></i></a>
                        </li>
                        <li>
                          <a class="cursor-pointer" data-ratingVal="5"><i class="far fa-star"></i></a>
                        </li>
                        <li>
                          <a class="cursor-pointer" data-ratingVal="5"><i class="far fa-star"></i></a>
                        </li>
                        <li>
                          <a class="cursor-pointer" data-ratingVal="5"><i class="far fa-star"></i></a>
                        </li>
                      </ul>
                    </div>
                  </div>
                  @if ($errors->has('rating'))
                    <p class="text-danger mb-2 text-left">{{ $errors->first('rating') }}</p>
                  @endif
                </div>

                <div class="col-lg-12">
                  <div class="form_group">
                    <input type="text" class="form_control" placeholder="{{__('Enter Name')}}" name="name">
                  </div>
                  @if ($errors->has('name'))
                    <p class="text-danger mb-2 text-left">{{ $errors->first('name') }}</p>
                  @endif
                </div>

                <div class="col-lg-12">
                  <div class="form_group">
                    <input type="email" class="form_control" placeholder="{{__('Email Address')}}" name="email">
                  </div>
                  @if ($errors->has('email'))
                    <p class="text-danger mb-2 text-left">{{ $errors->first('email') }}</p>
                  @endif
                </div>

                <div class="col-lg-12">
                  <div class="form_group">
                    <input type="text" class="form_control" placeholder="{{__('Subject')}}" name="subject">
                  </div>
                  @if ($errors->has('subject'))
                    <p class="text-danger mb-2 text-left">{{ $errors->first('subject') }}</p>
                  @endif
                </div>

                <div class="col-lg-12">
                  <textarea class="form_control pt-4" placeholder="{{__('Write Feedback')}}" name="feedback"></textarea>
                  @if ($errors->has('feedback'))
                    <p class="text-danger mb-2 text-left">{{ $errors->first('feedback') }}</p>
                  @endif
                </div>

                <div class="col-lg-12">
                  <div class="form_group text-center">
                    <button class="main-btn btn base-bg">{{ __('Submit') }}</button>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>
@endsection

@section('scripts')
  <script src="{{ asset('assets/front/js/jquery.ui.js') }}"></script>

  <script src="{{ asset('assets/front/js/jquery.nice-select.min.js') }}"></script>

  <script>
    $(document).ready(function () {
      // jquery nice select js
      $('select').niceSelect();
    });

    // get the rating (star) value in integer
    $(document).on('click', '.feedback-rating li a', function() {
      let ratingValue = $(this).attr('data-ratingVal');

      // first, remove star color from all the 'feedback-rating' class
      $('.feedback-rating li a i').removeClass('text-success');

      // second, add star color to the selected parent class
      let parentClass = `rating-${ratingValue}`;
      $('.' + parentClass + ' li a i').addClass('text-success');

      $('#ratingId').val(ratingValue);
    });
  </script>
@endsection
