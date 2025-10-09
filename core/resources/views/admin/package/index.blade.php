@extends('admin.layout')

@section('content')
<div class="page-header">
  <h4 class="page-title">Packages</h4>
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
      <a href="#">Packages</a>
    </li>
  </ul>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col-lg-4">
            <div class="card-title d-inline-block">Packages</div>
          </div>
          <div class="col-lg-3">
            @if (!empty($langs))
            <select name="language" class="form-control"
              onchange="window.location='{{url()->current() . '?language='}}'+this.value">
              <option value="" selected disabled>Select a Language</option>
              @foreach ($langs as $lang)
              <option value="{{$lang->code}}" {{$lang->code == request()->input('language') ? 'selected' : ''}}>
                {{$lang->name}}</option>
              @endforeach
            </select>
            @endif
          </div>
          <div class="col-lg-4 offset-lg-1 mt-2 mt-lg-0">
            <a href="#" class="btn btn-primary float-lg-right float-left btn-sm" data-toggle="modal"
              data-target="#createModal"><i class="fas fa-plus"></i> Add Package</a>
            <button class="btn btn-danger float-right btn-sm mr-2 d-none bulk-delete"
              data-href="{{route('admin.package.bulk.delete')}}"><i class="flaticon-interface-5"></i> Delete</button>
          </div>
        </div>
      </div>

      <div class="card-body">
        <div class="row">
          <div class="col-lg-12">
            @if (count($packages) == 0)
            <h3 class="text-center">NO PACKAGE FOUND</h3>
            @else
            <div class="table-responsive">
              <table class="table table-striped mt-3" id="basic-datatables">
                <thead>
                  <tr>
                    <th scope="col">
                      <input type="checkbox" class="bulk-check" data-val="all">
                    </th>
                    <th scope="col">Title</th>
                    <th scope="col">Price ({{$bex->base_currency_text}})</th>
                    @if ($bex->recurring_billing == 1)
                    <th scope="col">Type</th>
                    @endif
                    <th scope="col">Details</th>
                    <th scope="col">Featured</th>
                    <th scope="col">Serial Number</th>
                    <th scope="col">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($packages as $key => $package)
                  <tr>
                    <td>
                      <input type="checkbox" class="bulk-check" data-val="{{$package->id}}">
                    </td>
                    <td>
                      {{strlen(convertUtf8($package->title)) > 30 ? convertUtf8(substr($package->title, 0, 30)) . '...' : convertUtf8($package->title)}}
                    </td>
                    <td>{{convertUtf8($package->price)}}</td>
                    @if ($bex->recurring_billing == 1)
                    <td class="text-capitalize">
                      {{$package->duration}}
                    </td>
                    @endif
                    <td>
                      <button class="btn btn-secondary btn-sm" data-toggle="modal"
                        data-target="#detailsModal{{$package->id}}"><i class="fas fa-eye"></i> View</button>
                    </td>
                    <td>
                      <form id="featureForm{{$package->id}}" class="d-inline-block"
                        action="{{route('admin.package.feature')}}" method="post">
                        @csrf
                        <input type="hidden" name="package_id" value="{{$package->id}}">
                        <select class="form-control {{$package->feature == 1 ? 'bg-success' : 'bg-danger'}}"
                          name="feature" onchange="document.getElementById('featureForm{{$package->id}}').submit();">
                          <option value="1" {{$package->feature == 1 ? 'selected' : ''}}>Yes</option>
                          <option value="0" {{$package->feature == 0 ? 'selected' : ''}}>No</option>
                        </select>
                      </form>
                    </td>
                    <td>{{$package->serial_number}}</td>
                    <td>
                      <a class="btn btn-secondary btn-sm"
                        href="{{route('admin.package.edit', $package->id) . '?language=' . request()->input('language')}}">
                        <span class="btn-label">
                          <i class="fas fa-edit"></i>
                        </span>
                        Edit
                      </a>

                      <form class="deleteform d-inline-block" action="{{route('admin.package.delete')}}" method="post">
                        @csrf
                        <input type="hidden" name="package_id" value="{{$package->id}}">
                        <button type="submit" class="btn btn-danger btn-sm deletebtn">
                          <span class="btn-label">
                            <i class="fas fa-trash"></i>
                          </span>
                          Delete
                        </button>
                      </form>
                    </td>
                  </tr>
                  <!-- Packages Modal -->
                  <div class="modal fade" id="detailsModal{{$package->id}}" tabindex="-1" role="dialog"
                    aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title" id="exampleModalLongTitle">Details</h5>
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                          </button>
                        </div>
                        <div class="modal-body">
                          {!! replaceBaseUrl(convertUtf8($package->description)) !!}
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                      </div>
                    </div>
                  </div>
                  @endforeach
                </tbody>
              </table>
            </div>
            @endif
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<!-- Create Package Modal -->
<div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
  aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Add Package</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">

        <form id="ajaxForm" class="modal-form" action="{{route('admin.package.store')}}" method="POST">
          @csrf
          @if ($be->theme_version == 'lawyer')
          {{-- Image Part --}}
          <div class="form-group">
            <label for="">Icon Image ** </label>
            <br>
            <div class="thumb-preview" id="thumbPreview1">
              <img src="{{asset('assets/admin/img/noimage.jpg')}}" alt="User Image">
            </div>
            <br>
            <br>


            <input id="fileInput1" type="hidden" name="image">
            <button id="chooseImage1" class="choose-image btn btn-primary" type="button" data-multiple="false"
              data-toggle="modal" data-target="#lfmModal1">Choose Image</button>


            <p class="text-warning mb-0">JPG, PNG, JPEG, SVG images are allowed</p>
            <p class="em text-danger mb-0" id="errimage"></p>

          </div>
          @endif
          <div class="form-group">
            <label for="">Language **</label>
            <select id="language" name="language_id" class="form-control">
              <option value="" selected disabled>Select a language</option>
              @foreach ($langs as $lang)
              <option value="{{$lang->id}}">{{$lang->name}}</option>
              @endforeach
            </select>
            <p id="errlanguage_id" class="mb-0 text-danger em"></p>
          </div>
          <div class="form-group {{ $categoryInfo->package_category_status == 0 ? 'd-none' : '' }}">
            <label for="">Category **</label>
            <select name="category_id" id="package_category_id" class="form-control" disabled>
              <option selected disabled>Select a category</option>
            </select>
            <p id="errcategory_id" class="mb-0 text-danger em"></p>
          </div>
          <div class="form-group">
            <label for="">Title **</label>
            <input type="text" class="form-control" name="title" placeholder="Enter title" value="">
            <p id="errtitle" class="mb-0 text-danger em"></p>
          </div>

          @if ($bex->recurring_billing == 1)
          <div class="form-group">
            <label>Duration **</label>
            <div class="selectgroup w-100">
              <label class="selectgroup-item">
                <input type="radio" name="duration" value="monthly" class="selectgroup-input" checked>
                <span class="selectgroup-button">Monthly</span>
              </label>
              <label class="selectgroup-item">
                <input type="radio" name="duration" value="yearly" class="selectgroup-input">
                <span class="selectgroup-button">Yearly</span>
              </label>
            </div>
            <p id="errduration" class="mb-0 text-danger em"></p>
          </div>
          @endif

          <div class="row">
            <div class="{{$be->theme_version == 'cleaning' ? 'col-lg-6' : 'col-lg-12'}}">
              <div class="form-group">
                <label for="">Price (in {{$abx->base_currency_text}}) **</label>
                <input type="text" class="form-control ltr" name="price" placeholder="Enter price" value="">
                <p id="errprice" class="mb-0 text-danger em"></p>
              </div>
            </div>
            @if ($be->theme_version == 'cleaning')
            <div class="col-lg-6">
              <div class="form-group">
                <label for="">Color **</label>
                <input type="text" class="form-control jscolor" name="color" placeholder="Enter color" value="e4e8f9">
                <p id="errcolor" class="mb-0 text-danger em"></p>
              </div>
            </div>
            @endif
          </div>

          <div class="form-group">
            <label for="">Description **</label>
            <textarea class="form-control summernote" name="description" rows="8" cols="80"
              placeholder="Enter description" data-height="300"></textarea>
            <p id="errdescription" class="mb-0 text-danger em"></p>
          </div>

          @if ($bex->recurring_billing == 0)
          <div class="form-group">
            <label>Order Option **</label>
            <div class="selectgroup w-100">
              <label class="selectgroup-item">
                <input type="radio" name="order_status" value="1" class="selectgroup-input" checked>
                <span class="selectgroup-button">Active</span>
              </label>
              <label class="selectgroup-item">
                <input type="radio" name="order_status" value="0" class="selectgroup-input">
                <span class="selectgroup-button">Deactive</span>
              </label>
              <label class="selectgroup-item">
                <input type="radio" name="order_status" value="2" class="selectgroup-input">
                <span class="selectgroup-button">Link</span>
              </label>
            </div>
            <p id="errorder_status" class="mb-0 text-danger em"></p>
          </div>
          @endif

          <div class="form-group" style="display: none;" id="externalLink">
            <label for="">External Link **</label>
            <input class="form-control" name="link" type="text" placeholder="External Link">
            <p id="errlink" class="mb-0 text-danger em"></p>
          </div>
          <div class="form-group">
            <label for="">Serial Number **</label>
            <input type="number" class="form-control ltr" name="serial_number" value=""
              placeholder="Enter Serial Number">
            <p id="errserial_number" class="mb-0 text-danger em"></p>
            <p class="text-warning"><small>The higher the serial number is, the later the package will be shown
                everywhere.</small></p>
          </div>
          <div class="form-group">
            <label>Meta Keywords</label>
            <input class="form-control" name="meta_keywords" value="" placeholder="Enter meta keywords"
              data-role="tagsinput">
            <p id="errmeta_keywords" class="mb-0 text-danger em"></p>
          </div>
          <div class="form-group">
            <label>Meta Description</label>
            <textarea class="form-control" name="meta_description" rows="5"
              placeholder="Enter meta description"></textarea>
            <p id="errmeta_description" class="mb-0 text-danger em"></p>
          </div>
        </form>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button id="submitBtn" type="button" class="btn btn-primary">Submit</button>
      </div>
    </div>
  </div>
