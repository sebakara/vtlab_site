@extends('user.layout')

@section('pagename')
- {{__('Course Lessons')}}
@endsection

@section('content')
{{-- hero area start --}}
<div
  class="breadcrumb-area services service-bg"
  style="background-image: url('{{asset('assets/front/img/' . $bs->breadcrumb)}}'); background-size:cover;"
>
  <div class="container">
    <div class="breadcrumb-txt">
      <div class="row">
        <div class="col-xl-7 col-lg-8 col-sm-10">
          <h1>{{$course->title}}</h1>
          <ul class="breadcumb">
            <li><a href="{{route('user-dashboard')}}">{{__('Dashboard')}}</a></li>
            <li>{{__('Course Lessons')}}</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <div class="breadcrumb-area-overlay"></div>
</div>
{{-- hero area end --}}

{{-- course lessons area start --}}
<section class="course-videos-section dashboard pt-120 pb-120">
  <div class="container">
    <div class="row no-gutters">
      <div class="col-lg-5">
        <div class="video_list">
          <div class="content-box">
            <div
              class="accordion"
              id="accordionExample"
            >
              @foreach ($modules as $module)
                <div class="card">
                  <a
                    class="card-header collapsed py-3"
                    href="#"
                    id="headingone"
                    data-toggle="collapse"
                    data-target="{{'#collapse' . $module->id}}"
                    aria-expanded="{{$loop->first ? 'true' : 'false'}}"
                    aria-controls="{{'collapse' . $module->id}}"
                  >{{$module->name}}
                    <span class="toggle_btn"></span>
                    <small class="badge bg-white text-secondary float-right mr-2 mt-1">{{$module->duration}}</small>
                  </a>
                  <div
                    id="{{'collapse' . $module->id}}"
                    class="{{$loop->first ? 'collapse show' : 'collapse'}}"
                    aria-labelledby="headingOne"
                    data-parent="#accordionExample"
                  >
                    <div class="card-body">
                      <ul>
                        @php
                          $lessons = App\Lesson::where('module_id', $module->id)->get();
                        @endphp
                        @foreach ($lessons as $lesson)
                          <li><a href="#" class="videoLesson" data-video_file="{{$lesson->video_file}}" data-video_link="{{$lesson->video_link}}"><i class="fas fa-play"></i> {{$lesson->name}} <span class="duration">{{$lesson->duration}}</span></a></li>
                        @endforeach
                      </ul>
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-7">
        <div class="video-wrapper">
          <div class="video-box">
            <video
              id="videoFileId"
              class="d-none"
              controls
              type="video/mp4"
            ></video>

            <iframe
              id="videoLinkId"
              class="d-none"
              height="350px"
              width="100%"
              allowfullscreen
            ></iframe>

          </div>
        </div>
      </div>
    </div>
  </div>
</section>
{{-- course lessons area end --}}
@endsection

@section('scripts')
  <script>
    $(document).ready(function() {
      $('.videoLesson').on('click', function(event) {
        event.preventDefault();

        let datas = $(this).data();

        if (datas.video_file !== '') {
          $('#videoLinkId').attr('src', '');
          $('#videoFileId').removeClass('d-none');
          let videoFile = "{{asset('assets/front/video/lesson_videos')}}" + '/' + datas.video_file;
          $('#videoFileId').attr('src', videoFile);
        } else {
          $('#videoFileId').addClass('d-none');
        }

        if (datas.video_link !== '') {
          $('#videoFileId').attr('src', '');
          $('#videoLinkId').removeClass('d-none');
          let videoLink = datas.video_link;
          $('#videoLinkId').attr('src', videoLink);
        } else {
          $('#videoLinkId').addClass('d-none');
        }
      });
    });

    $(window).on('load', function() {
        $(".videoLesson").eq(0).trigger('click');
    })
  </script>
@endsection
