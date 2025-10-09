@extends("front.$version.layout")
@section('styles')
  <link rel="stylesheet" href="{{ asset('assets/front/css/nice-select.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/front/css/jquery.nice-number.min.css') }}">
  <style>
    input {
      margin-bottom: 10px;
    }

    .anonymous_user {
      font-size: 14px;
      text-align: center;
      margin-top: 20px;
    }

    .anonymous_user input {
      height: 14px;
      width: 14px;
      margin-right: 5px;
    }

    #stripe-section,
    #razorpay-section,
    #payumoney-section {
      margin-top: 10px;
    }

    .gateway-desc {
      background: #f1f1f1;
      font-size: 14px;
      padding: 10px 25px;
      margin-bottom: 20px;
      color: #212529;
      margin-top: 20px;
    }
  </style>
@endsection

@section('pagename')
  - {{ __('Event') }} - {{ convertUtf8($event->title) }}
@endsection

@section('meta-keywords', "$event->meta_keywords")
@section('meta-description', "$event->meta_description")

@section('breadcrumb-title', $bs->event_details_title)
@section('breadcrumb-subtitle', strlen($event->title) > 30 ? mb_substr($event->title, 0, 30, 'utf-8') . '...' :
  $event->title)
@section('breadcrumb-link', __('Event Details'))

