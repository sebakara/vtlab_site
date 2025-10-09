{{-- Start: Paypal Area --}}
@if ($paypal->status == 1)
  <div class="option-block">
    <div class="radio-block">
      <div class="checkbox">
        <label>
          <input name="method" type="radio" class="input-check" value="paypal" data-tabid="paypal"
            data-action="{{ route('product.paypal.submit') }}">
          <span>{{ __('Paypal') }}</span>
        </label>
      </div>
    </div>
  </div>
@endif
{{-- End: Paypal Area --}}


{{-- Start: Stripe Area --}}
@if ($stripe->status == 1)
  <div class="option-block">
    <div class="checkbox">
      <label>
        <input name="method" class="input-check" type="radio" value="stripe" data-tabid="stripe"
          data-action="{{ route('product.stripe.submit') }}">
        <span>{{ __('Stripe') }}</span>
      </label>
    </div>
  </div>


  <div class="row gateway-details" id="tab-stripe">
    <div class="col-md-12">
      <div id="stripe-element" class="mb-2">
        <!-- A Stripe Element will be inserted here. -->
      </div>
      <!-- Used to display form errors -->
      <div id="stripe-errors" class="text-danger pb-2" role="alert"></div>
    </div>
  </div>
@endif
{{-- End: Stripe Area --}}



{{-- Start: Paystack Area --}}
@if ($paystackData->status == 1)
  <div class="option-block">
    <div class="radio-block">
      <div class="checkbox">
        <label>
          <input name="method" type="radio" class="input-check" value="paystack" data-tabid="paystack"
            data-action="{{ route('product.paystack.submit') }}">
          <span>{{ __('Paystack') }}</span>
        </label>
      </div>
    </div>
  </div>

  <div class="row gateway-details" id="tab-paystack">
    <input type="hidden" name="txnid" id="ref_id" value="">
    <input type="hidden" name="sub" id="sub" value="0">
    <input type="hidden" name="method" value="Paystack">
  </div>
@endif
{{-- End: Paystack Area --}}




{{-- Start: Flutterwave Area --}}
@if ($flutterwave->status == 1)
  <div class="option-block">
    <div class="radio-block">
      <div class="checkbox">
        <label>
          <input name="method" type="radio" class="input-check" value="flutterwave" data-tabid="flutterwave"
            data-action="{{ route('product.flutterwave.submit') }}">
          <span>{{ __('Flutterwave') }}</span>
        </label>
      </div>
    </div>
  </div>

  <div class="row gateway-details" id="tab-flutterwave">
    <input type="hidden" name="method" value="Flutterwave">
  </div>
@endif
{{-- End: Flutterwave Area --}}



{{-- Start: Razorpay Area --}}
@if ($razorpay->status == 1)
  <div class="option-block">
    <div class="radio-block">
      <div class="checkbox">
        <label>
          <input name="method" type="radio" class="input-check" value="razorpay" data-tabid="razorpay"
            data-action="{{ route('product.razorpay.submit') }}">
          <span>{{ __('Razorpay') }}</span>
        </label>
      </div>
    </div>
  </div>

  <div class="row gateway-details" id="tab-razorpay">
    <input type="hidden" name="method" value="Razorpay">
  </div>
@endif
{{-- End: Razorpay Area --}}



{{-- Start: Instamojo Area --}}
@if ($instamojo->status == 1)
  <div class="option-block">
    <div class="radio-block">
      <div class="checkbox">
        <label>
          <input name="method" type="radio" class="input-check" value="instamojo" data-tabid="instamojo"
            data-action="{{ route('product.instamojo.submit') }}">
          <span>{{ __('Instamojo') }}</span>
        </label>
      </div>
    </div>
  </div>

  <div class="row gateway-details" id="tab-instamojo">
    <input type="hidden" name="method" value="Instamojo">
  </div>
@endif
{{-- End: Instamojo Area --}}



{{-- Start: Paytm Area --}}
@if ($paytm->status == 1)
  <div class="option-block">
    <div class="radio-block">
      <div class="checkbox">
        <label>
          <input name="method" type="radio" class="input-check" value="paytm" data-tabid="paytm"
            data-action="{{ route('product.paytm.submit') }}">
          <span>{{ __('Paytm') }}</span>
        </label>
      </div>
    </div>
  </div>
