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
   <h4 class="page-title">Donations</h4>
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
         <a href="#">Donation Page</a>
      </li>
      <li class="separator">
         <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
         <a href="#">Donations</a>
      </li>
   </ul>
</div>
<div class="row">
   <div class="col-md-12">
      <div class="card">
         <div class="card-header">
            <div class="row">
               <div class="col-lg-4">
                  <div class="card-title d-inline-block">Donations</div>
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
                  <a href="#" class="btn btn-primary float-right btn-sm" data-toggle="modal" data-target="#createModal" style="color: white !important;"><i class="fas fa-plus"></i> Add Cause</a>
                  <button class="btn btn-danger float-right btn-sm mr-2 d-none bulk-delete" data-href="{{route('admin.donation.bulk.delete')}}"><i class="flaticon-interface-5"></i> Delete</button>
               </div>
            </div>
         </div>
         <div class="card-body">
            <div class="row">
               <div class="col-lg-12">
                  @if (count($donations) == 0)
                  <h3 class="text-center">NO DONATION FOUND</h3>
                  @else
                  <div class="table-responsive">
                     <table class="table table-striped mt-3" id="basic-datatables">
                        <thead>
                           <tr>
                              <th scope="col">
                                 <input type="checkbox" class="bulk-check" data-val="all">
                              </th>
                              <th scope="col">Image</th>
                              <th scope="col">Title</th>
                               <th scope="col">Goal Amount</th>
                               <th scope="col">Raised Amount</th>
                               <th scope="col">Minimum Amount</th>
                              <th scope="col">Actions</th>
                           </tr>
                        </thead>
                        <tbody>
                           @foreach ($donations as $key => $donation)
                           <tr>
                              <td>
                                 <input type="checkbox" class="bulk-check" data-val="{{$donation->id}}">
                              </td>
                              <td><img src="{{asset('assets/front/img/donations/'.$donation->image)}}" alt="" width="80"></td>

                              <td>{{convertUtf8(strlen($donation->title)) > 30 ? convertUtf8(substr($donation->title, 0, 30)) . '...' : convertUtf8($donation->title)}}</td>

                              <td>{{$bex->base_currency_symbol_position == 'left' ? $bex->base_currency_symbol : ''}} {{convertUtf8($donation->goal_amount)}} {{$bex->base_currency_symbol_position == 'right' ? $bex->base_currency_symbol : ''}}</td>

                              <td>{{$bex->base_currency_symbol_position == 'left' ? $bex->base_currency_symbol : ''}} {{convertUtf8($donation->raised_amount)}} {{$bex->base_currency_symbol_position == 'right' ? $bex->base_currency_symbol : ''}}</td>

                              <td>{{$bex->base_currency_symbol_position == 'left' ? $bex->base_currency_symbol : ''}} {{convertUtf8($donation->min_amount)}} {{$bex->base_currency_symbol_position == 'right' ? $bex->base_currency_symbol : ''}}</td>

                              <td>
                                 <a class="btn btn-secondary btn-sm" href="{{route('admin.donation.edit', $donation->id) . '?language=' . request()->input('language')}}">
                                 <span class="btn-label">
                                 <i class="fas fa-edit"></i>
                                 </span>
                                 Edit
                                 </a>
                                 <form class="deleteform d-inline-block" action="{{route('admin.donation.delete')}}" method="post">
                                    @csrf
                                    <input type="hidden" name="donation_id" value="{{$donation->id}}">
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
            <h5 class="modal-title" id="exampleModalLongTitle">Add Donation</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body">

            <form id="ajaxForm" class="modal-form" action="{{route('admin.donation.store')}}" method="POST">
               @csrf
               {{-- Image Part --}}
               <div class="form-group">
                   <label for="">Image ** </label>
                   <br>
                   <div class="thumb-preview" id="thumbPreview1">
                       <img src="{{asset('assets/admin/img/noimage.jpg')}}" alt="User Image">
                   </div>
                   <br>
                   <br>


                   <input id="fileInput1" type="hidden" name="image">
                   <button id="chooseImage1" class="choose-image btn btn-primary" type="button" data-multiple="false" data-toggle="modal" data-target="#lfmModal1">Choose Image</button>


                   <p class="text-warning mb-0">JPG, PNG, JPEG, SVG images are allowed</p>
                   <p class="em text-danger mb-0" id="errimage"></p>

               </div>

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
                  <label for="">Content</label>
                  <textarea class="form-control summernote" name="content" rows="8" cols="80" placeholder="Enter content"></textarea>
                  <p id="errcontent" class="mb-0 text-danger em"></p>
               </div>
               <div class="form-group">
                  <label for="">Goal Amount (in {{$abx->base_currency_text}}) **</label>
                  <input type="number" class="form-control ltr" name="goal_amount" value="" placeholder="Enter Amount" required>
                  <p id="errgoal_amount" class="mb-0 text-danger em"></p>
               </div>
                <div class="form-group">
                    <label for="">Minimum Amount (in {{$abx->base_currency_text}}) **</label>
                    <input type="number" class="form-control ltr" name="min_amount" value="" placeholder="Enter Amount" required>
                    <small class="text-warning">Minimum amount for this cause</small>
                    <p id="errmin_amount" class="mb-0 text-danger em"></p>
                </div>
                <div class="form-group">
                    <label for="">Custom Amount (in {{$abx->base_currency_text}})</label>
                    <input type="text" class="form-control" name="custom_amount" value="" data-role="tagsinput">
                    <small class="text-warning">Use comma (,) to seperate the amounts.</small><br>
                    <small class="text-warning">Custom amount must be equal to or greater than minimum amount</small>
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
@endsection
@section('scripts')
<script>
   $(document).ready(function() {
       {{--$("select[name='language_id']").on('change', function() {--}}
       {{--    $("#bcategory").removeAttr('disabled');--}}
       {{--    let langid = $(this).val();--}}
       {{--    let url = "{{url('/')}}/admin/event/" + langid + "/get-categories";--}}
       {{--    $.get(url, function(data) {--}}
       {{--        console.log(data);--}}
       {{--        let options = `<option value="" disabled selected>Select a category</option>`;--}}
       {{--        for (let i = 0; i < data.length; i++) {--}}
       {{--            options += `<option value="${data[i].id}">${data[i].name}</option>`;--}}
       {{--        }--}}
       {{--        $("#bcategory").html(options);--}}

       {{--    });--}}
       {{--});--}}

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
   });
</script>
@endsection