@section('content')
  <!--====== Start Event details Section ======-->
  <section class="event-details-section pt-130 pb-140">
    <div class="container">
      @if ($errors->any())
        @foreach ($errors->all() as $error)
          <div class="alert alert-danger alert-block">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <strong>{{ $error }}</strong>
          </div>
        @endforeach
      @endif
      <div class="row">
        <div class="col-lg-6">
          @if ($event->image != 'null')
            <div class="event-big-slide mb-30">
              @foreach (json_decode($event->image) as $event_image)
                <div class="product-img">
                  <a href="{{ asset('/assets/front/img/events/sliders/' . $event_image) }}" class="image-popup">
                    <img src="{{ asset('/assets/front/img/events/sliders/' . $event_image) }}" class="img-fluid"
                      alt="" width="700" height="500">
                  </a>
                </div>
              @endforeach
            </div>
            <div class="event-thumb-slide">
              @foreach (json_decode($event->image) as $event_image)
                <div class="product-img">
                  <img src="{{ asset('/assets/front/img/events/sliders/' . $event_image) }}" class="img-fluid"
                    alt="">
                </div>
              @endforeach
            </div>
          @endif
        </div>
        <div class="col-lg-6">
          <div class="event-details-wrapper">
            <div class="event-content">
              <h3>{{ convertUtf8($event->title) }}</h3>
              <div class="event-meta mb-2">
                <span class="date"><i
                    class="far fa-calendar-alt"></i>{{ date_format(date_create($event->date), 'M d,Y') }}</span>
                @if (!empty($event->venue_location))
                  <span class="location"><i class="fas fa-map-marker-alt"></i>{{ $event->venue_location }}</span>
                @endif
              </div>
              <p class="price base-color">
                {{ $bex->base_currency_symbol_position == 'left' ? $bex->base_currency_symbol : '' }}{{ $event->cost }}{{ $bex->base_currency_symbol_position == 'right' ? $bex->base_currency_symbol : '' }}
                / {{ __('per ticket') }}</p>
              <div id="purchase-section" style="display: block">
                <div class="time-count">
                  <div id="simple_timer"></div>
                </div>
                <p>{!! replaceBaseUrl(convertUtf8($event->content)) !!}</p>
                <div class="info-box mb-15">
                  <a class="base-color"
                    href="{{ route('front.events', ['category' => $event->cat_id]) }}">{{ $event->eventCategories->name }}</a>
                  <p>{{ $event->venue_location }}</p>
                </div>

                @if ($event->available_tickets > 0)
                  <ul class="mb-20" style="display: flex">
                    <li><input type="number" id="tickets" min="1" max="{{ $event->available_tickets }}">
                    </li>
                    <li><input type="hidden" id="cost" value="{{ $event->cost }}"></li>
                    @if (!is_null($event->video))
                      <li><a href="{{ asset('/assets/front/img/events/videos/' . $event->video) }}" class="play_btn"><i
                            class="fas fa-play"></i></a></li>
                      <li>
                    @endif
                  </ul>
                  <a href="javascript:void(0)" class="main-btn" id="addToCart">{{ __('Add To Cart') }}</a>
                @else
                  <div>{{ __('No tickets are available') }}</div>
                @endif
              </div>
              @php
                if (Auth::check()) {
                    $name = Auth::user()->fname;
                    $email = Auth::user()->email;
                    $phone = Auth::user()->number;
                } else {
                    $name = '';
                    $email = '';
                    $phone = '';
                }
              @endphp

              <div id="invoice-section" style="display: none; justify-content: center; text-align: center">
                @if ($bex->event_guest_checkout == 1 && !Auth::check())
                  <div class="alert alert-warning">
                    {{ __('You are now purchasing ticket as a guest. If you want to login before purchasing, then please') }}
                    <a href="{{ route('user.login', ['redirected' => 'event']) }}">{{ __('Click Here') }}</a>
                  </div>
                @endif
                <form action="{{ route('front.event.payment') }}" method="POST" enctype="multipart/form-data"
                  id="eventForm">
                  @csrf
                  <hr>
                  <h4>{{ __('Invoice') }}</h4>
                  <hr>
                  <input type="hidden" name="event_id" value="{{ $event->id }}">
                  <input type="hidden" name="event_slug" value="{{ $event->slug }}">
                  <div>{{ __('No. Of Tickets') }}: <span id="quantity">5</span></div>
                  <input type="hidden" name="ticket_quantity" id="ticket-quantity" value="">
                  <div>{{ __('Per Ticket Cost') }}:
                    <span>{{ $bex->base_currency_symbol_position == 'left' ? $bex->base_currency_symbol : '' }}{{ $event->cost }}{{ $bex->base_currency_symbol_position == 'right' ? $bex->base_currency_symbol : '' }}
                    </span>
                  </div>
                  <input type="hidden" name="ticket_cost" id="ticket-cost" value="{{ $event->cost }}">
                  <div>{{ __('Total Cost') }}:
                    <span>{{ $bex->base_currency_symbol_position == 'left' ? $bex->base_currency_symbol : '' }}<span
                        id="total">100</span>{{ $bex->base_currency_symbol_position == 'right' ? $bex->base_currency_symbol : '' }}</span>
                  </div>
                  <input type="hidden" name="total_cost" id="total-cost" value="">
                  <br>
                  <div id="donation-info-section">
                    <input type="text" class="form_control" name="name" placeholder="{{ __('Enter your name') }}"
                      value="{{ $name }}">
                    <input type="email" class="form_control" name="email"
                      placeholder="{{ __('Enter your email address') }}" value="{{ $email }}">
                    <input type="text" class="form_control" name="phone" placeholder="{{ __('Enter your phone') }}"
                      value="{{ $phone }}">
                  </div>
                  <div class="form_group" style="display: flex; flex-direction: column; margin-top: 20px">
                    <select name="payment_method" id="payment-method">
                      <option value="0">{{ __('Choose an option') }}</option>
                      @foreach ($payment_gateways as $payment_gateway)
                        <option value="{{ $payment_gateway->name }}">{{ $payment_gateway->name }}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="iyzico-element d-none">
                    <input type="text" name="identity_number" class="form_control mt-3 mb-3" placeholder="Identity Number"
                      value="{{ old('identity_number') }}">
                    @error('identity_number')
                      <p class="text-danger text-left">{{ $message }}</p>
                    @enderror

                    <input type="text" name="city" class="form_control mb-3" placeholder="City"
                      value="{{ old('city') }}" >
                    @error('city')
                      <p class="text-danger text-left">{{ $message }}</p>
                    @enderror
                    <input type="text" name="country" class="form_control mb-3" placeholder="Country"
                      value="{{ old('country') }}">
                    @error('country')
                      <p class="text-danger text-left">{{ $message }}</p>
                    @enderror
                    <input type="text" name="address" class="form_control mb-3" placeholder="Address"
                      value="{{ old('address') }}">
                    @error('address')
                      <p class="text-danger text-left">{{ $message }}</p>
                    @enderror

                    <input type="text" name="zip_code" class="form_control mb-3" placeholder="Zip Code"
                      value="{{ old('zip_code') }}">
                    @error('zip_code')
                      <p class="text-danger text-left">{{ $message }}</p>
                    @enderror
                  </div>

                  <div id="stripe-section" style="display: none">
                    <div id="stripe-element">
                      <!-- A Stripe Element will be inserted here. -->
                    </div>
                    <!-- Used to display form errors -->
                    <div id="stripe-errors" class="pb-2 text-danger text-left" role="alert"></div>
                  </div>
                  <div id="razorpay-section" style="display: none">
                    <input type="text" class="form_control" name="razorpay_phone"
                      placeholder="{{ __('Enter your phone') }}">
                    <input type="text" class="form_control" name="razorpay_address"
                      placeholder="{{ __('Enter your address') }}">
                  </div>
                  <div id="payumoney-section" style="display: none">
                    <input type="text" class="form_control" name="payumoney_first_name"
                      placeholder="{{ __('First Name') }}">
                    <input type="text" class="form_control" name="payumoney_last_name"
                      placeholder="{{ __('Last Name') }}">
                    <input type="text" class="form_control" name="payumoney_phone"
                      placeholder="{{ __('Phone') }}">
                  </div>
                  <div id="instructions">

                  </div>
                  <input type="hidden" name="is_receipt" value="0" id="is_receipt">
                  <div class="form_group"
                    style="display: flex; flex-direction: row;justify-content: space-between; text-align: center;margin-top: 20px;">
                    <a href="javascript:void(0)" class="main-btn" id="cancel"
                      style="height: 45px;justify-content: center;text-align: center; padding-top:10px">{{ __('Cancel') }}</a>
                    <button class="main-btn" type="submit"
                      style="height: 45px;justify-content: center;text-align: center;padding-top: 0;padding-bottom: 0;">
                      {{ __('Confirm') }}
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-12">
          <div class="discription-area mt-80 mb-50">
            <div class="discription-tabs">
              <ul class="nav nav-tabs">
                <li class="nav-item">
                  <a class="nav-link active" data-toggle="tab" href="#description">{{ __('Details') }}</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" data-toggle="tab" href="#organizer">{{ __('Organizer') }}</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" data-toggle="tab" href="#vanue">{{ __('Venue') }}</a>
                </li>
              </ul>
            </div>
            <div class="tab-content">
              <div id="description" class="tab-pane active">
                <div class="event-content-box">
                  <div class="info">
                    <span>{{ __('Start') }}:</span>
                    <p>{{ date_format(date_create($event->date), 'M d,Y') }}
                      @ {{ date_format(date_create($event->time), 'h:i:sa') }}</p>
                  </div>
                  <div class="info">
                    <span>{{ __('Cost') }}:</span>
                    <p>
                      {{ $bex->base_currency_symbol_position == 'left' ? $bex->base_currency_symbol : '' }}{{ $event->cost }}{{ $bex->base_currency_symbol_position == 'right' ? $bex->base_currency_symbol : '' }}
                    </p>
                  </div>
                  <div class="info">
                    <span>{{ __('Event Categories') }}:</span>
                    <p>{{ convertUtf8($event->eventCategories->name) }}, {{ __('Event') }}</p>
                  </div>
                </div>
              </div>
              <div id="organizer" class="tab-pane fade">
                <div class="event-content-box">
                  <h4>{{ convertUtf8($event->organizer) }}</h4>
                  @if (!empty($event->organizer_email))
                    <div class="info">
                      <span>{{ __('Email') }}:</span>
                      <p><a href="{{ $event->organizer_email }}">{{ convertUtf8($event->organizer_email) }}</a></p>
                    </div>
                  @endif
                  @if (!empty($event->organizer_website))
                    <div class="info">
                      <span>{{ __('Website') }}:</span>
                      <p><a href="{{ $event->organizer_website }}">{{ convertUtf8($event->organizer_website) }}</a></p>
                    </div>
                  @endif
                </div>
              </div>
              <div id="vanue" class="tab-pane fade">
                <div class="event-content-box">
                  <h4>{{ convertUtf8($event->title) }}</h4>
                  <p>{{ convertUtf8($event->venue) }} <br>{{ convertUtf8($event->venue_location) }}</p>
                  @if (!empty($event->venue_location))
                    <div class="map-box">
                      <iframe id="gmap_canvas"
                        src="https://maps.google.com/maps?q={{ $event->venue_location }}&t=&z=13&ie=UTF8&iwloc=&output=embed"></iframe>
                    </div>
                  @endif
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      @if (count($moreEvents) > 0)
        <div class="row recent-event mb-30">
          <div class="col-lg-12">
            <h4 class="title">{{ __('MAYBE YOU LIKE') }}</h4>
            @foreach ($moreEvents as $moreEvent)
              <div class="event-item">
                <div class="event-img">
                  <img data-src="{{ asset('/assets/front/img/events/sliders/' . json_decode($moreEvent->image)[0]) }}"
                    class="img-fluid lazy" alt="">
                </div>
                <div class="event-content">
                  <h4><a
                      href="{{ route('front.event_details', [$moreEvent->slug]) }}">{{ convertUtf8($moreEvent->title) }}</a>
                  </h4>
                  <div class="post-meta mb-2">
                    @if (!empty($moreEvent->venue_location))
                      <span><a href="#">{{ convertUtf8($moreEvent->venue_location) }}</a></span>
                    @endif
                    <span>
                      <a href="#">{{ date_format(date_create($moreEvent->date), 'd M Y') }}</a>
                    </span>
                  </div>

                  <p class="price base-color">
                    {{ $bex->base_currency_symbol_position == 'left' ? $bex->base_currency_symbol : '' }}{{ $moreEvent->cost }}{{ $bex->base_currency_symbol_position == 'right' ? $bex->base_currency_symbol : '' }}
                  </p>
                </div>
              </div>
            @endforeach
          </div>
        </div>
      @endif
    </div>
  </section>
  <!--====== End Event details Section ======-->
