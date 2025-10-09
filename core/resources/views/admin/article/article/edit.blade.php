@extends('admin.layout')

@if(!empty($article->language) && $article->language->rtl == 1)
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
  <h4 class="page-title">Edit Article</h4>
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
      <a href="#">Edit Article</a>
    </li>
  </ul>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <div class="card-title d-inline-block">Edit Article</div>
        <a
          class="btn btn-info btn-sm float-right d-inline-block"
          href="{{route('admin.article.index') . '?language=' . request()->input('language')}}"
        >
          <span class="btn-label">
            <i
              class="fas fa-backward"
              style="font-size: 12px;"
            ></i>
          </span>
          Back
        </a>
      </div>

      <div class="card-body pt-5 pb-5">
        <div class="row">
          <div class="col-lg-6 offset-lg-3">
            <form
              id="ajaxForm"
              class=""
              action="{{route('admin.article.update')}}"
              method="post"
            >
              @csrf
              <input
                type="hidden"
                name="article_id"
                value="{{$article->id}}"
              >
              <div class="form-group">
                <label for="">Title **</label>
                <input
                  type="text"
                  class="form-control"
                  name="title"
                  value="{{$article->title}}"
                  placeholder="Enter Title"
                >
                <p
                  id="errtitle"
                  class="mb-0 text-danger em"
                ></p>
              </div>

              <div class="form-group">
                <label for="">Category **</label>
                <select
                  class="form-control"
                  name="article_category_id"
                >
                  <option
                    value=""
                    selected
                    disabled
                  >Select a Category</option>
                  @foreach ($article_categories as $article_category)
                  <option
                    value="{{$article_category->id}}"
                    {{$article_category->id == $article->articleCategory->id ? 'selected' : ''}}
                  >{{$article_category->name}}</option>
                  @endforeach
                </select>
                <p
                  id="errcategory"
                  class="mb-0 text-danger em"
                ></p>
              </div>

              <div class="form-group">
                <label for="">Content **</label>
                <textarea
                  class="form-control summernote"
                  name="content"
                  data-height="300"
                  placeholder="Enter Content"
                >{{replaceBaseUrl($article->content)}}</textarea>
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
                  value="{{$article->serial_number}}"
                  placeholder="Enter Serial Number"
                >
                <p
                  id="errserial_number"
                  class="mb-0 text-danger em"
                ></p>
                <p class="text-warning"><small>The higher the serial number is, the later the blog will be shown.</small></p>
              </div>

              <div class="form-group">
                <label for="">Meta Keywords</label>
                <input
                  type="text"
                  class="form-control"
                  name="meta_keywords"
                  value="{{$article->meta_keywords}}"
                  data-role="tagsinput"
                >
                <p
                  id="errmeta_keywords"
                  class="mb-0 text-danger em"
                ></p>
              </div>

              <div class="form-group">
                <label for="">Meta Description</label>
                <textarea
                  type="text"
                  class="form-control"
                  name="meta_description"
                  rows="5"
                >{{$article->meta_description}}</textarea>
                <p
                  id="errmeta_description"
                  class="mb-0 text-danger em"
                ></p>
              </div>
            </form>
          </div>
        </div>
      </div>

      <div class="card-footer">
        <div class="form">
          <div class="form-group from-show-notify row">
            <div class="col-12 text-center">
              <button
                type="submit"
                id="submitBtn"
                class="btn btn-success"
              >Update</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
