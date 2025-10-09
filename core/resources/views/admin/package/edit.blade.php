@extends('admin.layout')

@if(!empty($package->language) && $package->language->rtl == 1)
@section('styles')
<style>
  form input,
  form textarea,
  form select {
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
  <h4 class="page-title">Edit Package</h4>
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
      <a href="#">Package Page</a>
    </li>
    <li class="separator">
      <i class="flaticon-right-arrow"></i>
    </li>
    <li class="nav-item">
      <a href="#">Edit Package</a>
    </li>
  </ul>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <div class="card-title d-inline-block">Edit Package</div>
        <a class="btn btn-info btn-sm float-right d-inline-block"
          href="{{route('admin.package.index') . '?language=' . request()->input('language')}}">
          <span class="btn-label">
            <i class="fas fa-backward" style="font-size: 12px;"></i>
          </span>
          Back
        </a>
      </div>

      <div class="card-body pt-5 pb-5">
        <div class="row">
          <div class="col-lg-6 offset-lg-3">

            <form id="ajaxForm" class="modal-form" action="{{route('admin.package.update')}}" method="POST">
              @csrf
              <input type="hidden" name="package_id" value="{{$package->id}}">

              @if ($abe->theme_version == 'lawyer')

              {{-- Image Part --}}
              <div class="form-group">
                <label for="">Image ** </label>
                <br>
                <div class="thumb-preview" id="thumbPreview1">
                  <img src="{{asset('assets/front/img/packages/'.$package->image)}}" alt="User Image">
                </div>
                <br>
                <br>


                <input id="fileInput1" type="hidden" name="image">
                <button id="chooseImage1" class="choose-image btn btn-primary" type="button" data-multiple="false"
                  data-toggle="modal" data-target="#lfmModal1">Choose Image</button>


                <p class="text-warning mb-0">JPG, PNG, JPEG, SVG images are allowed</p>
                <p class="em text-danger mb-0" id="errimage"></p>

                <!-- Image LFM Modal -->
                <div class="modal fade lfm-modal" id="lfmModal1" tabindex="-1" role="dialog"
                  aria-labelledby="lfmModalTitle" aria-hidden="true">
                  <i class="fas fa-times-circle"></i>
                  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                    <div class="modal-content">
                      <div class="modal-body p-0">
                        <iframe src="{{url('laravel-filemanager')}}?serial=1"
                          style="width: 100%; height: 500px; overflow: hidden; border: none;"></iframe>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              @endif

              <div class="form-group {{ $categoryInfo->package_category_status == 0 ? 'd-none' : '' }}">
                <label for="">Category **</label>
                <select name="category_id" class="form-control">
                  <option disabled selected>Select a category</option>
                  @foreach ($categories as $category)
                    <option value="{{ $category->id }}" {{ $category->id == $package->category_id ? 'selected' : '' }}>{{ $category->name }}</option>
                  @endforeach
                </select>
                <p id="errcategory_id" class="mb-0 text-danger em"></p>
              </div>

              <div class="form-group">
                <label for="">Title **</label>
                <input type="text" class="form-control" name="title" placeholder="Enter title"
                  value="{{$package->title}}">
                <p id="errtitle" class="mb-0 text-danger em"></p>
              </div>

              @if ($bex->recurring_billing == 1)
              <div class="form-group">
                <label>Duration **</label>
                <div class="selectgroup w-100">
                  <label class="selectgroup-item">
                    <input type="radio" name="duration" value="monthly" class="selectgroup-input"
                      {{$package->duration == 'monthly' ? 'checked' : ''}}>
                    <span class="selectgroup-button">Monthly</span>
                  </label>
                  <label class="selectgroup-item">
                    <input type="radio" name="duration" value="yearly" class="selectgroup-input"
                      {{$package->duration == 'yearly' ? 'checked' : ''}}>
                    <span class="selectgroup-button">Yearly</span>
                  </label>
                </div>
                <p id="errduration" class="mb-0 text-danger em"></p>
              </div>
              @endif

              @if ($be->theme_version == 'cleaning')
              <div class="form-group">
                <label for="">Color **</label>
                <input id="incolor" type="text" class="form-control jscolor" name="color" placeholder="Enter color"
                  value="{{!empty($package->color) ? $package->color : 'e4e8f9'}}">
                <p id="eerrcolor" class="mb-0 text-danger em"></p>
              </div>
              @endif
              <div class="form-group">
                <label for="">Price (in {{$abx->base_currency_text}}) **</label>
                <input type="text" class="form-control" name="price" placeholder="Enter price"
                  value="{{$package->price}}">
                <p id="errprice" class="mb-0 text-danger em"></p>
              </div>

              <div class="form-group">
                <label for="">Description **</label>
                <textarea class="form-control summernote" name="description" rows="8" cols="80"
                  placeholder="Enter description" data-height="300">{{replaceBaseUrl($package->description)}}</textarea>
                <p id="errdescription" class="mb-0 text-danger em"></p>
              </div>

              @if ($bex->recurring_billing == 0)
              <div class="form-group">
                <label>Order Option **</label>
                <div class="selectgroup w-100">
                  <label class="selectgroup-item">
                    <input type="radio" name="order_status" value="1" class="selectgroup-input"
                      {{$package->order_status == 1 ? 'checked' : ''}} onchange="toggleLink({{$package->id}}, 1)">
                    <span class="selectgroup-button">Active</span>
                  </label>
                  <label class="selectgroup-item">
                    <input type="radio" name="order_status" value="0" class="selectgroup-input"
                      {{$package->order_status == 0 ? 'checked' : ''}} onchange="toggleLink({{$package->id}}, 0)">
                    <span class="selectgroup-button">Deactive</span>
                  </label>
                  <label class="selectgroup-item">
                    <input type="radio" name="order_status" value="2" class="selectgroup-input"
                      {{$package->order_status == 2 ? 'checked' : ''}} onchange="toggleLink({{$package->id}}, 2)">
                    <span class="selectgroup-button">Link</span>
                  </label>
                </div>
                <p id="errorder_status" class="mb-0 text-danger em"></p>
              </div>
              @endif

              <div class="form-group" id="externalLink{{$package->id}}" @if ($package->order_status != 2)
                style="display: none;" @endif>
                <label for="">External Link **</label>
                <input name="link" type="text" class="form-control" value="{{$package->link}}">
                <p id="errlink" class="mb-0 text-danger em"></p>
              </div>

              <div class="form-group">
                <label for="">Serial Number **</label>
                <input type="number" class="form-control ltr" name="serial_number" value="{{$package->serial_number}}"
                  placeholder="Enter Serial Number">
                <p id="errserial_number" class="mb-0 text-danger em"></p>
                <p class="text-warning"><small>The higher the serial number is, the later the package will be shown
                    everywhere.</small></p>
              </div>
              <div class="form-group">
                <label>Meta Keywords</label>
                <input class="form-control" name="meta_keywords" value="{{$package->meta_keywords}}"
                  placeholder="Enter meta keywords" data-role="tagsinput">
                <p id="errmeta_keywords" class="mb-0 text-danger em"></p>
              </div>
              <div class="form-group">
                <label>Meta Description</label>
                <textarea class="form-control" name="meta_description" rows="5"
                  placeholder="Enter meta description">{{$package->meta_description}}</textarea>
                <p id="errmeta_description" class="mb-0 text-danger em"></p>
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

@section('scripts')
<script>
  function toggleLink(pid, status) {
    if (status == 2) {
      $("#externalLink"+pid).show();
    } else {
      $("#externalLink"+pid).hide();
    }
  }
</script>
@endsection
