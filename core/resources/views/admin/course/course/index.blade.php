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
  <h4 class="page-title">Courses</h4>
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
      <a href="#">Course Page</a>
    </li>
    <li class="separator">
      <i class="flaticon-right-arrow"></i>
    </li>
    <li class="nav-item">
      <a href="#">Courses</a>
    </li>
  </ul>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col-lg-4">
            <div class="card-title d-inline-block">Courses</div>
          </div>

          <div class="col-lg-3">
            @if (!empty($langs))
            <select
              name="language"
              class="form-control"
              onchange="window.location='{{url()->current() . '?language='}}'+this.value"
            >
              <option
                value=""
                selected
                disabled
              >Select a Language</option>
              @foreach ($langs as $lang)
              <option
                value="{{$lang->code}}"
                {{$lang->code == request()->input('language') ? 'selected' : ''}}
              >{{$lang->name}}</option>
              @endforeach
            </select>
            @endif
          </div>

          <div class="col-lg-4 offset-lg-1 mt-2 mt-lg-0">
            <a
              href="{{ route('admin.course.create') . '?language=' . request()->input('language') }}"
              class="btn btn-primary float-right btn-sm"
            ><i class="fas fa-plus"></i> Add Course</a>
            <button
              class="btn btn-danger float-right btn-sm mr-2 d-none bulk-delete"
              data-href="{{route('admin.course.bulk_delete')}}"
            ><i class="flaticon-interface-5"></i> Delete</button>
          </div>
        </div>
      </div>

      <div class="card-body">
        <div class="row">
          <div class="col-lg-12">
            @if (count($courses) == 0)
            <h3 class="text-center">NO COURSE FOUND</h3>
            @else
            <div class="table-responsive">
              <table class="table table-striped mt-3" id="basic-datatables">
                <thead>
                  <tr>
                    <th scope="col">
                      <input
                        type="checkbox"
                        class="bulk-check"
                        data-val="all"
                      >
                    </th>
                    <th scope="col">Image</th>
                    <th scope="col">Category</th>
                    <th scope="col">Title</th>
                    <th scope="col">Featured</th>
                    <th scope="col">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($courses as $course)
                  <tr>
                    <td>
                      <input
                        type="checkbox"
                        class="bulk-check"
                        data-val="{{$course->id}}"
                      >
                    </td>

                    <td>
                      <img
                        src="{{asset('assets/front/img/courses/'.$course->course_image)}}"
                        alt=""
                        width="80"
                      >
                    </td>

                    <td>{{convertUtf8($course->courseCategory->name)}}</td>
                    <td>{{strlen($course->title) > 30 ? mb_substr($course->title,0,30,'utf-8') . '...' : $course->title}}</td>
                    <td>
                      <form
                        id="featureForm{{$course->id}}"
                        class="d-inline-block"
                        action="{{route('admin.course.featured')}}"
                        method="post"
                      >
                        @csrf
                        <input
                          type="hidden"
                          name="course_id"
                          value="{{$course->id}}"
                        >
                        <select
                          class="form-control {{$course->is_featured == 1 ? 'bg-success' : 'bg-danger'}}"
                          name="is_featured"
                          onchange="document.getElementById('featureForm{{$course->id}}').submit();"
                        >
                          <option
                            value="1"
                            {{$course->is_featured == 1 ? 'selected' : ''}}
                          >Yes</option>
                          <option
                            value="0"
                            {{$course->is_featured == 0 ? 'selected' : ''}}
                          >No</option>
                        </select>
                      </form>
                    </td>
                    {{-- <td>
                      @php
                      $date = \Carbon\Carbon::parse($course->created_at);
                      @endphp
                      {{$date->translatedFormat('jS F, Y')}}
                    </td> --}}
                    <td>
                      <a
                        class="btn btn-secondary btn-sm"
                        href="{{route('admin.course.edit', $course->id) . '?language=' . request()->input('language')}}"
                      >
                        <span class="btn-label">
                          <i class="fas fa-edit"></i>
                        </span>
                        Edit
                      </a>

                      <form
                        class="deleteform d-inline-block"
                        action="{{route('admin.course.delete')}}"
                        method="post"
                      >
                        @csrf
                        <input
                          type="hidden"
                          name="course_id"
                          value="{{$course->id}}"
                        >
                        <button
                          type="submit"
                          class="btn btn-danger btn-sm deletebtn"
                        >
                          <span class="btn-label">
                            <i class="fas fa-trash"></i>
                          </span>
                          Delete
                        </button>
                      </form>

                      <a
                        class="btn btn-success btn-sm"
                        href="{{route('admin.course.module.index', $course->id) . '?language=' . request()->input('language')}}"
                      >
                        <span class="btn-label">
                          <i class="fa fa-book"></i>
                        </span>
                        Modules
                      </a>
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
@endsection

@section('scripts')
<script>
  $(document).ready(function() {
    // translatable portfolios will be available if the selected language is not 'Default'
    $("#language").on('change', function() {
      let language = $(this).val();
      // console.log(language);
      if (language == 0) {
        $("#translatable").attr('disabled', true);
      } else {
        $("#translatable").removeAttr('disabled');
      }
    });
  });
  // console.log('loaded');
</script>
@endsection
