@extends('admin.layout')

@if(!empty($abe->language) && $abe->language->rtl == 1)
@section('styles')
<style>
    form input,
    form textarea,
    form select,
    select {
        direction: rtl;
    }
    form .note-editor.note-frame .note-editing-area .note-editable {
        direction: rtl;
        text-align: right;
    }
</style>
@endsection
@endif

@section('content')
<div class="page-header">
    <h4 class="page-title">Package Background</h4>
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
            <a href="#">Home Page</a>
        </li>
        <li class="separator">
            <i class="flaticon-right-arrow"></i>
        </li>
        <li class="nav-item">
            <a href="#">Package Background</a>
        </li>
    </ul>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-lg-10">
                        <div class="card-title">Background Image</div>
                    </div>
                    <div class="col-lg-2">
                        @if (!empty($langs))
                        <select name="language" class="form-control" onchange="window.location='{{url()->current() . '?language='}}'+this.value">
                            <option value="" selected disabled>Select a Language</option>
                            @foreach ($langs as $lang)
                            <option value="{{$lang->code}}" {{$lang->code == request()->input('language') ? 'selected' : ''}}>{{$lang->name}}</option>
                            @endforeach
                        </select>
                        @endif
                    </div>
                </div>
            </div>
            <div class="card-body pt-5 pb-4">
                <div class="row">
                    <div class="col-lg-6 offset-lg-3 text-center">
                    <form id="backgroundForm" action="{{route('admin.package.background.upload', $lang_id)}}" method="POST">
                        @csrf
                        {{-- Image Part --}}
                        <div class="form-group">
                            <div class="thumb-preview" id="thumbPreview1">
                                <img src="{{asset('assets/front/img/'.$abe->package_background)}}" alt="Background Image">
                            </div>
                            <br>
                            <br>


                            <input id="fileInput1" type="hidden" name="background_image">
                            <button id="chooseImage1" class="choose-image btn btn-primary" type="button" data-multiple="false" data-toggle="modal" data-target="#lfmModal1">Choose Image</button>


                            <p class="text-warning mb-0">JPG, PNG, JPEG, SVG images are allowed</p>
                            @if ($errors->has('background_image'))
                            <p class="text-danger mb-0">{{$errors->first('background_image')}}</p>
                            @endif

                            <!-- Image LFM Modal -->
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
                    </form>
                </div>
            </div>
        </div>

        <div class="card-footer text-center">
            <div class="row">
                <div class="offset-lg-3 col-lg-6">
                    <button form="backgroundForm" class="btn btn-success" type="submit">Update</button>
                </div>
            </div>
        </div>

    </div>
</div>
</div>

@endsection
