@extends("front.$version.layout")

@section('breadcrumb-subtitle', __('Success!'))
@section('breadcrumb-link', __('Success'))

@section('content')
<div class="checkout-message">
  <div class="container">
    <div class="row">
      <div class="col-lg-12">
        <div class="checkout-success">
          <div class="icon text-success"><i class="far fa-check-circle"></i></div>
          <h2>{{__('Success!')}}</h2>
          <p>{{__('You have successfully purchased this course.')}}</p>
          <p>{{__('We have sent you a mail with an invoice.')}}</p>
          <p class="mt-4">{{__('Thank You.')}}</p>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