</div>

<!-- Image LFM Modal -->
<div class="modal fade lfm-modal" id="lfmModal1" tabindex="-1" role="dialog" aria-labelledby="lfmModalTitle"
  aria-hidden="true">
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
@endsection

@section('scripts')
<script>
  $(document).ready(function() {
    $("select[name='language_id']").on('change', function() {
      $("#package_category_id").removeAttr('disabled');

      let langId = $(this).val();
      let url = "{{url('/')}}/admin/package/" + langId + "/get_categories";

      $.get(url, function(data) {
        let options = `<option value="" disabled selected>Select a category</option>`;

        if (data.length == 0) {
          options += `<option value="" disabled>${'No Category Exists'}</option>`;
        } else {
          for (let i = 0; i < data.length; i++) {
            options +=`<option value="${data[i].id}">${data[i].name}</option>`;
          }
        }

        $("#package_category_id").html(options);
      });
    });

    // make input fields RTL
    $("select[name='language_id']").on('change', function() {
      $(".request-loader").addClass("show");
      let url = "{{url('/')}}/admin/rtlcheck/" + $(this).val();
      // console.log(url);
      $.get(url, function(data) {
        $(".request-loader").removeClass("show");
        if (data == 1) {
          $("form.modal-form input").each(function() {
            if (!$(this).hasClass('ltr')) {
              $(this).addClass('rtl');
            }
          });
          $("form.modal-form select").each(function() {
            if (!$(this).hasClass('ltr')) {
              $(this).addClass('rtl');
            }
          });
          $("form.modal-form textarea").each(function() {
            if (!$(this).hasClass('ltr')) {
              $(this).addClass('rtl');
            }
          });
          $("form.modal-form .summernote").each(function() {
            $(this).siblings('.note-editor').find('.note-editable').addClass('rtl text-right');
          });
        } else {
          $("form.modal-form input, form.modal-form select, form.modal-form textarea").removeClass('rtl');
          $("form.modal-form .summernote").siblings('.note-editor').find('.note-editable').removeClass('rtl text-right');
        }
      });
    });

    $(document).on('change', 'input[name="order_status"]', function() {
      let status = $(this).val();

      if (status == 2) {
        $("#externalLink").show();
      } else {
        $("#externalLink").hide();
      }
    });
  });
</script>
@endsection
