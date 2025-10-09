@extends('admin.layout')

@section('content')
<div class="page-header">
  <h4 class="page-title">{{ $module->name }}</h4>
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
      <a href="#">Modules</a>
    </li>
    <li class="separator">
      <i class="flaticon-right-arrow"></i>
    </li>
    <li class="nav-item">
      <a href="#">Lessons</a>
    </li>
  </ul>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col-lg-4">
            <div class="card-title d-inline-block">Lessons</div>
          </div>

          <div class="col-lg-3"></div>

          <div class="col-lg-4 offset-lg-1 mt-2 mt-lg-0">
            <a
              href="#"
              class="btn btn-primary float-right btn-sm"
              data-toggle="modal"
              data-target="#createModal"
            ><i class="fas fa-plus"></i> Add Lesson</a>

            <button
              class="btn btn-danger float-right btn-sm mr-2 d-none bulk-delete"
              data-href="{{route('admin.module.lesson.bulk_delete')}}"
            ><i class="flaticon-interface-5"></i> Delete</button>
          </div>
        </div>
      </div>

      <div class="card-body">
        <div class="row">
          <div class="col-lg-12">
            @if (count($lessons) == 0)
            <h3 class="text-center">NO LESSON FOUND</h3>
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
                    <th scope="col">Lesson Name</th>
                    <th scope="col">Lesson Duration</th>
                    <th scope="col">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($lessons as $lesson)
                  <tr>
                    <td>
                      <input
                        type="checkbox"
                        class="bulk-check"
                        data-val="{{$lesson->id}}"
                      >
                    </td>
                    <td>{{convertUtf8($lesson->name)}}</td>
                    <td>{{$lesson->duration}}</td>
                    <td>
                      <a
                        class="btn btn-secondary btn-sm lessonEditBtn"
                        href="#editModal"
                        data-toggle="modal"
                        data-lesson_id="{{$lesson->id}}"
                        data-name="{{$lesson->name}}"
                        data-edit_video="{{!empty($lesson->video_file) ? 1 : 2}}"
                        data-file="{{$lesson->video_file}}"
                        data-link="{{$lesson->video_link}}"
                        data-duration="{{$lesson->duration}}"
                      >
                        <span class="btn-label">
                          <i class="fas fa-edit"></i>
                        </span>
                        Edit
                      </a>

                      <form
                        class="deleteform d-inline-block"
                        action="{{route('admin.module.lesson.delete')}}"
                        method="post"
                      >
                        @csrf
                        <input
                          type="hidden"
                          name="lesson_id"
                          value="{{$lesson->id}}"
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


<!-- Create Module Lesson Modal -->
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
        >Add Module Lesson</h5>
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
          action="{{route('admin.module.lesson.store')}}"
          method="POST"
        >
          @csrf
          <input
            type="hidden"
            name="module_id"
            value="{{ $module->id }}"
          >

          <div class="form-group">
            <label for="">Lesson Name **</label>
            <input
              type="text"
              class="form-control"
              name="name"
              value=""
              placeholder="Enter Lesson Name"
            >
            <p
              id="errname"
              class="mb-0 text-danger em"
            ></p>
          </div>

          <div class="form-group mb-1">
            <label for="">Lesson Video **</label>
            <div class="d-flex flex-row">
              <div class="mr-5">
                <input
                  type="radio"
                  id="video_file"
                  name="video"
                  value="1"
                >
                <p class="d-inline-block">Upload Video</p>
              </div>

              <div>
                <input
                  type="radio"
                  id="video_link"
                  name="video"
                  value="2"
                >
                <p class="d-inline-block">Enter Video Link</p>
              </div>
            </div>

            <div>
              <div
                id="upload_btn_id"
                class="d-none"
              >
                {{-- Video Part --}}
                <div class="form-group p-0">
                    <div class="video-preview" id="videoPreview2">
                        <video width="320" height="240" controls id="video_src">
                            <source src="" type="video/mp4">
                        </video>
                    </div>
                    <br>


                    <input id="fileInput2" type="hidden" name="video_file">
                    <button id="chooseVideo2" class="choose-video btn btn-primary" type="button" data-multiple="false" data-video="true" data-toggle="modal" data-target="#lfmModal2">Choose Video</button>


                    <p class="text-warning mb-0">MP4 video is allowed</p>
                    <p class="em text-danger mb-0" id="errvideo_file"></p>

                </div>
              </div>

              <div
                id="video_link_id"
                class="d-none"
              >
                <input
                  class="form-control"
                  type="text"
                  name="video_link"
                  placeholder="Enter Embed Video Link"
                  value=""
                >
              </div>
            </div>
            <p
              id="errvideo"
              class="mb-0 text-danger em"
            ></p>
          </div>



          <div class="form-group">
            <label for="">Lesson Duration **</label>
            <input
              type="text"
              class="form-control"
              name="duration"
              value=""
              placeholder="eg: 20m 25s"
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

