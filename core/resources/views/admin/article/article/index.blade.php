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
  <h4 class="page-title">Articles</h4>
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
      <a href="#">Article Page</a>
    </li>
    <li class="separator">
      <i class="flaticon-right-arrow"></i>
    </li>
    <li class="nav-item">
      <a href="#">Articles</a>
    </li>
  </ul>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col-lg-4">
            <div class="card-title d-inline-block">Articles</div>
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
              href="#"
              class="btn btn-primary float-right btn-sm"
              data-toggle="modal"
              data-target="#createModal"
            ><i class="fas fa-plus"></i> Add Article</a>
            <button
              class="btn btn-danger float-right btn-sm mr-2 d-none bulk-delete"
              data-href="{{route('admin.article.bulk_delete')}}"
            ><i class="flaticon-interface-5"></i> Delete</button>
          </div>
        </div>
      </div>

      <div class="card-body">
        <div class="row">
          <div class="col-lg-12">
            @if (count($articles) == 0)
              <h3 class="text-center">NO ARTICLE FOUND</h3>
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
                      {{-- <th scope="col">Image</th> --}}
                      <th scope="col">Category</th>
                      <th scope="col">Title</th>
                      <th scope="col">Publish Date</th>
                      <th scope="col">Serial Number</th>
                      <th scope="col">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($articles as $article)
                    <tr>
                      <td>
                        <input
                          type="checkbox"
                          class="bulk-check"
                          data-val="{{$article->id}}"
                        >
                      </td>

                      {{-- <td>
                        <img
                          src="{{asset('assets/front/img/articles/'.$article->image)}}"
                          alt=""
                          width="80"
                        >
                      </td> --}}

                      <td>{{convertUtf8($article->articleCategory->name)}}</td>
                      <td>{{convertUtf8(strlen($article->title)) > 30 ? convertUtf8(substr($article->title, 0, 30)) . '...' : convertUtf8($article->title)}}</td>
                      <td>
                        @php
                        $date = \Carbon\Carbon::parse($article->created_at);
                        @endphp
                        {{$date->translatedFormat('jS F, Y')}}
                      </td>
                      <td>{{$article->serial_number}}</td>
                      <td>
                        <a
                          class="btn btn-secondary btn-sm"
                          href="{{route('admin.article.edit', $article->id) . '?language=' . request()->input('language')}}"
                        >
                          <span class="btn-label">
                            <i class="fas fa-edit"></i>
                          </span>
                          Edit
                        </a>

                        <form
                          class="deleteform d-inline-block"
                          action="{{route('admin.article.delete')}}"
                          method="post"
                        >
                          @csrf
                          <input
                            type="hidden"
                            name="article_id"
                            value="{{$article->id}}"
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

<!-- Create Article Modal -->
<div
  class="modal fade"
  id="createModal"
  tabindex="-1"
  role="dialog"
  aria-labelledby="exampleModalCenterTitle"
  aria-hidden="true"
>
  <div
    class="modal-dialog modal-dialog-centered modal-lg"
    role="document"
  >
    <div class="modal-content">
      <div class="modal-header">
        <h5
          class="modal-title"
          id="exampleModalLongTitle"
        >Add Article</h5>
        <button
          type="button"
          class="close"
          data-dismiss="modal"
          aria-label="Close"
        >
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <form
          id="ajaxForm"
          class="modal-form"
          action="{{route('admin.article.store')}}"
          method="POST"
        >
          @csrf
          <input
            type="hidden"
            id="image"
            name=""
            value=""
          >
          <div class="form-group">
            <label for="">Language **</label>
            <select
              id="language"
              name="language_id"
              class="form-control"
            >
              <option
                value=""
                selected
                disabled
              >Select a Language</option>
              @foreach ($langs as $lang)
              <option value="{{$lang->id}}">{{$lang->name}}</option>
              @endforeach
            </select>
            <p
              id="errlanguage_id"
              class="mb-0 text-danger em"
            ></p>
          </div>

          <div class="form-group">
            <label for="">Title **</label>
            <input
              type="text"
              class="form-control"
              name="title"
              placeholder="Enter Title"
              value=""
            >
            <p
              id="errtitle"
              class="mb-0 text-danger em"
            ></p>
          </div>

          <div class="form-group">
            <label for="">Category **</label>
            <select
              id="article_category_id"
              class="form-control"
              name="article_category_id"
              disabled
            >
              <option
                value=""
                selected
                disabled
              >Select a category</option>
            </select>
            <p
              id="errarticle_category_id"
              class="mb-0 text-danger em"
            ></p>
          </div>

          <div class="form-group">
            <label for="">Content **</label>
            <textarea
              class="form-control summernote"
              name="content"
              rows="8"
              cols="80"
              placeholder="Enter Content"
            ></textarea>
            <p
              id="errcontent"
              class="mb-0 text-danger em"
            ></p>
          </div>

          <div class="form-group">
            <label for="">Serial Number **</label>
            <input
              type="number"
              class="form-control ltr"
              name="serial_number"
              value=""
              placeholder="Enter Serial Number"
            >
            <p
              id="errserial_number"
              class="mb-0 text-danger em"
            ></p>
            <p class="text-warning mb-0"><small>The higher the serial number is, the later the article will be shown.</small></p>
          </div>

          <div class="form-group">
            <label for="">Meta Keywords</label>
            <input
              type="text"
              class="form-control"
              name="meta_keywords"
              value=""
              data-role="tagsinput"
            >
          </div>

          <div class="form-group">
            <label for="">Meta Description</label>
            <textarea
              type="text"
              class="form-control"
              name="meta_description"
              rows="5"
            ></textarea>
          </div>
        </form>
      </div>

      <div class="modal-footer">
        <button
          type="button"
          class="btn btn-secondary"
          data-dismiss="modal"
        >Close</button>
        <button
          id="submitBtn"
          type="button"
          class="btn btn-primary"
        >Submit</button>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
  $(document).ready(function() {
    $("select[name='language_id']").on('change', function() {
      $("#article_category_id").removeAttr('disabled');

      let langId = $(this).val();
      let url = "{{url('/')}}/admin/article/" + langId + "/get_categories";
      // console.log(url);

      $.get(url, function(data) {
        // console.log(data);
        let options = `<option value="" disabled selected>Select a Category</option>`;

        for (let i = 0; i < data.length; i++) {
          options += `<option value="${data[i].id}">${data[i].name}</option>`;
        }
        $("#article_category_id").html(options);
      });
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
          $("form .summernote").each(function() {
            $(this).siblings('.note-editor').find('.note-editable').addClass('rtl text-right');
          });
        } else {
          $("form input, form select, form textarea").removeClass('rtl');
          $("form.modal-form .summernote").siblings('.note-editor').find('.note-editable').removeClass('rtl text-right');
        }
      })
    });

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
