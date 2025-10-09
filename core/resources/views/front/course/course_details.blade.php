@extends("front.$version.layout")

@section('pagename')
  - {{ __('Course') }} - {{ convertUtf8($course_details->title) }}
@endsection

@section('styles')
  <link rel="stylesheet" href="{{ asset('assets/front/css/magnific-popup.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/front/css/nice-select.css') }}">
@endsection

@section('breadcrumb-title', $bex->course_details_title)
@section('breadcrumb-subtitle', $course_details->title)
@section('breadcrumb-link', $course_details->title)

@section('content')
  {{-- course details start --}}
  <section class="course-details-section pt-120 pb-120">
    <div class="container">
      @if ($errors->any())
        @foreach ($errors->all() as $error)
          <div class="alert alert-danger alert-block">
            <strong>{{ $error }}</strong>
            <button type="button" class="close" data-dismiss="alert">×</button>
          </div>
        @endforeach
      @endif
      <div class="row">
        <div class="col-lg-6">
          <div class="courses-img-box">
            <img data-src="{{ asset('assets/front/img/courses/' . $course_details->course_image) }}"
              class="lazy img-fluid" alt="">
            <div class="video-box">
              <a href="{{ $course_details->video_link }}" class="video-popup"><i class="fas fa-play"></i></a>
            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="course-content">
            <h3>{{ convertUtf8($course_details->title) }}</h3>


            @if ($bex->is_course_rating == 1)
              <div class="rate mb-3 mt-3">
                <div class="rating" style="width: {{ $course_details->review()->avg('rating') * 20 }}%"></div>
              </div>
            @endif


            <p class="price">
              @if (empty($course_details->current_price))
                <span class="off-price">{{ __('Free') }}</span>
              @else
                <span
                  class="off-price">{{ $bse->base_currency_symbol_position == 'left' ? $bse->base_currency_symbol : '' }}
                  {{ $course_details->current_price }}
                  {{ $bse->base_currency_symbol_position == 'right' ? $bse->base_currency_symbol : '' }}</span>
                @if (!empty($course_details->previous_price))
                  <span
                    class="main-price">{{ $bse->base_currency_symbol_position == 'left' ? $bse->base_currency_symbol : '' }}
                    {{ $course_details->previous_price }}
                    {{ $bse->base_currency_symbol_position == 'right' ? $bse->base_currency_symbol : '' }}</span>
                @endif
              @endif
            </p>
            <p>{{ $course_details->summary }}</p>
            <ul class="info">
              <li>{{ __('Category') . ':' }}<span><a
                    href="{{ route('courses', ['categroy_id' => $course_details->course_category_id]) }}">{{ $course_details->courseCategory->name }}</a></span>
              </li>
            </ul>
            <form method="POST" id="paymentGatewayForm" enctype="multipart/form-data">
              {{-- this form using POST method for safety --}}
              @csrf
              <input type="hidden" name="course_id" value="{{ $course_details->id }}">
              <div class="d-flex flex-row">


                @auth
                  @php
                    $pcourse = Auth::user()
                        ->courseOrder()
                        ->where('course_id', $course_details->id)
                        ->where('payment_status', 'completed');
                  @endphp
                  @if (Auth::check() && $pcourse->count() > 0)
                    <strong class="text-danger mb-3 mr-4">{{ __('Already Purchased') }}</strong><br>
                    <a class="text-primary"
                      href="{{ route('user.course.lessons', $pcourse->first()->id) }}">{{ __('Go to this Course') }}</a>
                  @else
                    <div
                      class="form_group {{ $rtl == 1 ? 'ml-3' : 'mr-3' }} {{ $course_details->current_price == null ? 'd-none' : '' }}">
                      <select name="gateway" id="paymentType" class="select-payment">
                        <option selected disabled>{{ __('Pay Via') }}</option>
                        @foreach ($paymentGateways as $paymentGateway)
                          <option value="{{ $paymentGateway->keyword }}">{{ $paymentGateway->name }}</option>
                        @endforeach
                        @foreach ($offlineGateways as $offlineGateway)
                          <option value="{{ $offlineGateway->id }}" data-type="offline">{{ $offlineGateway->name }}
                          </option>
                        @endforeach
                      </select>
                    </div>
                    <div class="form-group button mb-20">
                      <button type="submit" id="enrollBtn"
                        class="main-btn {{ $course_details->current_price == null ? 'd-none' : '' }}">{{ __('Enroll Now') }}</button>
                    </div>
                    <div class="form-group button mb-20">
                      <button type="submit" id="enrollBtn__free"
                        class="main-btn {{ $course_details->current_price != null ? 'd-none' : '' }}">{{ __('Enroll Now') }}</button>
                    </div>
                  @endif
                @endauth
                @guest
                  <div
                    class="form_group {{ $rtl == 1 ? 'ml-3' : 'mr-3' }} {{ $course_details->current_price == null ? 'd-none' : '' }}">
                    <select name="gateway" id="paymentType" class="select-payment">
                      <option selected disabled>{{ __('Pay Via') }}</option>
                      @foreach ($paymentGateways as $paymentGateway)
                        <option value="{{ $paymentGateway->keyword }}">{{ $paymentGateway->name }}</option>
                      @endforeach
                      @foreach ($offlineGateways as $offlineGateway)
                        <option value="{{ $offlineGateway->id }}" data-type="offline">{{ $offlineGateway->name }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="form-group button mb-20">
                    <button type="submit" id="enrollBtn"
                      class="main-btn {{ $course_details->current_price == null ? 'd-none' : '' }}">{{ __('Enroll Now') }}</button>
                  </div>
                  <div class="form-group button mb-20">
                    <button type="submit" id="enrollBtn__free"
                      class="main-btn {{ $course_details->current_price != null ? 'd-none' : '' }}">{{ __('Enroll Now') }}</button>
                  </div>
                @endguest
              </div>
              <div class="row mt-3 d-none" id="stripeTab">
                <div class="col-md-12 mb-3">
                  <div id="stripe-element">
                    <!-- A Stripe Element will be inserted here. -->
                  </div>
                  <!-- Used to display form errors -->
                  <div id="stripe-errors" class="pb-2 text-danger text-left" role="alert"></div>
                </div>
              </div>

              <div class="row mt-3 d-none" id="payumoneyTab">
                <div class="col-md-6 mb-3">
                  <div class="field-label mb-2">{{ __('First Name') }}*</div>
                  <div class="field-input mb-2">
                    <input type="text" name="payumoney_first_name" placeholder="{{ __('Enter First Name') }}" />
                  </div>
                  @if ($errors->has('payumoney_first_name'))
                    <p class="mb-0 text-danger">{{ $errors->first('payumoney_first_name') }}</p>
                  @endif
                </div>
                <div class="col-md-6 mb-3">
                  <div class="field-label mb-2">{{ __('Last Name') }}*</div>
                  <div class="field-input mb-2">
                    <input type="text" name="payumoney_last_name" placeholder="{{ __('Enter Last Name') }}" />
                  </div>
                  @if ($errors->has('payumoney_last_name'))
                    <p class="mb-0 text-danger">{{ $errors->first('payumoney_last_name') }}</p>
                  @endif
                </div>
                <div class="col-md-6 mb-3">
                  <div class="field-label mb-2">{{ __('Phone Number') }}*</div>
                  <div class="field-input mb-2">
                    <input type="text" name="payumoney_phone" placeholder="{{ __('Enter Phone Number') }}" />
                  </div>
                  @if ($errors->has('payumoney_phone'))
                    <p class="mb-0 text-danger">{{ $errors->first('payumoney_phone') }}</p>
                  @endif
                </div>
              </div>

              <div class="row mt-3 d-none" id="iyzicoTab">
                <div class="col-md-6 mb-3">
                  <div class="field-label mb-2">{{ __('Identity Number') }}*</div>
                  <div class="field-input mb-2">
                    <input type="text" class="card-elements" name="identity_number"
                      placeholder="{{ __('Enter Identity Number') }}" autocomplete="off"
                      value="{{ old('identity_number') }}" />
                  </div>
                  @error('identity_number')
                    <p class="text-danger">{{ $message }}</p>
                  @enderror
                </div>
                <div class="col-md-6 mb-3">
                  <div class="field-label mb-2">{{ __('Zip Code') }}*</div>
                  <div class="field-input mb-2">
                    <input type="text" class="card-elements" placeholder="{{ __('Enter Zip Code') }}"
                      name="zip_code" value="{{ old('zip_code') }}">
                  </div>
                  @error('zip_code')
                    <p class="text-danger">{{ $message }}</p>
                  @enderror
                </div>
              </div>



              @foreach ($offlineGateways as $ogateway)
                <div class="gateway-details row" id="tab-{{ $ogateway->id }}" style="display: none;">
                  <div class="col-12">
                    <p class="gateway-desc">{{ $ogateway->short_description }}</p>
                  </div>
                  <div class="col-12">
                    <div class="gateway-instruction">
                      {!! replaceBaseUrl($ogateway->instructions) !!}
                    </div>
                  </div>

                  @if ($ogateway->is_receipt == 1)
                    <div class="col-12 mb-4">
                      <label for="" class="d-block">{{ __('Receipt') }} **</label>
                      <input type="file" name="receipt">
                      <p class="mb-0 text-warning">** {{ __('Receipt image must be .jpg / .jpeg / .png') }}</p>
                    </div>
                  @endif
                </div>
              @endforeach

              <p class="text-danger payment-warning" style="display: none;">*Please select a payment method</p>
            </form>
            <ul class="social-link">
              <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
              <li><a href="#"><i class="fab fa-twitter"></i></a></li>
              <li><a href="#"><i class="fab fa-pinterest-p"></i></a></li>
              <li><a href="#"><i class="fab fa-linkedin-in"></i></a></li>
            </ul>
          </div>
        </div>
      </div>
      <div class="row mt-80">
        <div class="col-lg-12">
          <div class="discription-area">
            <div class="discription-tabs">
              <ul class="nav nav-tabs">
                <li class="nav-item">
                  <a class="nav-link active" data-toggle="tab" href="#overview">{{ __('Overview') }}</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" data-toggle="tab" href="#outline">{{ __('Outline') }}</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" data-toggle="tab" href="#instructor">{{ __('Instructor') }}</a>
                </li>

                @if ($bex->is_course_rating == 1)
                  <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#reviews">{{ __('Reviews') }}</a>
                  </li>
                @endif
              </ul>
            </div>
            <div class="tab-content">
              <div id="overview" class="tab-pane active">
                <div class="content-box">
                  <h4>{{ __('Overview') }}</h4>
                  <p>{!! $course_details->overview !!}</p>
                </div>
              </div>
              <div id="outline" class="tab-pane fade">
                <div class="content-box">
                  <div class="accordion" id="accordionExample">
                    @foreach ($modules as $module)
                      <div class="card">
                        <a class="card-header collapsed" href="#" id="headingone" data-toggle="collapse"
                          data-target="{{ '#collapse' . $module->id }}"
                          aria-expanded="{{ $loop->first ? 'true' : 'false' }}"
                          aria-controls="collapseone">{{ $module->name }}<span class="toggle_btn"></span>
                        </a>
                        <div id="{{ 'collapse' . $module->id }}"
                          class="{{ $loop->first ? 'collapse show' : 'collapse' }}" aria-labelledby="headingOne"
                          data-parent="#accordionExample" style="">
                          <div class="card-body">
                            @php
                              $lessons = App\Lesson::where('module_id', $module->id)->get();
                            @endphp
                            <ul>
                              @foreach ($lessons as $lesson)
                                <li><a><i class="fas fa-play"></i> {{ $lesson->name }} <span
                                      class="duration">{{ $lesson->duration }}</span></a></li>
                              @endforeach
                            </ul>
                          </div>
                        </div>
                      </div>
                    @endforeach
                  </div>
                </div>
              </div>
              <div id="instructor" class="tab-pane fade">
                <div class="instructor-wrap">
                  <div class="thumb">
                    <img data-src="{{ asset('assets/front/img/instructors/' . $course_details->instructor_image) }}"
                      class="img-fluid lazy" alt="">
                  </div>
                  <div class="content">
                    <h4>{{ $course_details->instructor_name }}</h4>
                    <span>{{ $course_details->instructor_occupation }}</span>
                    <div class="text-box">
                      <p>{{ $course_details->instructor_details }}</p>
                    </div>
                    <ul class="social-link">
                      @if (!empty($course_details->instructor_facebook))
                        <li><a href="{{ $course_details->instructor_facebook }}" target="_blank"><i
                              class="fab fa-facebook-f"></i></a></li>
                      @endif
                      @if (!empty($course_details->instructor_twitter))
                        <li><a href="{{ $course_details->instructor_twitter }}" target="_blank"><i
                              class="fab fa-twitter"></i></a></li>
                      @endif
                      @if (!empty($course_details->instructor_instagram))
                        <li><a href="{{ $course_details->instructor_instagram }}" target="_blank"><i
                              class="fab fa-instagram"></i></a></li>
                      @endif
                      @if (!empty($course_details->instructor_linkedin))
                        <li><a href="{{ $course_details->instructor_linkedin }}" target="_blank"><i
                              class="fab fa-linkedin-in"></i></a></li>
                      @endif
                    </ul>
                  </div>
                </div>
              </div>

              @if ($bex->is_course_rating == 1)
                <div id="reviews" class="tab-pane fade">
                  <div class="shop-review-area">
                    @php $numOfReview = count($reviews); @endphp
                    @if ($numOfReview == 0)
                      <h4 class="title">{{ __('This course has no review yet') }}</h4>
                    @else
                      <h4 class="title">
                        {{ $numOfReview == 1 ? '1' . ' ' . __('Review for this course') : $numOfReview . ' ' . __('Reviews for this course') }}
                      </h4>
                      <div class="mb-5">
                        @foreach ($reviews as $review)
                          <div class="review_user">
                            @if (strpos($review->reviewByUser->photo, 'facebook') !== false || strpos($review->reviewByUser->photo, 'google'))
                              <img class="lazy"
                                data-src="{{ $review->reviewByUser->photo ? $review->reviewByUser->photo : asset('assets/front/img/user/profile.jpg') }}"
                                alt="user image">
                            @else
                              <img class="lazy"
                                data-src="{{ $review->reviewByUser->photo ? asset('assets/front/img/user/' . $review->reviewByUser->photo) : '' }}"
                                alt="user image">
                            @endif
                            <div class="rate">
                              <div class="rating" style="width: {{ $review->rating * 20 }}%"></div>
                            </div>
                            @if ($rtl == 1)
                              <span><span>{{ $review->created_at->format('M d, Y') }}</span>
                                {{ '- ' . $review->reviewByUser->fname . ' ' . $review->reviewByUser->lname }}</span>
                            @else
                              <span><span>{{ $review->reviewByUser->fname . ' ' . $review->reviewByUser->lname }}</span>
                                {{ '- ' . $review->created_at->format('M d, Y') }}</span>
                            @endif
                            <p>{{ $review->comment }}</p>
                          </div>
                        @endforeach
                      </div>
                    @endif
                    @if (Auth::user())
                      <div class="review_form pt-4">
                        <form action="{{ route('course.review') }}" method="POST">
                          @csrf
                          <input type="hidden" name="course_id" value="{{ $course_details->id }}">
                          <div class="form_group">
                            <label>{{ __('Comment') }}</label>
                            <textarea class="form_control mt-2" name="comment" placeholder="{{ __('Write something about this course') }}"></textarea>
                          </div>
                          <input type="hidden" id="ratingId" name="rating">
                          <div class="form_group">
                            <label>{{ __('Rating') . '*' }}</label>
                            <div class="review-content mt-2">
                              <ul class="review-value review-1">
                                <li>
                                  <a class="cursor-pointer" data-ratingVal="1"><i class="far fa-star"></i></a>
                                </li>
                              </ul>
                              <ul class="review-value review-2">
                                <li>
                                  <a class="cursor-pointer" data-ratingVal="2"><i class="far fa-star"></i></a>
                                </li>
                                <li>
                                  <a class="cursor-pointer" data-ratingVal="2"><i class="far fa-star"></i></a>
                                </li>
                              </ul>
                              <ul class="review-value review-3">
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
                              <ul class="review-value review-4">
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
                              <ul class="review-value review-5">
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
                          <div class="form_button">
                            <button type="submit" class="main-btn mt-2">{{ __('Submit') }}</button>
                          </div>
                        </form>
                      </div>
                    @endif
                  </div>
                </div>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  {{-- course details end --}}
@endsection
@section('scripts')
  <script src="{{ asset('assets/front/js/jquery.magnific-popup.min.js') }}"></script>
  <script src="{{ asset('assets/front/js/jquery.nice-select.min.js') }}"></script>
  <script>
    let stripe_key = "{{ $stripe_key }}";
  </script>
  <script src="https://js.stripe.com/v3/"></script>
  <script src="{{ asset('assets/front/js/course-checkout-stripe.js') }}"></script>
  <script>
    $(document).ready(function() {
      // Magnific Popup
      $('.video-popup').magnificPopup({
        type: 'iframe',
        removalDelay: 300,
        mainClass: 'mfp-fade'
      });

      // jquery nice select js
      $('select').niceSelect();

      // change form action for various payment gateways
      $(document).on('click', '#enrollBtn', function() {
        let paymentGateway = $('#paymentType').val();
        let type = $('#paymentType').find("option:selected").data('type');
        //    console.log(paymentGateway, type);

        if (paymentGateway == null) {
          event.preventDefault();
          $('.payment-warning').fadeIn().delay(1500).fadeOut();
        }

        if (type == 'offline') {
          $('#paymentGatewayForm').attr('action', "{{ url('/') }}" + "/course/offline/" + paymentGateway +
            "/submit");
        } else {
          if (paymentGateway == 'paypal') {
            $('#paymentGatewayForm').attr('action', "{{ route('course.payment.paypal') }}");
          } else if (paymentGateway == 'paytm') {
            $('#paymentGatewayForm').attr('action', "{{ route('course.payment.paytm') }}");
          } else if (paymentGateway == 'razorpay') {
            $('#paymentGatewayForm').attr('action', "{{ route('course.payment.razorpay') }}");
          } else if (paymentGateway == 'stripe') {
            $('#paymentGatewayForm').attr('action', "{{ route('course.payment.stripe') }}");
          } else if (paymentGateway == 'instamojo') {
            $('#paymentGatewayForm').attr('action', "{{ route('course.payment.instamojo') }}");
          } else if (paymentGateway == 'mollie') {
            $('#paymentGatewayForm').attr('action', "{{ route('course.payment.mollie') }}");
          } else if (paymentGateway == 'flutterwave') {
            $('#paymentGatewayForm').attr('action', "{{ route('course.payment.flutterwave') }}");
          } else if (paymentGateway == 'mercadopago') {
            $('#paymentGatewayForm').attr('action', "{{ route('course.payment.mercadopago') }}");
          } else if (paymentGateway == 'paystack') {
            $('#paymentGatewayForm').attr('action', "{{ route('course.payment.paystack') }}");
          } else if (paymentGateway == 'payumoney') {
            $('#paymentGatewayForm').attr('action', "{{ route('course.payment.payumoney') }}");
          } else if (paymentGateway == 'yoco') {
            $('#paymentGatewayForm').attr('action', "{{ route('course.payment.yoco') }}");
          } else if (paymentGateway == 'perfect_money') {
            $('#paymentGatewayForm').attr('action', "{{ route('course.payment.perfect_money') }}");
          } else if (paymentGateway == 'xendit') {
            $('#paymentGatewayForm').attr('action', "{{ route('course.payment.xendit') }}");
          } else if (paymentGateway == 'toyyibpay') {
            $('#paymentGatewayForm').attr('action', "{{ route('course.payment.toyyibpay') }}");
          } else if (paymentGateway == 'paytabs') {
            $('#paymentGatewayForm').attr('action', "{{ route('course.payment.paytabs') }}");
          } else if (paymentGateway == 'midtrans') {
            $('#paymentGatewayForm').attr('action', "{{ route('course.payment.midtrans') }}");
          } else if (paymentGateway == 'iyzico') {
            $('#paymentGatewayForm').attr('action', "{{ route('course.payment.iyzico') }}");
          } else if (paymentGateway == 'phonepe') {
            $('#paymentGatewayForm').attr('action', "{{ route('course.payment.phonepe') }}");
          } else if (paymentGateway == 'myfatoorah') {
            $('#paymentGatewayForm').attr('action', "{{ route('course.payment.myfatoorah') }}");
          }
        }

      });

      // enroll free course by clicking the 'enroll now' button
      $(document).on('click', '#enrollBtn__free', function() {
        $('#paymentGatewayForm').attr('action', "{{ route('free_course.enroll') }}");
      });

      // show the input form for the stripe payment
      $(document).on('change', '#paymentType', function() {
        let gatewayName = $(this).val();
        let type = $(this).find('option:checked').data('type');

        if (gatewayName == 'stripe') {
          $('#stripeTab').removeClass('d-none');
        } else {
          $('#stripeTab').addClass('d-none');
        }

        if (gatewayName == 'iyzico') {
          $('#iyzicoTab').removeClass('d-none');
        } else {
          $('#iyzicoTab').addClass('d-none');
        }

        if (gatewayName == 'payumoney') {
          $('#payumoneyTab').removeClass('d-none');
          $('#payumoneyTab input').removeAttr('disabled');
        } else {
          $('#payumoneyTab input').attr('disabled', true);
          $('#payumoneyTab').addClass('d-none');
        }

        if (type == 'offline') {
          $(".gateway-details").hide();
          $("#tab-" + gatewayName).show();

          $(".gateway-details").attr('disabled', true);
          $("#tab-" + gatewayName + " input").removeAttr('disabled');
        } else {
          $(".gateway-details").hide();
          $(".gateway-details").attr('disabled', true);
        }
      });

      // get the rating (star) value in integer
      $(document).on('click', '.review-value li a', function() {
        let ratingValue = $(this).attr('data-ratingVal');

        // first, remove star color from all the 'review-value' class
        $('.review-value li a i').removeClass('text-warning');

        // second, add star color to the selected parent class
        let parentClass = `review-${ratingValue}`;
        $('.' + parentClass + ' li a i').addClass('text-warning');

        $('#ratingId').val(ratingValue);
      });
    });
  </script>
@endsection
