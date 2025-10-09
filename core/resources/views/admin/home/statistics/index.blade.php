@extends('admin.layout')

@if(!empty($selLang) && $selLang->rtl == 1)
@section('styles')
<style>
    select[name='language'] {
        direction: rtl;
    }
</style>
@endsection
@endif

@section('content')
  <div class="page-header">
    <h4 class="page-title">Statistics Section</h4>
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
        <a href="#">Home</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">Statistics Section</a>
      </li>
    </ul>
  </div>
  <div class="row">
    <div class="col-md-12">

      @if ($be->theme_version != 'car' && $bex->home_page_pagebuilder == 0)
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
            <div class="card-body">
                <div class="row">
                    <div class="offset-lg-3 col-lg-6 text-center">
                        <form id="backgroundForm" action="{{route('admin.statistics.upload', $lang_id)}}" method="POST">
                            @csrf
                            {{-- Image Part --}}
                            <div class="form-group">
                                <div class="thumb-preview" id="thumbPreview1">
                                    <img src="{{asset('assets/front/img/' . $abe->statistics_bg)}}" alt="Image">
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
                <button class="btn btn-success" type="submit" form="backgroundForm">Update</button>
            </div>
        </div>
      @endif

      <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-lg-6">
                    <div class="card-title d-inline-block">Statistics</div>
                </div>
                <div class="col-lg-6 mt-2 mt-lg-0">
                    <a href="#" class="btn btn-primary float-lg-right float-left" data-toggle="modal" data-target="#createStatisticModal"><i class="fas fa-plus"></i> Add Statistic</a>
                </div>
            </div>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-lg-12">
              @if (count($statistics) == 0)
                <h2 class="text-center">NO STATISTIC ADDED</h2>
              @else
                <div class="table-responsive">
                  <table class="table table-striped mt-3">
                    <thead>
                      <tr>
                        <th scope="col">#</th>
                        <th scope="col">Icon</th>
                        <th scope="col">Title</th>
                        <th scope="col">Quantity</th>
                        <th scope="col">Serial Number</th>
                        <th scope="col">Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($statistics as $key => $statistic)
                        <tr>
                          <td>{{$loop->iteration}}</td>
                          <td><i class="{{ $statistic->icon }}"></i></td>
                          <td>{{convertUtf8($statistic->title)}}</td>
                          <td>{{$statistic->quantity}}</td>
                          <td>{{$statistic->serial_number}}</td>
                          <td>
                            <a class="btn btn-secondary btn-sm" href="{{route('admin.statistics.edit', $statistic->id) . '?language=' . request()->input('language')}}">
                            <span class="btn-label">
                              <i class="fas fa-edit"></i>
                            </span>
                            Edit
                            </a>
                            <form class="d-inline-block deleteform" action="{{route('admin.statistics.delete')}}" method="post">
                              @csrf
                              <input type="hidden" name="statisticid" value="{{$statistic->id}}">
                              <button type="submit" class="btn btn-danger btn-sm deletebtn">
                                <span class="btn-label">
                                  <i class="fas fa-trash"></i>
                                </span>
                                Delete
                              </button>
                            </form>
                          </td>
                        </tr>
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

  {{-- Statistic Create Modal --}}
  @includeif('admin.home.statistics.create')
@endsection


@section('scripts')
  <script>
    $(document).ready(function() {
        $('.icp').on('iconpickerSelected', function(event){
            $("#inputIcon").val($(".iconpicker-component").find('i').attr('class'));
        });

        // make input fields RTL
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
                    $("form .input-group").each(function() {
                        if (!$(this).hasClass('ltr')) {
                            $(this).addClass('rtl');
                        }
                    });
                    $("form .nicEdit-main").each(function() {
                        $(this).addClass('rtl text-right');
                    });

                } else {
                    $("form input, form select, form textarea, form .input-group").removeClass('rtl');
                    $("form .nicEdit-main").removeClass('rtl text-right');
                }
            })
        });
    });
  </script>
@endsection
