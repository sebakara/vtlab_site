@extends('admin.layout')

@section('content')
<div class="page-header">
    <h4 class="page-title">Push Notification Settings</h4>
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
            <a href="#">Push Notification</a>
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
                <div class="card-title">Push Notification Settings</div>
            </div>
            <div class="card-body pt-5 pb-5">
                <div class="row">
                    <div class="col-lg-6 offset-lg-3">
                        <form id="pushSettingsForm" action="{{route('admin.pushnotification.updateSettings')}}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-lg-6">
                                    {{-- Icon Image --}}
                                    <div class="form-group">
                                        <label for="">Icon Image ** </label>
                                        <br>
                                        <div class="thumb-preview" id="thumbPreview1">
                                            <img src="{{asset('assets/front/img/'.$bex->push_notification_icon)}}" alt="Icon Image">
                                        </div>
                                        <br>
                                        <br>


                                        <input id="fileInput1" type="hidden" name="icon">
                                        <button id="chooseImage1" class="choose-image btn btn-primary" type="button" data-multiple="false" data-toggle="modal" data-target="#lfmModal1">Choose Image</button>


                                        <p class="text-warning mb-0">JPG, PNG, JPEG, SVG images are allowed</p>
                                        @if ($errors->has('icon'))
                                        <p class="text-danger mb-0">{{$errors->first('icon')}}</p>
                                        @endif

                                        <!-- icon LFM Modal -->
                                        <div class="modal fade lfm-modal" id="lfmModal1" tabindex="-1" role="dialog" aria-labelledby="lfmModalTitle" aria-hidden="true">
                                            <i class="fas fa-times-circle"></i>
                                            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-body p-0">
                                                        <iframe src="{{url('laravel-filemanager')}}?serial=1" style="width: 100%; height: 500px; overflow: hidden; border: none;"></iframe>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            @if (empty(env('VAPID_PUBLIC_KEY')))
                            <div class="form-group">
                                <label>VAPID Public Key</label>
                                <textarea class="form-control" rows="2" name="public_key" {{!empty(env('VAPID_PUBLIC_KEY')) ? 'disabled' : ''}}>{{env('VAPID_PUBLIC_KEY')}}</textarea>
                            </div>
                            @endif

                            @if (empty(env('VAPID_PRIVATE_KEY')))
                            <div class="form-group">
                                <label>VAPID Private Key</label>
                                <input type="text" class="form-control" name="private_key" value="{{env('VAPID_PRIVATE_KEY')}}">
                            </div>
                            @endif

                            @if (empty(env('VAPID_PUBLIC_KEY')) || empty(env('VAPID_PRIVATE_KEY')))
                            <div class="form-group">
                                <p class="mb-0">
                                    <a href="https://www.attheminute.com/vapid-key-generator/" target="_blank">Click Here</a> to generate & get VAPID Public Key &  VAPID Private Key.
                                </p>
                                <p class="text-warning">
                                    It will be generated one time. You wont be able to change it later.
                                </p>
                            </div>
                            @endif


                        </form>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <div class="form-group from-show-notify row">
                    <div class="col-12 text-center">
                        <button type="submit" class="btn btn-success" form="pushSettingsForm">Update</button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection
