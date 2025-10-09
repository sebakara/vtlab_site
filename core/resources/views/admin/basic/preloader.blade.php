@extends('admin.layout')

@section('content')
  <div class="page-header">
    <h4 class="page-title">Preloader</h4>
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
        <a href="#">Preloader</a>
      </li>
    </ul>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <div class="card-title">Update Preloader</div>
        </div>
        <div class="card-body pt-5 pb-4">
          <div class="row">
            <div class="col-lg-6 offset-lg-3">
              <form  enctype="multipart/form-data" action="{{route('admin.preloader.update')}}" method="POST">
                @csrf
                <div class="row">
                  <div class="col-lg-12">
                    <div class="form-group">
                        <label>Status</label>
                        <div class="selectgroup w-100">
                           <label class="selectgroup-item">
                            <input type="radio" name="preloader_status" value="1" class="selectgroup-input" {{$bex->preloader_status == 1 ? 'checked' : ''}}>
                            <span class="selectgroup-button">Active</span>
                           </label>
                           <label class="selectgroup-item">
                            <input type="radio" name="preloader_status" value="0" class="selectgroup-input" {{$bex->preloader_status == 0 ? 'checked' : ''}}>
                            <span class="selectgroup-button">Deactive</span>
                           </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="">Preloader ** </label>
                        <br>
                        <div class="thumb-preview" id="thumbPreview1">
                            <img src="{{asset('assets/front/img/' . $bex->preloader)}}" alt="Preloader">
                        </div>
                        <br>
                        <br>


                        <input id="fileInput1" type="hidden" name="preloader">
                        <button id="chooseImage1" class="choose-image btn btn-primary" type="button" data-multiple="false" data-toggle="modal" data-target="#lfmModal1">Choose Image</button>


                        <p class="text-warning mb-0">JPG, PNG, JPEG, GIF, SVG images are allowed</p>
                        @if ($errors->has('preloader'))
                        <p class="text-danger mb-0">{{$errors->first('preloader')}}</p>
                        @endif

                        <!-- preloader LFM Modal -->
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

                <div class="card-footer">
                  <div class="form">
                    <div class="form-group from-show-notify row">
                      <div class="col-12 text-center">
                        <button type="submit" class="btn btn-success">Update</button>
                      </div>
                    </div>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

@endsection
