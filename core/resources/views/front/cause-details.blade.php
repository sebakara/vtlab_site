@extends("front.$version.layout")

@section('styles')
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
    }
  </style>
@endsection

@section('pagename')
  - {{ __('Cause') }} - {{ convertUtf8($cause->title) }}
@endsection

@section('meta-keywords', "$cause->meta_keywords")
@section('meta-description', "$cause->meta_description")

@section('breadcrumb-title', $bs->cause_details_title)
@section('breadcrumb-subtitle', strlen($cause->title) > 30 ? mb_substr($cause->title, 0, 30, 'utf-8') . '...' :
  $cause->title)
@section('breadcrumb-link', $bs->cause_details_title)

@section('content')
  <!--====== Start charity-causes Section ======-->
  <section class="single-causes-section">
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
        <div class="col-lg-8">
          <div class="causes-single-wrapper">
            <div class="causes-img">
              @if (!empty($cause->image))
                <img class="lazy" data-src="{{ asset('/assets/front/img/donations/' . $cause->image) }}"
                  alt="Cause Details">
              @endif
            </div>
            <div class="causes-content">
              <div class="single-progress-bar">
                <div class="progress-bar-inner" data-aos="fade-right"
                  style="width: {{ $cause->goal_percentage == 0 ? 2 : $cause->goal_percentage }}%">
                  <div class="progress-bar-style">{{ $cause->goal_percentage }}%</div>
                </div>
              </div>
              <div class="content-info pt-20">
                <h3><a>{{ convertUtf8($cause->title) }}</a></h3>
                <div class="causes-meta">
                  <p>
                    <span>{{ __('Goal') }}</span>-{{ $bex->base_currency_symbol_position == 'left' ? $bex->base_currency_symbol : '' }}{{ $cause->goal_amount }}{{ $bex->base_currency_symbol_position == 'right' ? $bex->base_currency_symbol : '' }}
                  </p>
                  <p>
                    <span>{{ __('Raised') }}</span>-
                    {{ $bex->base_currency_symbol_position == 'left' ? $bex->base_currency_symbol : '' }}{{ $cause->raised_amount }}{{ $bex->base_currency_symbol_position == 'right' ? $bex->base_currency_symbol : '' }}
                  </p>
                </div>
                <p>{!! replaceBaseUrl(convertUtf8($cause->content)) !!}</p>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="charity-sidebar">
            <div class="widget-box donation-box">
              <div class="donation-form">
                @if ($bex->donation_guest_checkout == 1 && !Auth::check())
                  <div class="alert alert-warning">
                    {{ __('You are now donating as a guest. If you want to login before donating, then please') }} <a
                      href="{{ route('user.login', ['redirected' => 'donation']) }}">{{ __('Click Here') }}</a>
                  </div>
                @endif
                <h4 class="widget-title">{{ __('Donation Form') }}</h4>
                <form action="{{ route('front.causes.payment') }}" method="post" enctype="multipart/form-data"
                  id="donationForm">
                  @csrf
                  <input type="hidden" name="donation_id" value="{{ $cause->id }}" />
                  <input type="hidden" name="donation_slug" value="{{ $cause->slug }}" />
                  <div id="donation-section">
                    <div class="form_group">
                      <input type="number" class="form_control amount_input" name="amount"
                        min="{{ $cause->min_amount }}" value="{{ $cause->min_amount }}" id="custom_amount">
                      <span>{{ $bex->base_currency_symbol }}</span>
                    </div>

                    @if (!empty($cause->custom_amount))
                      <ul>
                        @foreach ($custom_amounts as $custom_amount)
                          <li style="margin-bottom: 20px;"><a href="javascript:void(0)"
                              onclick="rmvdbimg({{ $cause->min_amount }},{{ $custom_amount }})">{{ $custom_amount }}</a>
                          </li>
                        @endforeach
                      </ul>
                    @endif

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
                    <div id="donation-info-section">
                      <input type="hidden" name="minimum_amount" value="{{ $cause->min_amount }}">
                      <input type="text" class="form_control" name="name" placeholder="{{ __('Enter your name') }}"
                        value="{{ $name }}">
                      <input type="email" class="form_control" name="email"
                        placeholder="{{ __('Enter your email address') }}" value="{{ $email }}">
                      <input type="text" class="form_control" name="phone"
                        placeholder="{{ __('Enter your phone') }}" value="{{ $phone }}">
                    </div>
                  </div>

                  <select class="form-control" name="payment_method" id="payment-gateway" style="margin-bottom: 20px;"
                    required>
                    <option value="0">{{ __('Choose an option') }}</option>
                    @foreach ($payment_gateways as $payment_gateway)
                      <option value="{{ $payment_gateway->name }}">{{ $payment_gateway->name }}
                      </option>
                    @endforeach
                  </select>

                  <div class="iyzico-element {{ old('payment_method') == 'Iyzico' ? '' : 'd-none' }}">
                    <input type="text" name="identity_number" class="form-control mb-3" placeholder="Identity Number"
                      value="{{ old('identity_number') }}">
                    @error('identity_number')
                      <p class="text-danger text-left">{{ $message }}</p>
                    @enderror

                    <input type="text" name="city" class="form-control mb-3" placeholder="City"
                      value="{{ old('city') }}">
                    @error('city')
                      <p class="text-danger text-left">{{ $message }}</p>
                    @enderror
                    <input type="text" name="country" class="form-control mb-3" placeholder="Country"
                      value="{{ old('country') }}">
                    @error('country')
                      <p class="text-danger text-left">{{ $message }}</p>
                    @enderror
                    <input type="text" name="address" class="form-control mb-3" placeholder="Address"
                      value="{{ old('address') }}">
                    @error('address')
                      <p class="text-danger text-left">{{ $message }}</p>
                    @enderror

                    <input type="text" name="zip_code" class="form-control mb-3" placeholder="Zip Code"
                      value="{{ old('zip_code') }}">
                    @error('zip_code')
                      <p class="text-danger text-left">{{ $message }}</p>
                    @enderror
                  </div>

                  <div id="paystack-section" style="display: none">
                    <input type="text" class="form_control" name="paystack_email"
                      placeholder="{{ __('Email Address') }}">
                  </div>
                  <div id="flutterwave-section" style="display: none">
                    <input type="text" class="form_control" name="flutterwave_email"
                      placeholder="{{ __('Email Address') }}">
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
                      placeholder="{{ 'Enter your address' }}">
                  </div>
                  <div id="instructions">

                  </div>
                  <input type="hidden" name="is_receipt" value="0" id="is_receipt">
                  <div class="anonymous_user">
                    <input type="checkbox" class="form_control" name="checkbox">
                    {{ __('Anonymous Donation') }}
                  </div>
                  <button type="submit" class="main-btn"
                    style="text-align: center; margin-top: 20px;">{{ __('Donate Now') }}
                  </button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
