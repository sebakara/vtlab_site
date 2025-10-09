@extends('admin.layout')

@section('content')
  <div class="page-header">
    <h4 class="page-title">Add Member</h4>
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
        <a href="#">Home Page</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">Add Member</a>
      </li>
    </ul>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <div class="card-title d-inline-block">Add Member</div>
          <a class="btn btn-info btn-sm float-right d-inline-block" href="{{route('admin.member.index') . '?language=' . request()->input('language')}}">
            <span class="btn-label">
              <i class="fas fa-backward" style="font-size: 12px;"></i>
            </span>
            Back
          </a>
        </div>
        <div class="card-body pt-5 pb-5">
          <div class="row">
            <div class="col-lg-6 offset-lg-3">

              <form id="ajaxForm" class="" action="{{route('admin.member.store')}}" method="POST">
                @csrf
                {{-- Image Part --}}
                <div class="form-group">
                    <label for="">Image ** </label>
                    <br>
                    <div class="thumb-preview" id="thumbPreview1">
                        <img src="{{asset('assets/admin/img/noimage.jpg')}}" alt="User Image">
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

                <div class="form-group">
                    <label for="">Language **</label>
                    <select name="language_id" class="form-control">
                        <option value="" selected disabled>Select a language</option>
                        @foreach ($langs as $lang)
                            <option value="{{$lang->id}}">{{$lang->name}}</option>
                        @endforeach
                    </select>
                    <p id="errlanguage_id" class="mb-0 text-danger em"></p>
                </div>
                <div class="form-group">
                  <label for="">Name **</label>
                  <input type="text" class="form-control" name="name" value="" placeholder="Enter name">
                  <p id="errname" class="mb-0 text-danger em"></p>
                </div>
                <div class="form-group">
                  <label for="">Rank **</label>
                  <input type="text" class="form-control" name="rank" value="" placeholder="Enter rank">
                  <p id="errrank" class="mb-0 text-danger em"></p>
                </div>
                <div class="form-group">
                  <label for="">Facebook</label>
                  <input type="text" class="form-control ltr" name="facebook" value="" placeholder="Enter facebook url">
                  <p id="errfacebook" class="mb-0 text-danger em"></p>
                </div>
                <div class="form-group">
                  <label for="">Twitter</label>
                  <input type="text" class="form-control ltr" name="twitter" value="" placeholder="Enter twitter url">
                  <p id="errtwitter" class="mb-0 text-danger em"></p>
                </div>
                <div class="form-group">
                  <label for="">Instagram</label>
                  <input type="text" class="form-control ltr" name="instagram" value="" placeholder="Enter instagram url">
                  <p id="errinstagram" class="mb-0 text-danger em"></p>
                </div>
                <div class="form-group">
                  <label for="">Linkedin</label>
                  <input type="text" class="form-control ltr" name="linkedin" value="" placeholder="Enter linkedin url">
                  <p id="errlinkedin" class="mb-0 text-danger em"></p>
                </div>
              </form>
            </div>
          </div>
        </div>
        <div class="card-footer">
          <div class="form">
            <div class="form-group from-show-notify row">
              <div class="col-12 text-center">
                <button type="submit" id="submitBtn" class="btn btn-success">Submit</button>
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
        $("select[name='language_id']").on('change', function() {
            $(".request-loader").addClass("show");
            let url = "{{url('/')}}/admin/rtlcheck/" + $(this).val();
            console.log(url);
            $.get(url, function(data) {
                $(".request-loader").removeClass("show");
                if (data == 1) {
                    $("form input").each(function() {
                        if (!$(this).hasClass('ltr')) {
                            $(this).addClass('rtl');
                        }
                    });
                    $("form select").each(function() {
                        if (!$(this).hasClass('ltr')) {
                            $(this).addClass('rtl');
                        }
                    });
                    $("form textarea").each(function() {
                        if (!$(this).hasClass('ltr')) {
                            $(this).addClass('rtl');
                        }
                    });
                } else {
                    $("form input, form select, form textarea").removeClass('rtl');
                }
            })
        });
    });
</script>
@endsection
