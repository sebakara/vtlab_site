@extends('admin.layout')

@if(!empty($slider->language) && $slider->language->rtl == 1)
@section('styles')
<style>
    form input,
    form textarea,
    form select {
        direction: rtl;
    }
    .nicEdit-main {
        direction: rtl;
        text-align: right;
    }
</style>
@endsection
@endif

@section('content')
<div class="page-header">
    <h4 class="page-title">Edit Slider</h4>
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
            <a href="#">Hero Section</a>
        </li>
        <li class="separator">
            <i class="flaticon-right-arrow"></i>
        </li>
        <li class="nav-item">
            <a href="#">Edit Slider</a>
        </li>
    </ul>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="card-title d-inline-block">Edit Slider</div>
                <a class="btn btn-info btn-sm float-right d-inline-block" href="{{route('admin.slider.index') . '?language=' . request()->input('language')}}">
                    <span class="btn-label">
                        <i class="fas fa-backward" style="font-size: 12px;"></i>
                    </span>
                    Back
                </a>
            </div>
            <div class="card-body pt-5 pb-5">
                <div class="row">
                    <div class="col-lg-8 offset-lg-2">

                    <form id="ajaxForm" class="" action="{{route('admin.slider.update')}}" method="post">
                        @csrf
                        <input type="hidden" name="slider_id" value="{{$slider->id}}">

                        {{-- Image Part --}}
                        <div class="form-group">
                            <label for="">Image ** </label>
                            <br>
                            <div class="thumb-preview" id="thumbPreview1">
                                <img src="{{asset('assets/front/img/sliders/'.$slider->image)}}" alt="Slider Image">
                            </div>
                            <br>
                            <br>


                            <input id="fileInput1" type="hidden" name="image">
                            <button id="chooseImage1" class="choose-image btn btn-primary" type="button" data-multiple="false" data-toggle="modal" data-target="#lfmModal1">Choose Image</button>


                            <p class="text-warning mb-0">JPG, PNG, JPEG, SVG images are allowed</p>
                            <p class="em text-danger mb-0" id="errimage"></p>

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

                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="">Title </label>
                                    <input type="text" class="form-control" name="title" value="{{$slider->title}}" placeholder="Enter Title">
                                    <p id="errtitle" class="text-danger mb-0 em"></p>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="">Title Font Size **</label>
                                    <input type="number" class="form-control ltr" name="title_font_size" value="{{$slider->title_font_size}}">
                                    <p id="errtitle_font_size" class="em text-danger mb-0"></p>
                                </div>
                            </div>
                        </div>


                        @if ($be->theme_version == 'gym' || $be->theme_version == 'car' || $be->theme_version == 'cleaning')
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="">Bold Text </label>
                                    <input type="text" class="form-control" name="bold_text" value="{{$slider->bold_text}}" placeholder="Enter Bold Text">
                                    <p id="errbold_text" class="mb-0 text-danger em"></p>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="">Bold Text Font Size **</label>
                                    <input type="number" class="form-control ltr" name="bold_text_font_size" value="{{$slider->bold_text_font_size}}">
                                    <p id="errbold_text_font_size" class="em text-danger mb-0"></p>
                                </div>
                            </div>
                        </div>
                        @endif



                        @if ($be->theme_version == 'cleaning')
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label for="">Bold Text Color **</label>
                                    <input type="text" class="form-control jscolor" name="bold_text_color" value="{{$slider->bold_text_color}}">
                                    <p id="errbold_text_color" class="em text-danger mb-0"></p>
                                </div>
                            </div>
                        </div>
                        @endif


                        @if ($be->theme_version != 'cleaning')
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="">Text </label>
                                    <input type="text" class="form-control" name="text" value="{{$slider->text}}" placeholder="Enter Text">
                                    <p id="errtext" class="text-danger mb-0 em"></p>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="">Text Font Size **</label>
                                    <input type="number" class="form-control ltr" name="text_font_size" value="{{$slider->text_font_size}}">
                                    <p id="errtext_font_size" class="em text-danger mb-0"></p>
                                </div>
                            </div>
                        </div>
                        @endif


                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="">Button Text </label>
                                    <input type="text" class="form-control" name="button_text" value="{{$slider->button_text}}" placeholder="Enter Button Text">
                                    <p id="errbutton_text" class="text-danger mb-0 em"></p>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="">Button Text Font Size **</label>
                                    <input type="number" class="form-control ltr" name="button_text_font_size" value="{{$slider->button_text_font_size}}">
                                    <p id="errbutton_text_font_size" class="em text-danger mb-0"></p>
                                </div>
                            </div>
                        </div>


                        <div class="form-group">
                            <label for="">Button URL **</label>
                            <input type="text" class="form-control ltr" name="button_url" value="{{$slider->button_url}}" placeholder="Enter Button URL">
                            <p id="errbutton_url" class="text-danger mb-0 em"></p>
                        </div>
                        <div class="form-group">
                            <label for="">Serial Number **</label>
                            <input type="number" class="form-control ltr" name="serial_number" value="{{$slider->serial_number}}" placeholder="Enter Serial Number">
                            <p id="errserial_number" class="mb-0 text-danger em"></p>
                            <p class="text-warning"><small>The higher the serial number is, the later the slider will be shown.</small></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <div class="form">
                <div class="form-group from-show-notify row">
                    <div class="col-12 text-center">
                        <button type="submit" id="submitBtn" class="btn btn-success">Update</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
</div>

@endsection
