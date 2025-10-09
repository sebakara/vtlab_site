@extends('admin.layout')

@section('content')
<div class="page-header">
  <h4 class="page-title">Create Portfolio</h4>
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
      <a href="#">Portfolio Page</a>
    </li>
    <li class="separator">
      <i class="flaticon-right-arrow"></i>
    </li>
    <li class="nav-item">
      <a href="#">Create Portfolio</a>
    </li>
  </ul>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <div class="card-title d-inline-block">Create Portfolio</div>
        <a class="btn btn-info btn-sm float-right d-inline-block"
          href="{{route('admin.portfolio.index') . '?language=' . request()->input('language')}}">
          <span class="btn-label">
            <i class="fas fa-backward" style="font-size: 12px;"></i>
          </span>
          Back
        </a>
      </div>
      <div class="card-body pt-5 pb-5">
        <div class="row">
          <div class="col-lg-6 offset-lg-3">
            <form id="ajaxForm" class="" action="{{route('admin.portfolio.store')}}" method="post">
              @csrf
              <div id="sliders"></div>
              <div class="row">
                <div class="col-12">
                  {{-- Featured Image Part --}}
                  <div class="form-group">
                    <label for="">Featured Image ** </label>
                    <br>
                    <div class="thumb-preview" id="thumbPreview1">
                      <img src="{{asset('assets/admin/img/noimage.jpg')}}" alt="Featured Image">
                    </div>
                    <br>
                    <br>


                    <input id="fileInput1" type="hidden" name="image">
                    <button id="chooseImage1" class="choose-image btn btn-primary" type="button" data-multiple="false"
                      data-toggle="modal" data-target="#lfmModal1">Choose Image</button>


                    <p class="text-warning mb-0">JPG, PNG, JPEG, SVG images are allowed</p>
                    <p class="em text-danger mb-0" id="errimage"></p>

                    <!-- Image LFM Modal -->
                    <div class="modal fade lfm-modal" id="lfmModal1" tabindex="-1" role="dialog"
                      aria-labelledby="lfmModalTitle" aria-hidden="true">
                      <i class="fas fa-times-circle"></i>
                      <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                        <div class="modal-content">
                          <div class="modal-body p-0">
                            <iframe src="{{url('laravel-filemanager')}}?serial=1"
                              style="width: 100%; height: 500px; overflow: hidden; border: none;"></iframe>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-12">
                  {{-- START: slider Part --}}
                  <div class="row">
                    <div class="col-12">
                      <div class="form-group">
                        <label for="">Slider Images ** </label>
                        <br>
                        <div class="slider-thumbs" id="sliderThumbs2">

                        </div>

                        <input id="fileInput2" type="hidden" name="slider" value="" />
                        <button id="chooseImage2" class="choose-image btn btn-primary" type="button"
                          data-multiple="true" data-toggle="modal" data-target="#lfmModal2">Choose Images</button>


                        <p class="text-warning mb-0">JPG, PNG, JPEG images are allowed</p>
                        <p id="errslider" class="mb-0 text-danger em"></p>

                        <!-- slider LFM Modal -->
                        <div class="modal fade lfm-modal" id="lfmModal2" tabindex="-1" role="dialog"
                          aria-labelledby="lfmModalTitle" aria-hidden="true">
                          <i class="fas fa-times-circle"></i>
                          <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                            <div class="modal-content">
                              <div class="modal-body p-0">
                                <iframe id="lfmIframe2" src="{{url('laravel-filemanager')}}?serial=2"
                                  style="width: 100%; height: 500px; overflow: hidden; border: none;"></iframe>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  {{-- END: slider Part --}}
                </div>
              </div>
              <div class="row">
                <div class="col-lg-6">
                  <div class="form-group">
                    <label for="">Language **</label>
                    <select id="language" name="language_id" class="form-control">
                      <option value="" selected disabled>Select a language</option>
                      @foreach ($langs as $lang)
                      <option value="{{$lang->id}}">{{$lang->name}}</option>
                      @endforeach
                    </select>
                    <p id="errlanguage_id" class="mb-0 text-danger em"></p>
                  </div>
                </div>
                <div class="col-lg-6">
                  <div class="form-group">
                    <label for="">Serial Number **</label>
                    <input type="number" class="form-control ltr" name="serial_number" value=""
                      placeholder="Enter Serial Number">
                    <p id="errserial_number" class="mb-0 text-danger em"></p>
                    <p class="text-warning mb-0"><small>The higher the serial number is, the later the portfolio will be
                        shown.</small></p>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-lg-12">
                  <div class="form-group">
                    <label for="">Title **</label>
                    <input type="text" class="form-control" name="title" value="" placeholder="Enter title">
                    <p id="errtitle" class="mb-0 text-danger em"></p>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-lg-6">
                  <div class="form-group">
                    <label for="">Status **</label>
                    <select class="form-control ltr" name="status">
                      <option value="" selected disabled>Select a status</option>
                      <option value="In Progress">In Progress</option>
                      <option value="Completed">Completed</option>
                    </select>
                    <p id="errstatus" class="mb-0 text-danger em"></p>
                  </div>
                </div>
                <div class="col-lg-6">
                  <div class="form-group">
                    <label for="">Client Name **</label>
                    <input type="text" class="form-control" name="client_name" value="" placeholder="Enter client name">
                    <p id="errclient_name" class="mb-0 text-danger em"></p>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-lg-6">
                  <div class="form-group">
                    <label for="">Service **</label>
                    <select id="services" class="form-control" name="service_id" disabled>
                      <option value="" selected disabled>Select a service</option>
                      @foreach ($services as $key => $service)
                      <option value="{{$service->id}}">{{$service->title}}</option>
                      @endforeach
                    </select>
                    <p id="errservice_id" class="mb-0 text-danger em"></p>
                  </div>
                </div>
                <div class="col-lg-6">
                  <div class="form-group">
                    <label for="">Tags **</label>
                    <input type="text" class="form-control" name="tags" value="" data-role="tagsinput"
                      placeholder="Enter tags">
                    <p id="errtags" class="mb-0 text-danger em"></p>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-lg-6">
                  <div class="form-group">
                    <label for="">Start Date</label>
                    <input id="startDate" type="text" class="form-control datepicker" name="start_date" value=""
                      placeholder="Enter start date" autocomplete="off">
                    <p id="errstart_date" class="mb-0 text-danger em"></p>
                  </div>
                </div>
                <div class="col-lg-6">
                  <div class="form-group">
                    <label for="">Submission Date</label>
                    <input id="submissionDate" type="text" class="form-control datepicker" name="submission_date"
                      value="" placeholder="Enter submission date" autocomplete="off">
                    <p id="errsubmission_date" class="mb-0 text-danger em"></p>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-lg-12">
                  <div class="form-group">
                    <label for="">Website Link</label>
                    <input type="url" class="form-control" name="website_link" placeholder="Enter website link">
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-lg-12">
                  <div class="form-group">
                    <label for="">Content **</label>
                    <textarea id="portfolioContent" class="form-control summernote" id="summernote1" name="content"
                      placeholder="Enter content" data-height="300"></textarea>
                    <p id="errcontent" class="mb-0 text-danger em"></p>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label>Meta Keywords</label>
                <input class="form-control" name="meta_keywords" value="" placeholder="Enter meta keywords"
                  data-role="tagsinput">
              </div>
              <div class="form-group">
                <label>Meta Description</label>
                <textarea class="form-control" name="meta_description" rows="5"
                  placeholder="Enter meta description"></textarea>
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
    // {{url('laravel-filemanager')}}?serial=2

        $("input.note-image-input").on('change', function(e) {
            e.preventDefault();
            console.log('changed');
        });

       // services load according to language selection
       $("select[name='language_id']").on('change', function() {

           $("#services").removeAttr('disabled');

           let langid = $(this).val();
           let url = "{{url('/')}}/admin/portfolio/" + langid + "/getservices";
           // console.log(url);
           $.get(url, function(data) {
               // console.log(data);
               let options = `<option value="" disabled selected>Select a service</option>`;
               for (let i = 0; i < data.length; i++) {
                   options += `<option value="${data[i].id}">${data[i].title}</option>`;
               }
               $("#services").html(options);

           });
       });


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
                   $("form .summernote").siblings('.note-editor').find('.note-editable').removeClass('rtl text-right');
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


   // myDropzone is the configuration for the element that has an id attribute
   // with the value my-dropzone (or myDropzone)
   Dropzone.options.myDropzone = {
     acceptedFiles: '.png, .jpg, .jpeg',
     url: "{{route('admin.portfolio.sliderstore')}}",
     maxFilesize: 2, // specify the number of MB you want to limit here
     success : function(file, response){
         console.log(response.file_id);
         $("#sliders").append(`<input type="hidden" name="slider_images[]" id="slider${response.file_id}" value="${response.file_id}">`);
         // Create the remove button
         var removeButton = Dropzone.createElement("<button class='rmv-btn'><i class='fa fa-times'></i></button>");

         // Capture the Dropzone instance as closure.
         var _this = this;

         // Listen to the click event
         removeButton.addEventListener("click", function(e) {
           // Make sure the button click doesn't submit the form:
           e.preventDefault();
           e.stopPropagation();
           _this.removeFile(file);
           rmvimg(response.file_id);
         });

         // Add the button to the file preview element.
         file.previewElement.appendChild(removeButton);

         if(typeof response.error != 'undefined') {
           if (typeof response.file != 'undefined') {
             document.getElementById('errpreimg').innerHTML = response.file[0];
           }
         }
     }
   };

   function rmvimg(fileid) {
       // If you want to the delete the file on the server as well,
       // you can do the AJAX request here.

         $.ajax({
           url: "{{route('admin.portfolio.sliderrmv')}}",
           type: 'POST',
           data: {
             _token: "{{csrf_token()}}",
             fileid: fileid
           },
           success: function(data) {
             $("#slider"+fileid).remove();
           }
         });

   }

   var today = new Date();
   $("#submissionDate").datepicker({
     autoclose: true,
     endDate : today,
     todayHighlight: true
   });
   $("#startDate").datepicker({
     autoclose: true,
     endDate : today,
     todayHighlight: true
   });
</script>
@endsection
