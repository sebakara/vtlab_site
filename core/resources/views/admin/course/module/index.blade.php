@extends('admin.layout')

@section('content')
<div class="page-header">
  <h4 class="page-title">{{ $course->title }}</h4>
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
      <a href="#">Courses</a>
    </li>
    <li class="separator">
      <i class="flaticon-right-arrow"></i>
    </li>
    <li class="nav-item">
      <a href="#">Modules</a>
    </li>
  </ul>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col-lg-4">
            <div class="card-title d-inline-block">Modules</div>
          </div>

          <div class="col-lg-3"></div>

          <div class="col-lg-4 offset-lg-1 mt-2 mt-lg-0">
            <a
              href="#"
              class="btn btn-primary float-right btn-sm"
              data-toggle="modal"
              data-target="#createModal"
            ><i class="fas fa-plus"></i> Add Module</a>

            <button
              class="btn btn-danger float-right btn-sm mr-2 d-none bulk-delete"
              data-href="{{route('admin.course.module.bulk_delete')}}"
            ><i class="flaticon-interface-5"></i> Delete</button>
          </div>
        </div>
      </div>

      <div class="card-body">
        <div class="row">
          <div class="col-lg-12">
            @if (count($modules) == 0)
            <h3 class="text-center">NO COURSE MODULE FOUND</h3>
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
                    <th scope="col">Module Name</th>
                    <th scope="col">Module Duration</th>
                    <th scope="col">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($modules as $module)
                  <tr>
                    <td>
                      <input
                        type="checkbox"
                        class="bulk-check"
                        data-val="{{$module->id}}"
                      >
                    </td>
                    <td>{{convertUtf8(strlen($module->name)) > 30 ? convertUtf8(substr($module->name, 0, 30)) . '...' : convertUtf8($module->name)}}</td>
                    <td>{{$module->duration}}</td>
                    <td>
                      <a
                        class="btn btn-secondary btn-sm editbtn"
                        href="#editModal"
                        data-toggle="modal"
                        data-module_id="{{$module->id}}"
                        data-name="{{$module->name}}"
                        data-duration="{{$module->duration}}"
                      >
                        <span class="btn-label">
                          <i class="fas fa-edit"></i>
                        </span>
                        Edit
                      </a>

                      <form
                        class="deleteform d-inline-block"
                        action="{{route('admin.course.module.delete')}}"
                        method="post"
                      >
                        @csrf
                        <input
                          type="hidden"
                          name="module_id"
                          value="{{$module->id}}"
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
                        href="{{route('admin.module.lesson.index', $module->id) . '?language=' . request()->input('language')}}"
                      >
                        <span class="btn-label">
                          <i class="fa fa-graduation-cap"></i>
                        </span>
                        Lessons
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


<!-- Create Course Module Modal -->
<div
  class="modal fade"
  id="createModal"
  tabindex="-1"
  role="dialog"
  aria-labelledby="exampleModalCenterTitle"
  aria-hidden="true"
>
  <div
    class="modal-dialog modal-dialog-centered"
    role="document"
  >
    <div class="modal-content">
      <div class="modal-header">
        <h5
          class="modal-title"
          id="exampleModalLongTitle"
        >Add Course Module</h5>
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
          class="modal-form create"
          action="{{route('admin.course.module.store')}}"
          method="POST"
        >
          @csrf
          <input type="hidden" name="course_id" value="{{ $course->id }}">

          <div class="form-group">
            <label for="">Module Name **</label>
            <input
              type="text"
              class="form-control"
              name="name"
              value=""
              placeholder="Enter Module Name"
            >
            <p
              id="errname"
              class="mb-0 text-danger em"
            ></p>
          </div>

          <div class="form-group">
            <label for="">Module Duration **</label>
            <input
              type="text"
              class="form-control"
              name="duration"
              value=""
              placeholder="eg: 10h 15m"
            >
            <p
              id="errduration"
              class="mb-0 text-danger em"
            ></p>
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

<!-- Edit Course Module Modal -->
<div
  class="modal fade"
  id="editModal"
  tabindex="-1"
  role="dialog"
  aria-labelledby="exampleModalCenterTitle"
  aria-hidden="true"
>
  <div
    class="modal-dialog modal-dialog-centered"
    role="document"
  >
    <div class="modal-content">
      <div class="modal-header">
        <h5
          class="modal-title"
          id="exampleModalLongTitle"
        >Edit Course Module</h5>
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
          id="ajaxEditForm"
          class=""
          action="{{route('admin.course.module.update')}}"
          method="POST"
        >
          @csrf
          <input
            id="inmodule_id"
            type="hidden"
            name="module_id"
            value=""
          >
          <div class="form-group">
            <label for="">Module Name **</label>
            <input
              id="inname"
              type="name"
              class="form-control"
              name="name"
              value=""
              placeholder="Enter Name"
            >
            <p
              id="eerrname"
              class="mb-0 text-danger em"
            ></p>
          </div>

          <div class="form-group">
            <label for="">Module Duration **</label>
            <input
              id="induration"
              type="text"
              class="form-control"
              name="duration"
              value=""
              placeholder="eg: 10h 15m"
            >
            <p
              id="eerrduration"
              class="mb-0 text-danger em"
            ></p>
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
          id="updateBtn"
          type="button"
          class="btn btn-primary"
        >Save Changes</button>
      </div>
    </div>
  </div>
</div>
@endsection