@endif
{{-- End: Paytm Area --}}


{{-- Start: Mollie Payment Area --}}
@if ($mollie->status == 1)
  <div class="option-block">
    <div class="radio-block">
      <div class="checkbox">
        <label>
          <input name="method" type="radio" class="input-check" value="mollie" data-tabid="mollie"
            data-action="{{ route('product.mollie.submit') }}">
          <span>{{ __('Mollie Payment') }}</span>
        </label>
      </div>
    </div>
  </div>
@endif
{{-- End: Mollie Payment Area --}}




{{-- Start:Mercadopago Area --}}
@if ($mercadopago->status == 1)
  <div class="option-block">
    <div class="radio-block">
      <div class="checkbox">
        <label>
          <input name="method" type="radio" class="input-check" value="mercadopago" data-tabid="mercadopago"
            data-action="{{ route('product.mercadopago.submit') }}">
          <span>{{ __('Mercadopago') }}</span>
        </label>
      </div>
    </div>
  </div>
@endif
{{-- End:Mercadopago Area --}}

{{-- Start:Yoco Area --}}
@if ($yoco->status == 1)
  <div class="option-block">
    <div class="radio-block">
      <div class="checkbox">
        <label>
          <input name="method" type="radio" class="input-check" value="yoco" data-tabid="yoco"
            data-action="{{ route('product.yoco.submit') }}">
          <span>{{ __('Yoco') }}</span>
        </label>
      </div>
    </div>
  </div>
@endif
{{-- End:Yoco Area --}}

{{-- Start:Pefect Money Area --}}
@if ($perfect_money->status == 1)
  <div class="option-block">
    <div class="radio-block">
      <div class="checkbox">
        <label>
          <input name="method" type="radio" class="input-check" value="perfect_money" data-tabid="perfect_money"
            data-action="{{ route('product.perfect_money.submit') }}">
          <span>{{ __('Perfect Money') }}</span>
        </label>
      </div>
    </div>
  </div>
@endif
{{-- End:Pefect Money Area --}}

{{-- Start:xendit Area --}}
@if ($xendit->status == 1)
  <div class="option-block">
    <div class="radio-block">
      <div class="checkbox">
        <label>
          <input name="method" type="radio" class="input-check" value="xendit" data-tabid="xendit"
            data-action="{{ route('product.xendit.submit') }}">
          <span>{{ __('Xendit') }}</span>
        </label>
      </div>
    </div>
  </div>
@endif
{{-- End:xendit Area --}}

{{-- Start:toyyibpay Area --}}
@if ($toyyibpay->status == 1)
  <div class="option-block">
    <div class="radio-block">
      <div class="checkbox">
        <label>
          <input name="method" type="radio" class="input-check" value="toyyibpay" data-tabid="toyyibpay"
            data-action="{{ route('product.toyyibpay.submit') }}">
          <span>{{ __('Toyyibpay') }}</span>
        </label>
      </div>
    </div>
  </div>
@endif
{{-- End:toyyibpay Area --}}

{{-- Start:paytabs Area --}}
@if ($paytabs->status == 1)
  <div class="option-block">
    <div class="radio-block">
      <div class="checkbox">
        <label>
          <input name="method" type="radio" class="input-check" value="paytabs" data-tabid="paytabs"
            data-action="{{ route('product.paytabs.submit') }}">
          <span>{{ __('Paytabs') }}</span>
        </label>
      </div>
    </div>
  </div>
@endif
{{-- End:paytabs Area --}}

{{-- Start:midtrans Area --}}
@if ($midtrans->status == 1)
  <div class="option-block">
    <div class="radio-block">
      <div class="checkbox">
        <label>
          <input name="method" type="radio" class="input-check" value="midtrans" data-tabid="midtrans"
            data-action="{{ route('product.midtrans.submit') }}">
          <span>{{ __('Midtrans') }}</span>
        </label>
      </div>
    </div>
  </div>
