@extends('admin.layout')

@section('content')
<div class="page-header">
    <h4 class="page-title">Logo</h4>
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
            <a href="#">Basic Settings</a>
        </li>
        <li class="separator">
            <i class="flaticon-right-arrow"></i>
        </li>
        <li class="nav-item">
            <a href="#">Logo</a>
        </li>
    </ul>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card-title">Update Logo</div>
                    </div>
                </div>
            </div>
            <div class="card-body pt-5 pb-4">
                <div class="row">
                    <div class="col-lg-6 offset-lg-3">
                        <form id="imageForm" action="{{route('admin.logo.update')}}" method="POST">
                            @csrf
                            {{-- Logo Part --}}
                            <div class="form-group">
                                <label for="">Logo ** </label>
                                <br>
                                <div class="thumb-preview" id="thumbPreview1">
                                    <img src="{{asset('assets/front/img/' . $abs->logo)}}" alt="Logo">
                                </div>
                                <br>
                                <br>


                                <input id="fileInput1" type="hidden" name="logo">
                                <button id="chooseImage1" class="choose-image btn btn-primary" type="button" data-multiple="false" data-toggle="modal" data-target="#lfmModal1">Choose Image</button>


                                <p class="text-warning mb-0">JPG, PNG, JPEG, SVG images are allowed</p>
                                @if ($errors->has('logo'))
                                <p class="text-danger mb-0">{{$errors->first('logo')}}</p>
                                @endif

                                <!-- Logo LFM Modal -->
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

                            {{-- Favicon Part --}}
                            <div class="form-group">
                                <label for="">Favicon ** </label>
                                <br>
                                <div class="thumb-preview" id="thumbPreview2">
                                    <img src="{{asset('assets/front/img/' . $abs->favicon)}}" alt="favicon">
                                </div>
                                <br>
                                <br>


                                <input id="fileInput2" type="hidden" name="favicon">
                                <button id="chooseImage2" class="choose-image btn btn-primary" type="button" data-multiple="false" data-toggle="modal" data-target="#lfmModal2">Choose Image</button>


                                <p class="text-warning mb-0">JPG, PNG, JPEG, SVG, SVG, SVG images are allowed</p>
                                @if ($errors->has('favicon'))
                                <p class="text-danger mb-0">{{$errors->first('favicon')}}</p>
                                @endif

                                <!-- favicon LFM Modal -->
                                <div class="modal fade lfm-modal" id="lfmModal2" tabindex="-1" role="dialog" aria-labelledby="lfmModalTitle" aria-hidden="true">
                                    <i class="fas fa-times-circle"></i>
                                    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                        <div class="modal-content">
                                            <div class="modal-body p-0">
                                                <iframe src="{{url('laravel-filemanager')}}?serial=2" style="width: 100%; height: 500px; overflow: hidden; border: none;"></iframe>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Breadcrumb Part --}}
                            <div class="form-group">
                                <label for="">Breadcrumb ** </label>
                                <br>
                                <div class="thumb-preview" id="thumbPreview3">
                                    <img src="{{asset('assets/front/img/' . $abs->breadcrumb)}}" alt="breadcrumb">
                                </div>
                                <br>
                                <br>


                                <input id="fileInput3" type="hidden" name="breadcrumb">
                                <button id="chooseImage3" class="choose-image btn btn-primary" type="button" data-multiple="false" data-toggle="modal" data-target="#lfmModal3">Choose Image</button>


                                <p class="text-warning mb-0">JPG, PNG, JPEG, SVG, SVG images are allowed</p>
                                @if ($errors->has('breadcrumb'))
                                <p class="text-danger mb-0">{{$errors->first('breadcrumb')}}</p>
                                @endif

                                <!-- breadcrumb LFM Modal -->
                                <div class="modal fade lfm-modal" id="lfmModal3" tabindex="-1" role="dialog" aria-labelledby="lfmModalTitle" aria-hidden="true">
                                    <i class="fas fa-times-circle"></i>
                                    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                        <div class="modal-content">
                                            <div class="modal-body p-0">
                                                <iframe src="{{url('laravel-filemanager')}}?serial=3" style="width: 100%; height: 500px; overflow: hidden; border: none;"></iframe>
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
                <button type="submit" class="btn btn-success" form="imageForm">Update</button>
            </div>
        </div>
    </div>
</div>



@endsection

