@extends('admin.layout')

@section('content')
  <div class="page-header">
    <h4 class="page-title">Settings</h4>
    <ul class="breadcrumbs">
      <li class="nav-home">
        <a href="#">
          <i class="flaticon-home"></i>
        </a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">Gallery</a>
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
              <form id="settingsForm" action="{{route('admin.gallery.update_settings')}}" method="post">
                @csrf
                <div class="form-group">
                  <label>Gallery Category**</label>
                  <div class="selectgroup w-100">
                    <label class="selectgroup-item">
                      <input type="radio" name="gallery_category_status" value="1" class="selectgroup-input" {{$abex->gallery_category_status == 1 ? 'checked' : ''}}>
                      <span class="selectgroup-button">Active</span>
                    </label>
                    <label class="selectgroup-item">
                      <input type="radio" name="gallery_category_status" value="0" class="selectgroup-input" {{$abex->gallery_category_status == 0 ? 'checked' : ''}}>
                      <span class="selectgroup-button">Deactive</span>
                    </label>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>

        <div class="card-footer">
          <div class="form">
            <div class="form-group from-show-notify row">
              <div class="col-12 text-center">
                <button type="submit" form="settingsForm" class="btn btn-success">
                  Submit
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