@endsection
@section('scripts')
  <script>
    let stripe_key = "{{ $stripe_key }}";
  </script>
  <script src="https://js.stripe.com/v3/"></script>
  <script src="{{ asset('assets/front/js/donation-checkout-stripe.js') }}"></script>
  <script>
    function rmvdbimg(min, amount) {
      $("#custom_amount").val(amount);
    }

    function infoSectionToggle() {
      let selectedPaymentMethod = $("#payment-gateway").children("option:selected").val();
      if ($('input[type="checkbox"]:checked').length > 0 && selectedPaymentMethod != 'PayUmoney') {
        $('#donation-info-section').fadeOut();
      } else {
        $('#donation-info-section').fadeIn(5);
      }
    }

    $(document).ready(function() {
      $('input[type="checkbox"]').click(function() {
        var selectedPaymentMethod = $("#payment-gateway").children("option:selected").val();

        if ($(this).prop("checked") == true) {
          if (selectedPaymentMethod != "PayUmoney" || selectedPaymentMethod == 'Iyzico') {
            $('#donation-info-section').fadeOut();
          }
          if (selectedPaymentMethod == "Paystack") {
            $('#stripe-section').fadeOut();
            $('#instructions').fadeOut();
            $('#razorpay-section').fadeOut();
            $('#flutterwave-section').fadeOut();
            $('#paystack-section').fadeIn(5);
          } else if (selectedPaymentMethod == "Flutterwave") {
            $('#stripe-section').fadeOut();
            $('#instructions').fadeOut();
            $('#razorpay-section').fadeOut();
            $('#paystack-section').fadeOut();
            $('#flutterwave-section').fadeIn(5);
          } else if (selectedPaymentMethod == "Iyzico") {
            $('#stripe-section').fadeOut();
            $('#instructions').fadeOut();
            $('#razorpay-section').fadeOut();
            $('#paystack-section').fadeOut();
            $('#flutterwave-section').fadeOut();
            $('#donation-info-section').fadeIn(5);
            $('.iyzico-element').removeClass('d-none');
          }
        } else if ($(this).prop("checked") == false) {
          $('#donation-info-section').fadeIn(5);
          $('#paystack-section').fadeOut();
          $('#flutterwave-section').fadeOut();
        }
      });
      $("#payment-gateway").change(function() {
        var selectedPaymentMethod = $(this).children("option:selected").val();
        let offline = {!! $offline !!};
        let data = [];
        offline.map(({
          id,
          name
        }) => {
          data.push(name);
        });
        $('#instructions').fadeOut();
        infoSectionToggle();
        if (selectedPaymentMethod == "Stripe") {
          $('.iyzico-element').addClass('d-none');
          $('#razorpay-section').fadeOut();
          $('#instructions').fadeOut();
          $('#paystack-section').fadeOut();
          $('#flutterwave-section').fadeOut();
          $('#stripe-section').fadeIn(5);
        } else if (selectedPaymentMethod == "Iyzico") {
          $('#stripe-section').fadeOut();
          $('#instructions').fadeOut();
          $('#paystack-section').fadeOut();
          $('#flutterwave-section').fadeOut();
          $('#razorpay-section').fadeOut();
          $('.iyzico-element').removeClass('d-none');
          $('#donation-info-section').fadeIn(5);
        } else if (selectedPaymentMethod == "Razorpay") {
          $('#stripe-section').fadeOut();
          $('#instructions').fadeOut();
          $('#paystack-section').fadeOut();
          $('#flutterwave-section').fadeOut();
          $('#razorpay-section').fadeIn(5);
          $('.iyzico-element').addClass('d-none');
        } else if (selectedPaymentMethod == "PayUmoney") {
          $('#stripe-section').fadeOut();
          $('#instructions').fadeOut();
          $('#paystack-section').fadeOut();
          $('#flutterwave-section').fadeOut();
          $('#razorpay-section').fadeOut(5);
          $('.iyzico-element').addClass('d-none');
        } else if (selectedPaymentMethod == "Paystack" && $("input[name='checkbox']:checked").length > 0) {
          $('#stripe-section').fadeOut();
          $('#instructions').fadeOut();
          $('#razorpay-section').fadeOut();
          $('#flutterwave-section').fadeOut();
          $('#paystack-section').fadeIn(5);
          $('.iyzico-element').addClass('d-none');
        } else if (selectedPaymentMethod == "Flutterwave" && $("input[name='checkbox']:checked").length > 0) {
          $('#stripe-section').fadeOut();
          $('#instructions').fadeOut();
          $('#razorpay-section').fadeOut();
          $('#paystack-section').fadeOut();
          $('#flutterwave-section').fadeIn(5);
          $('.iyzico-element').addClass('d-none');
        } else if (data.indexOf(selectedPaymentMethod) !== -1) {
          $('#stripe-section').fadeOut();
          $('#razorpay-section').fadeOut();
          $('#paystack-section').fadeOut();
          $('#flutterwave-section').fadeOut();
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
          $('#paystack-section').fadeOut();
          $('#flutterwave-section').fadeOut();
          $('#instructions').fadeOut();
          $('.iyzico-element').addClass('d-none');
        }
      });

    });
  </script>
@endsection