@endif
{{-- End:midtrans Area --}}

{{-- Start:iyzico Area --}}
@if ($iyzico->status == 1)
  <div class="option-block">
    <div class="radio-block">
      <div class="checkbox">
        <label>
          <input name="method" type="radio" class="input-check" value="iyzico" data-tabid="iyzico"
            data-action="{{ route('product.iyzico.submit') }}">
          <span>{{ __('Iyzico') }}</span>
        </label>
      </div>
    </div>
  </div>
@endif
{{-- End:midtrans Area --}}

{{-- Start:phonepe Area --}}
@if ($phonepe->status == 1)
  <div class="option-block">
    <div class="radio-block">
      <div class="checkbox">
        <label>
          <input name="method" type="radio" class="input-check" value="phonepe" data-tabid="phonepe"
            data-action="{{ route('product.phonepe.submit') }}">
          <span>{{ __('PhonePe') }}</span>
        </label>
      </div>
    </div>
  </div>
@endif
{{-- End:midtrans Area --}}
{{-- Start:myfatoorah Area --}}
@if ($myfatoorah->status == 1)
  <div class="option-block">
    <div class="radio-block">
      <div class="checkbox">
        <label>
          <input name="method" type="radio" class="input-check" value="myfatoorah" data-tabid="myfatoorah"
            data-action="{{ route('product.myfatoorah.submit') }}">
          <span>{{ __('MyFatoorah') }}</span>
        </label>
      </div>
    </div>
  </div>
@endif
{{-- End:midtrans Area --}}




{{-- Start: Offline Gateways Area --}}
@foreach ($ogateways as $ogateway)
  <div class="option-block">
    <div class="checkbox">
      <label>
        <input name="method" class="input-check" type="radio" value="{{ $ogateway->id }}"
          data-tabid="{{ $ogateway->id }}" data-action="{{ route('product.offline.submit', $ogateway->id) }}">
        <span>{{ $ogateway->name }}</span>
      </label>
    </div>
  </div>

  <p class="gateway-desc">{{ $ogateway->short_description }}</p>

  <div class="gateway-details row" id="tab-{{ $ogateway->id }}">
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

<div class="row gateway-details {{ old('method') == 'iyzico' ? 'd-flex' : '' }}" id="tab-iyzico">

  <div class="col-md-6 mb-4">
    <div class="field-label">{{ __('Identy Number') }} *</div>
    <div class="field-input">
      <input type="text" class="card-elements" name="identity_number" placeholder="{{ __('Identity Number') }}"
        autocomplete="off" />
    </div>
    @error('identity_number')
      <p class="text-danger">{{ convertUtf8($message) }}</p>
    @enderror
  </div>
  <div class="col-md-6 mb-4">
    <div class="field-label">{{ __('Country') }} *</div>
    <div class="field-input">
      <input type="text" name="country" value="{{ old('country') }}" class="card-elements"
        placeholder="Country" autocomplete="off">
    </div>
    @error('country')
      <p class="text-danger">{{ convertUtf8($message) }}</p>
    @enderror
  </div>
  <div class="col-md-6 mb-4">
    <div class="field-label">{{ __('Zip Code') }} *</div>
    <div class="field-input">
      <input type="text" name="zip_code" value="{{ old('zip_code') }}" class="card-elements"
        placeholder="Zip Code">
    </div>
    @error('zip_code')
      <p class="text-danger">{{ convertUtf8($message) }}</p>
    @enderror
  </div>

</div>


@if ($errors->has('receipt'))
  <p class="text-danger mb-4">{{ $errors->first('receipt') }}</p>
@endif
{{-- End: Offline Gateways Area --}}



<input type="hidden" name="cmd" value="_xclick">
<input type="hidden" name="no_note" value="1">
<input type="hidden" name="lc" value="UK">
<input type="hidden" name="currency_code" value="USD">
<input type="hidden" name="ref_id" id="ref_id" value="">
<input type="hidden" name="bn" value="PP-BuyNowBF:btn_buynow_LG.gif:NonHostedGuest">
<input type="hidden" name="currency_sign" value="$">
