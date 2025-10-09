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
   <h4 class="page-title">Events</h4>
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
         <a href="#">Event Page</a>
      </li>
      <li class="separator">
         <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
         <a href="#">Events</a>
      </li>
   </ul>
</div>
<div class="row">
   <div class="col-md-12">
      <div class="card">
         <div class="card-header">
            <div class="row">
               <div class="col-lg-4">
                  <div class="card-title d-inline-block">Events</div>
               </div>
               <div class="col-lg-3">
                  @if (!empty($langs))
                  <select name="language" class="form-control" onchange="window.location='{{url()->current() . '?language='}}'+this.value">
                     <option value="" selected disabled>Select a Language</option>
                     @foreach ($langs as $lang)
                     <option value="{{$lang->code}}" {{$lang->code == request()->input('language') ? 'selected' : ''}}>{{$lang->name}}</option>
                     @endforeach
                  </select>
                  @endif
               </div>

               <div class="col-lg-4 offset-lg-1 mt-2 mt-lg-0">
                  <a href="#" class="btn btn-primary float-right btn-sm" data-toggle="modal" data-target="#createModal"><i class="fas fa-plus" style="color: white !important;"></i> Add Event</a>
                  <button class="btn btn-danger float-right btn-sm mr-2 d-none bulk-delete" data-href="{{route('admin.event.bulk.delete')}}"><i class="flaticon-interface-5"></i> Delete</button>
               </div>
            </div>
         </div>
         <div class="card-body">
            <div class="row">
               <div class="col-lg-12">

                  @if (count($events) == 0)
                  <h3 class="text-center">NO EVENT FOUND</h3>
                  @else
                  <div class="table-responsive">
                     <table class="table table-striped mt-3" id="basic-datatables">
                        <thead>
                           <tr>
                              <th scope="col">
                                 <input type="checkbox" class="bulk-check" data-val="all">
                              </th>
                              <th scope="col">Image</th>
                              <th scope="col">Category</th>
                              <th scope="col">Title</th>
                              <th scope="col">Event Date</th>
                              <th scope="col">Actions</th>
                           </tr>
                        </thead>
                        <tbody>
                           @foreach ($events as $key => $event)
                           <tr>
                              <td>
                                 <input type="checkbox" class="bulk-check" data-val="{{$event->id}}">
                              </td>
                              @php
                                $images = json_decode($event->image, true);
                              @endphp
                              <td><img src="{{(!empty($images)) ? asset('/assets/front/img/events/sliders/'.$images[0]) : ''}}" alt="" width="80"></td>
                              <td>{{ !empty(convertUtf8($event->eventCategories)) ? convertUtf8($event->eventCategories->name) : '' }}</td>
                              <td>{{convertUtf8(strlen($event->title)) > 30 ? convertUtf8(substr($event->title, 0, 30)) . '...' : convertUtf8($event->title)}}</td>
                              <td>
                                 @php
                                 $date = \Carbon\Carbon::parse($event->date);
                                 @endphp
                                 {{$date->translatedFormat('jS F, Y')}}
                              </td>
                              <td>
                                 <a class="btn btn-secondary btn-sm" href="{{route('admin.event.edit', $event->id) . '?language=' . request()->input('language')}}">
                                 <span class="btn-label">
                                 <i class="fas fa-edit"></i>
                                 </span>
                                 Edit
                                 </a>
                                 <form class="deleteform d-inline-block" action="{{route('admin.event.delete')}}" method="post">
                                    @csrf
                                    <input type="hidden" name="event_id" value="{{$event->id}}">
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
<!-- Create Blog Modal -->
<div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle">Add Event</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body">

            <form id="ajaxForm" class="modal-form" action="{{route('admin.event.store')}}" method="POST">
               @csrf

                {{-- Video Part --}}
                <div class="form-group">
                    <label for="">Video ** </label>
                    <br>
                    <div class="video-preview" id="videoPreview2">
                        <video width="320" height="240" controls id="video_src">
                            <source src="" type="video/mp4">
                        </video>
                    </div>
                    <br>


                    <input id="fileInput2" type="hidden" name="video">
                    <button id="chooseVideo2" class="choose-video btn btn-primary" type="button" data-multiple="false" data-video="true" data-toggle="modal" data-target="#lfmModal2">Choose Video</button>


                    <p class="text-warning mb-0">MP4 video is allowed</p>
                    <p class="em text-danger mb-0" id="errvideo"></p>

                </div>
                {{-- START: slider Part --}}
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="">Slider Images ** </label>
                            <br>
                            <div class="slider-thumbs" id="sliderThumbs1">

                            </div>

                            <input id="fileInput1" type="hidden" name="slider" value="" />
                            <button id="chooseImage1" class="choose-image btn btn-primary" type="button" data-multiple="true" data-toggle="modal" data-target="#lfmModal1">Choose Images</button>


                            <p class="text-warning mb-0">JPG, PNG, JPEG images are allowed</p>
                            <p id="errslider" class="mb-0 text-danger em"></p>


                        </div>
                    </div>
                </div>
                {{-- END: slider Part --}}
               <div class="form-group">
                  <label for="">Language **</label>
                  <select id="language" name="lang_id" class="form-control" required>
                     <option value="" selected disabled>Select a language</option>
                     @foreach ($langs as $lang)
                     <option value="{{$lang->id}}">{{$lang->name}}</option>
                     @endforeach
                  </select>
                  <p id="errlang_id" class="mb-0 text-danger em"></p>
               </div>
               <div class="form-group">
                  <label for="">Title **</label>
                  <input type="text" class="form-control" name="title" placeholder="Enter title" value="" required>
                  <p id="errtitle" class="mb-0 text-danger em"></p>
               </div>
               <div class="form-group">
                  <label for="">Category **</label>
                  <select id="bcategory" class="form-control" name="cat_id" disabled required>
                     <option value="" selected disabled>Select a category</option>
                  </select>
                  <p id="errcat_id" class="mb-0 text-danger em"></p>
               </div>

               <div class="form-group">
                  <label for="">Content</label>
                  <textarea class="form-control summernote" name="content" rows="8" cols="80" placeholder="Enter content"></textarea>
                  <p id="errcontent" class="mb-0 text-danger em"></p>
               </div>
                <div class="form-group">
                    <label for="">Date **</label>
                    <input type="date" class="form-control ltr" name="date" value="" placeholder="Enter Event Date" required>
                    <p id="errdate" class="mb-0 text-danger em"></p>
                </div>
                <div class="form-group">
                    <label for="">Time **</label>
                    <input type="time" class="form-control ltr" name="time" value="" placeholder="Enter Event Time" required>
                    <p id="errtime" class="mb-0 text-danger em"></p>
                </div>
               <div class="form-group">
                  <label for="">Cost (in {{$abx->base_currency_text}}) **</label>
                  <input type="number" class="form-control ltr" name="cost" value="" placeholder="Enter Ticket Cost" required>
                  <p id="errcost" class="mb-0 text-danger em"></p>
               </div>
                <div class="form-group">
                    <label for="">Available Tickets **</label>
                    <input type="number" class="form-control ltr" name="available_tickets" value="" placeholder="Enter Available Tickets Number" required>
                    <p id="erravailable_tickets" class="mb-0 text-danger em"></p>
                </div>
                <div class="form-group">
                    <label for="">Organizer **</label>
                    <input type="text" class="form-control ltr" name="organizer" value="" placeholder="Event Organizer" required>
                    <p id="errorganizer" class="mb-0 text-danger em"></p>
                </div>
                <div class="form-group">
                    <label for="">Organizer Email</label>
                    <input type="text" class="form-control ltr" name="organizer_email" value="" placeholder="Organizer Email">
                    <p id="errorganizer_email" class="mb-0 text-danger em"></p>
                </div>
                <div class="form-group">
                    <label for="">Organizer Phone</label>
                    <input type="text" class="form-control ltr" name="organizer_phone" value="" placeholder="Organizer Email">
                    <p id="errorganizer_phone" class="mb-0 text-danger em"></p>
                </div>
                <div class="form-group">
                    <label for="">Organizer Website</label>
                    <input type="text" class="form-control ltr" name="organizer_website" value="" placeholder="Organizer Website">
                    <p id="errorganizer_website" class="mb-0 text-danger em"></p>
                </div>
                <div class="form-group">
                    <label for="">Venue **</label>
                    <input type="text" class="form-control ltr" name="venue" value="" placeholder="Enter Venue" required>
                    <p id="errvenue" class="mb-0 text-danger em"></p>
                </div>
                <div class="form-group">
                    <label for="">Venue Location</label>
                    <input type="text" class="form-control ltr" name="venue_location" value="" placeholder="Venue Location">
                    <p id="errvenue_location" class="mb-0 text-danger em"></p>
                </div>
                <div class="form-group">
                    <label for="">Venue Phone</label>
                    <input type="text" class="form-control ltr" name="venue_phone" value="" placeholder="Venue Phone">
                    <p id="errvenue_phone" class="mb-0 text-danger em"></p>
                </div>
               <div class="form-group">
                  <label for="">Meta Keywords</label>
                  <input type="text" class="form-control" name="meta_tags" value="" data-role="tagsinput">
               </div>
               <div class="form-group">
                  <label for="">Meta Description</label>
                  <textarea type="text" class="form-control" name="meta_description" rows="5"></textarea>
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