<!-- Edit Module Lesson Modal -->
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
        >Edit Module Lesson</h5>
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
          action="{{route('admin.module.lesson.update')}}"
          method="POST"
        >
          @csrf
          <input
            id="inlesson_id"
            type="hidden"
            name="lesson_id"
            value=""
          >
          <div class="form-group">
            <label for="">Lesson Name **</label>
            <input
              id="inname"
              type="text"
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

          <div class="form-group mb-1">
            <label for="">Lesson Video **</label>
            <div class="d-flex flex-row">
              <div class="mr-5">
                <input
                  type="radio"
                  id="videoFile"
                  name="edit_video"
                  value="1"
                >
                <p class="d-inline-block">Upload Video</p>
              </div>

              <div>
                <input
                  type="radio"
                  id="videoLink"
                  name="edit_video"
                  value="2"
                >
                <p class="d-inline-block">Enter Video Link</p>
              </div>
            </div>

            <div>
              <div
                id="edit_upload_btn_id"
                class="d-none"
              >
                {{-- Video Part --}}
                <div class="form-group p-0">
                    <div class="video-preview" id="videoPreview1">
                        <video width="320" height="240" controls id="video_src">
                            <source src="" type="video/mp4">
                        </video>
                    </div>
                    <br>


                    <input id="fileInput1" type="hidden" name="video_file">
                    <button id="chooseVideo1" class="choose-video btn btn-primary" type="button" data-multiple="false" data-video="true" data-toggle="modal" data-target="#lfmModal1">Choose Video</button>


                    <p class="text-warning mb-0">MP4 video is allowed</p>
                    <p class="em text-danger mb-0" id="eerrvideo_file"></p>

                </div>
              </div>

              <div
                id="edit_video_link_id"
                class="d-none"
              >
                <input
                  id="inlink"
                  class="form-control"
                  type="text"
                  name="edit_video_link"
                  placeholder="Enter Embed Video Link"
                >
              </div>
            </div>
            <p
              id="eerredit_video"
              class="mb-0 text-danger em"
            ></p>
          </div>

          <div class="form-group">
            <label for="">Lesson Duration **</label>
            <input
              id="induration"
              type="text"
              class="form-control"
              name="duration"
              value=""
              placeholder="eg: 20m 25s"
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


<!-- Video LFM Modal -->
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
<!-- Video LFM Modal -->
<div class="modal fade lfm-modal" id="lfmModal2" tabindex="-1" role="dialog" aria-labelledby="lfmModalTitle" aria-hidden="true">
    <i class="fas fa-times-circle"></i>
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-body p-0">
                <iframe src="{{url('laravel-filemanager')}}?serial=2" style="width: 100%; height: 500px; overflow: hidden; border: none;"></iframe>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
  $(document).ready(function() {
    // on page load show default input field for video upload
    let btnName = $('input:radio[name=video]');

    if (btnName.is(':checked') === false) {
      btnName.filter('[value=1]').prop('checked', true);

      let radioValue = $("input[name='video']:checked").val();

      if (radioValue == 1) {
        $('#upload_btn_id').removeClass('d-none');
      }
    }

    // show different video input field by toggling radio button
    $("input[type='radio']").click(function() {
      let radioValue = $("input[name='video']:checked").val();

      if (radioValue == 1) {
        $('#upload_btn_id').removeClass('d-none');
        $('#video_link_id').addClass('d-none');
      } else {
        $('#video_link_id').removeClass('d-none');
        $('#upload_btn_id').addClass('d-none');
      }
    });

    /*=========================
    jquery code for edit modal
    =========================*/

    $(".lessonEditBtn").on('click', function () {
      let datas = $(this).data();

      // first, get the value of which video field has data. either video_file = 1 or the video_link = 2
      for (let x in datas) {
        if ($("input[name='" + x + "']").attr('type') == 'radio') {
          $("input[name='" + x + "']").each(function (i) {
            if ($(this).val() == datas[x]) {
              $(this).prop('checked', true);
            }
          });
        } else if (x == 'file' && datas['file']) {
            $("#editModal").find('source').attr('src', "{{url('assets/front/video/lesson_videos')}}/" + datas['file']);
            $("#editModal video")[0].load();
        } else {
          $("#in" + x).val(datas[x]);
        }
      }

      // then, on page load show previous checked input field for edit modal
      let radioVal = $("input[name='edit_video']:checked").val();

      if (radioVal == 1) {
        $('#edit_upload_btn_id').removeClass('d-none');
        $('#edit_video_link_id').addClass('d-none');
      } else {
        $('#edit_video_link_id').removeClass('d-none');
        $('#edit_upload_btn_id').addClass('d-none');
      }

      // show different video input field by toggling radio button for edit modal
      $("input[type='radio']").click(function() {
        let radioBtnVal = $("input[name='edit_video']:checked").val();

        if (radioBtnVal == 1) {
          $('#edit_upload_btn_id').removeClass('d-none');
          $('#edit_video_link_id').addClass('d-none');
        } else {
          $('#edit_video_link_id').removeClass('d-none');
          $('#edit_upload_btn_id').addClass('d-none');
        }
      });
    });
  });
</script>
@endsection
