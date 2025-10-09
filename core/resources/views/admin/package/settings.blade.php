@extends('admin.layout')

@section('content')
<div class="page-header">
  <h4 class="page-title">Settings</h4>
  <ul class="breadcrumbs">
    <li class="nav-home">
      <a href="{{route('admin.dashboard')}}">
        <i class="flaticon-home"></i>
      </a>
    </li>
    <li class="separator">
      <i class="flaticon-right-arrow"></i>
    </li>
    <li class="nav-item">
      <a href="#">Package Management</a>
    </li>
    <li class="separator">
      <i class="flaticon-right-arrow"></i>
    </li>
    <li class="nav-item">
      <a href="#">Settings</a>
    </li>
  </ul>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <div class="card-title d-inline-block">Settings</div>

      </div>
      <div class="card-body pt-5 pb-5">
        <div class="row">
          <div class="col-lg-6 offset-lg-3">
            <form id="settingsForm" action="{{route('admin.package.settings')}}" method="POST">
              @csrf
              <div class="form-group">
                <label>Package Category**</label>
                <div class="selectgroup w-100">
                  <label class="selectgroup-item">
                    <input type="radio" name="package_category_status" value="1" class="selectgroup-input"
                      {{$abex->package_category_status == 1 ? 'checked' : ''}}>
                    <span class="selectgroup-button">Active</span>
                  </label>
                  <label class="selectgroup-item">
                    <input type="radio" name="package_category_status" value="0" class="selectgroup-input"
                      {{$abex->package_category_status == 0 ? 'checked' : ''}}>
                    <span class="selectgroup-button">Deactive</span>
                  </label>
                </div>
              </div>
              <div class="form-group">
                <label>Recurring Billing / Subscription **</label>
                <div class="selectgroup w-100">
                  <label class="selectgroup-item">
                    <input type="radio" name="recurring_billing" value="1" class="selectgroup-input"
                      {{$abex->recurring_billing == 1 ? 'checked' : ''}}>
                    <span class="selectgroup-button">Active</span>
                  </label>
                  <label class="selectgroup-item">
                    <input type="radio" name="recurring_billing" value="0" class="selectgroup-input"
                      {{$abex->recurring_billing == 0 ? 'checked' : ''}}>
                    <span class="selectgroup-button">Deactive</span>
                  </label>
                </div>
                <p class="text-warning mb-0">Recurring billing / subscription is only allowed for registered users.</p>
                <p class="text-warning mb-0">If you have enabled 'Recurring Billing', then Please turn of the 'Guest
                  Checkout'.</p>
              </div>
              <div id="recurringBilling" @if($abex->recurring_billing == 0) style="display:none;" @endif>
                <div class="form-group">
                  <label>Remind Before (Days) **</label>
                  <input type="number" name="expiration_reminder" class="form-control"
                    value="{{$abex->expiration_reminder}}">
                  <p class="text-warning mb-0">Specify how many days before you want to remind your customers about
                    subscription expiration. (via mail)</p>
                </div>
              </div>
              <div class="form-group">
                <label>Guest Checkout **</label>
                <div class="selectgroup w-100">
                  <label class="selectgroup-item">
                    <input type="radio" name="package_guest_checkout" value="1" class="selectgroup-input"
                      {{$abex->package_guest_checkout == 1 ? 'checked' : ''}}>
                    <span class="selectgroup-button">Active</span>
                  </label>
                  <label class="selectgroup-item">
                    <input type="radio" name="package_guest_checkout" value="0" class="selectgroup-input"
                      {{$abex->package_guest_checkout == 0 ? 'checked' : ''}}>
                    <span class="selectgroup-button">Deactive</span>
                  </label>
                </div>
                <p class="text-warning mb-0">If you enable 'guest checkout', then customers can checkout without login
                </p>
              </div>
            </form>
          </div>
        </div>
      </div>
      <div class="card-footer">
        <div class="form">
          <div class="form-group from-show-notify row">
            <div class="col-12 text-center">
              <button type="submit" form="settingsForm" class="btn btn-success">Update</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection


@section('scripts')
<script>
  $(document).ready(function() {
            $("input[name='recurring_billing']").on('change', function() {
                let rb = $(this).val();
                let $rb = $("#recurringBilling");
                if (rb == 1) {
                    $rb.show();
                } else {
                    $rb.hide();
                }
            })
        });
</script>
@endsection