<!-- slider LFM Modal -->
<div class="modal fade lfm-modal" id="lfmModal1" tabindex="-1" role="dialog" aria-labelledby="lfmModalTitle" aria-hidden="true">
    <i class="fas fa-times-circle"></i>
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-body p-0">
                <iframe id="lfmIframe1" src="{{url('laravel-filemanager')}}?serial=1" style="width: 100%; height: 500px; overflow: hidden; border: none;"></iframe>
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
       $("select[name='lang_id']").on('change', function() {
           $("#bcategory").removeAttr('disabled');
           let langid = $(this).val();
           let url = "{{url('/')}}/admin/event/" + langid + "/get-categories";
           $.get(url, function(data) {
               console.log(data);
               let options = `<option value="" disabled selected>Select a category</option>`;
               for (let i = 0; i < data.length; i++) {
                   options += `<option value="${data[i].id}">${data[i].name}</option>`;
               }
               $("#bcategory").html(options);

           });
       });

       // make input fields RTL
       $("select[name='lang_id']").on('change', function() {
           $(".request-loader").addClass("show");
           let url = "{{url('/')}}/admin/rtlcheck/" + $(this).val();
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
           if (language == 0) {
               $("#translatable").attr('disabled', true);
           } else {
               $("#translatable").removeAttr('disabled');
           }
       });

       $("#upload-video").on('change',function (event){
           let formData = new FormData($('#video-frm')[0]);
           let file = $('input[type=file]')[0].files[0];
           // formData.append('upload_video', file, file.name);
           formData.append('upload_video', file);
           $.ajax({
               url: '{{route('admin.event.upload')}}',
               headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
               type: 'POST',
               contentType: false,
               processData: false,
               cache: false,
               data: formData,
               success: function(data) {
                   $("#my_video").val(data.filename);
                   var url = '{{ asset("assets/front/img/events/videos/filename") }}';
                   url = url.replace('filename', data.filename);
                   $("#video_src").attr('src',url);
               },
               error: function(data) {
                   console.log(data);
               }
           })
       })
   });

</script>
@endsection