@endsection
@section('scripts')
  <script>
    let stripe_key = "{{ $stripe_key }}";
  </script>
  <script src="https://js.stripe.com/v3/"></script>
  <script src="{{ asset('assets/front/js/event-checkout-stripe.js') }}"></script>

  <script src="{{ asset('/assets/front/js/jquery.nice-select.min.js') }}"></script>
  <script src="{{ asset('/assets/front/js/jquery.nice-number.min.js') }}"></script>
  <script src="{{ asset('/assets/front/js/jquery.easypiechart.min.js') }}"></script>
  {{-- <script src="{{asset('/assets/front/js/jquery.syotimer.min.js')}}"></script> --}}
  <script src="{{ asset('/assets/front/js/event.js') }}"></script>
  <script type="text/javascript">
    const d = new Date('{!! $event->date !!}');
    const ye = parseInt(new Intl.DateTimeFormat('en', {
      year: 'numeric'
    }).format(d));
    const mo = parseInt(new Intl.DateTimeFormat('en', {
      month: 'numeric'
    }).format(d));
    const da = parseInt(new Intl.DateTimeFormat('en', {
      day: '2-digit'
    }).format(d));
    const t = ' {!! $event->time !!}';
    const time = t.split(":");
    const hr = parseInt(time[0]);
    const min = parseInt(time[1]);
    $('#simple_timer').syotimer({
      year: ye,
      month: mo,
      day: da,
      hour: hr,
      minute: min,
    });
  </script>
  <script>
    $(document).ready(function() {
      $("#addToCart").on('click', function() {
        const quantity = $("#tickets").val();
        const cost = $("#cost").val();
        const total = quantity * cost;
        $("#quantity").html(`<span>${quantity}<span/>`);
        $("#ticket-quantity").val(quantity);
        $("#total").html(`<span>${total}<span/>`);
        $("#total-cost").val(total);
        $("#purchase-section").css('display', 'none');
        $("#invoice-section").css('display', 'block');
      })
      $("#cancel").on('click', function() {
        $("#purchase-section").css('display', 'block');
        $("#invoice-section").css('display', 'none');
      });
      $("#payment-method").change(function() {
        var selectedPaymentMethod = $(this).children("option:selected").val();
        let offline = {!! $offline !!};
        let data = [];
        offline.map(({
          id,
          name
        }) => {
          data.push(name);
        });
        if (selectedPaymentMethod == "Stripe") {
          $('#razorpay-section').fadeOut();
          $('#payumoney-section').fadeOut();
          $('#stripe-section').fadeIn(5);
          $('.iyzico-element').addClass('d-none');
        } else if (selectedPaymentMethod == "Iyzico") {
          $('#razorpay-section').fadeOut();
          $('#payumoney-section').fadeOut();
          $('#stripe-section').fadeOut();
          $('.iyzico-element').removeClass('d-none');
        } else if (selectedPaymentMethod == "Razorpay") {
          $('#stripe-section').fadeOut();
          $('#payumoney-section').fadeOut();
          $('#razorpay-section').fadeIn(5);
          $('.iyzico-element').addClass('d-none');
        } else if (selectedPaymentMethod == "PayUmoney") {
          $('#stripe-section').fadeOut();
          $('#razorpay-section').fadeOut();
          $('#payumoney-section').fadeIn(5);
          $('.iyzico-element').addClass('d-none');
        } else if (data.indexOf(selectedPaymentMethod) !== -1) {
          $('#stripe-section').fadeOut();
          $('#razorpay-section').fadeOut();
          $('#payumoney-section').fadeOut();
          $('.iyzico-element').addClass('d-none');
          //ajax call for instructions
          let name = selectedPaymentMethod;
          let formData = new FormData();
          formData.append('name', name);
          $.ajax({
            url: '{{ route('front.payment.instructions') }}',
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'POST',
            contentType: false,
            processData: false,
            cache: false,
            data: formData,
            success: function(data) {
              console.log(data);
              let instruction = $("#instructions");
              let instructions = `<div class="gateway-desc">${data.instructions}</div>`;
              let description = `<div class="gateway-desc"><p>${data.description}</p></div>`;
              let receipt = `<div class="form-element mb-2">
                                        <label>Receipt  <span>**</span> </label>
                                        <input type="file" name="receipt" value="" class="file-input">
                                        <p class="mb-0 text-warning">** Receipt image must be .jpg / .jpeg / .png</p>
                                    </div>`;
              if (data.is_receipt === 1) {
                $("#is_receipt").val(1);
                let finalInstruction = instructions + description + receipt;
                instruction.html(finalInstruction);
              } else {
                $("#is_receipt").val(0);
                let finalInstruction = instructions + description;
                instruction.html(finalInstruction);
              }
              $('#instructions').fadeIn();
            },
            error: function(data) {
              console.log(data);
            }
          })
        } else {
          $('#stripe-section').fadeOut();
          $('#razorpay-section').fadeOut();
          $('#payumoney-section').fadeOut();
          $('#instructions').fadeOut();
          $('.iyzico-element').addClass('d-none');
        }
      });
    });
  </script>
@endsection
