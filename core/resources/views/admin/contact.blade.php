@extends('admin.layout')

@php
$selLang = \App\Language::where('code', request()->input('language'))->first();
@endphp
@if(!empty($selLang) && $selLang->rtl == 1)
@section('styles')
<style>
    form:not(.modal-form) input,
    form:not(.modal-form) textarea,
    form:not(.modal-form) select,
    select[name='language'] {
        direction: rtl;
    }
    form:not(.modal-form) .note-editor.note-frame .note-editing-area .note-editable {
        direction: rtl;
        text-align: right;
    }
</style>
@endsection
@endif

@section('content')
  <div class="page-header">
    <h4 class="page-title">Contact Page</h4>
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
        <a href="#">Contact Page</a>
      </li>
    </ul>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <form class="mb-3 dm-uploader drag-and-drop-zone" enctype="multipart/form-data" action="{{route('admin.contact.update', $lang_id)}}" method="POST">
            <div class="card-header">
                <div class="row">
                    <div class="col-lg-10">
                        <div class="card-title">Contact Page</div>
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
          <div class="card-body pt-5 pb-5">
            <div class="row">
              <div class="col-lg-6 offset-lg-3">
                @csrf
                <div class="form-group">
                  <label>Form Title **</label>
                  <input class="form-control" name="contact_form_title" value="{{$abs->contact_form_title}}" placeholder="Enter Titlte">
                  @if ($errors->has('contact_form_title'))
                    <p class="mb-0 text-danger">{{$errors->first('contact_form_title')}}</p>
                  @endif
                </div>
                <div class="form-group">
                  <label>Form Subtitle **</label>
                  <input class="form-control" name="contact_form_subtitle" value="{{$abs->contact_form_subtitle}}" placeholder="Enter Subtitlte">
                  @if ($errors->has('contact_form_subtitle'))
                    <p class="mb-0 text-danger">{{$errors->first('contact_form_subtitle')}}</p>
                  @endif
                </div>

                <div class="form-group">
                  <label>Address **</label>
                  <textarea class="form-control" name="contact_addresses" rows="3">{{$abex->contact_addresses}}</textarea>
                  <p class="mb-0 text-warning">Use newline to seperate multiple addresses.</p>
                  @if ($errors->has('contact_addresses'))
                    <p class="mb-0 text-danger">{{$errors->first('contact_addresses')}}</p>
                  @endif
                </div>

                <div class="form-group">
                  <label>Phone **</label>
                  <input class="form-control" name="contact_numbers" data-role="tagsinput" value="{{$abex->contact_numbers}}" placeholder="Enter Phone Number">
                  <p class="mb-0 text-warning">Use comma (,) to seperate multiple contact numbers.</p>
                  @if ($errors->has('contact_numbers'))
                    <p class="mb-0 text-danger">{{$errors->first('contact_numbers')}}</p>
                  @endif
                </div>

                <div class="form-group">
                  <label>Email **</label>
                  <input class="form-control ltr" name="contact_mails" data-role="tagsinput" value="{{$abex->contact_mails}}" placeholder="Enter Email Address">
                  <p class="mb-0 text-warning">Use comma (,) to seperate multiple contact mails.</p>
                  @if ($errors->has('contact_mails'))
                    <p class="mb-0 text-danger">{{$errors->first('contact_mails')}}</p>
                  @endif
                </div>

                <div class="form-group">
                  <label>Latitude </label>
                  <input class="form-control" name="latitude" value="{{$abex->latitude}}" placeholder="Enter Google Map Address">
                  @if ($errors->has('latitude'))
                    <p class="mb-0 text-danger">{{$errors->first('latitude')}}</p>
                  @endif
                </div>

                <div class="form-group">
                  <label>Longitude</label>
                  <input class="form-control" name="longitude" value="{{$abex->longitude}}" placeholder="Enter Google Map Address">
                  @if ($errors->has('longitude'))
                    <p class="mb-0 text-danger">{{$errors->first('longitude')}}</p>
                  @endif
                </div>

                <div class="form-group">
                  <label>Map Zoom</label>
                  <input class="form-control" name="map_zoom" value="{{$abex->map_zoom}}" placeholder="Enter Google Map Address">
                  @if ($errors->has('map_zoom'))
                    <p class="mb-0 text-danger">{{$errors->first('map_zoom')}}</p>
                  @endif
                </div>

              </div>
            </div>
          </div>
          <div class="card-footer pt-3">
            <div class="form">
              <div class="form-group from-show-notify row">
                <div class="col-12 text-center">
                  <button id="displayNotif" class="btn btn-success">Update</button>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $("input[name='contact_addresses']").tagsinput({ delimiter: '|' });
        });
    </script>
@endsection
