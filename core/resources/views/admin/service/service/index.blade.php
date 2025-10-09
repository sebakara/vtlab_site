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
    <h4 class="page-title">Services</h4>
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
            <a href="#">Service Page</a>
        </li>
        <li class="separator">
            <i class="flaticon-right-arrow"></i>
        </li>
        <li class="nav-item">
            <a href="#">Services</a>
        </li>
    </ul>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-lg-4">
                        <div class="card-title d-inline-block">Services</div>
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
                        <a href="#" class="btn btn-primary float-right btn-sm" data-toggle="modal" data-target="#createModal"><i class="fas fa-plus"></i> Add Service</a>
                        <button class="btn btn-danger float-right btn-sm mr-2 d-none bulk-delete" data-href="{{route('admin.service.bulk.delete')}}"><i class="flaticon-interface-5"></i> Delete</button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-12">
                        @if (count($services) == 0)
                        <h3 class="text-center">NO SERVICE FOUND</h3>
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
                                        @if (serviceCategory())
                                        <th scope="col">Category</th>
                                        @endif
                                        <th scope="col">Featured</th>
                                        <th scope="col">Serial Number</th>
                                        <th scope="col">Sidebar</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>


                                <tbody>
                                    @foreach ($services as $key => $service)
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="bulk-check" data-val="{{$service->id}}">
                                        </td>
                                        <td><img src="{{asset('assets/front/img/services/'.$service->main_image)}}" alt="" width="70"></td>
                                        <td>{{strlen(convertUtf8($service->title)) > 100 ? convertUtf8(substr($service->title, 0, 100)) . '...' : convertUtf8($service->title)}}</td>

                                        @if (serviceCategory())
                                        <td>
                                            @if (!empty($service->scategory))
                                            {{convertUtf8($service->scategory->name)}}
                                            @endif
                                        </td>
                                        @endif

                                        <td>
                                            <form id="featureForm{{$service->id}}" class="d-inline-block" action="{{route('admin.service.feature')}}" method="post">
                                                @csrf
                                                <input type="hidden" name="service_id" value="{{$service->id}}">
                                                <select class="form-control {{$service->feature == 1 ? 'bg-success' : 'bg-danger'}}" name="feature" onchange="document.getElementById('featureForm{{$service->id}}').submit();">
                                                    <option value="1" {{$service->feature == 1 ? 'selected' : ''}}>Yes</option>
                                                    <option value="0" {{$service->feature == 0 ? 'selected' : ''}}>No</option>
                                                </select>
                                            </form>
                                        </td>

                                        <td>{{$service->serial_number}}</td>
                                        <td>
                                            <form id="statusForm{{$service->id}}" class="d-inline-block" action="{{route('admin.service.sidebar')}}" method="post">
                                                @csrf
                                                <input type="hidden" name="service_id" value="{{$service->id}}">
                                                <select class="form-control form-control-sm
                                                @if ($service->sidebar == 1)
                                                bg-success
                                                @elseif ($service->sidebar == 0)
                                                bg-danger
                                                @endif
                                                " name="sidebar" onchange="document.getElementById('statusForm{{$service->id}}').submit();">
                                                <option value="1" {{$service->sidebar == 1 ? 'selected' : ''}}>Enabled</option>
                                                <option value="0" {{$service->sidebar == 0 ? 'selected' : ''}}>Disabled</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td>
                                        <a class="btn btn-secondary btn-sm" href="{{route('admin.service.edit', $service->id) . '?language=' . request()->input('language')}}">
                                            <span class="btn-label">
                                                <i class="fas fa-edit"></i>
                                            </span>
                                            Edit
                                        </a>
                                        <form class="deleteform d-inline-block" action="{{route('admin.service.delete')}}" method="post">
                                            @csrf
                                            <input type="hidden" name="service_id" value="{{$service->id}}">
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
<!-- Create Service Modal -->
<div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Add Service</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            <form id="ajaxForm" class="modal-form" action="{{route('admin.service.store')}}" method="POST">
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
                    <select id="language" name="language_id" class="form-control">
                        @foreach ($langs as $lang)
                        <option value="{{$lang->id}}">{{$lang->name}}</option>
                        @endforeach
                    </select>
                    <p id="errlanguage_id" class="mb-0 text-danger em"></p>
                </div>
                <div class="form-group">
                    <label for="">Title **</label>
                    <input type="text" class="form-control" name="title" placeholder="Enter title" value="">
                    <p id="errtitle" class="mb-0 text-danger em"></p>
                </div>
                @if (serviceCategory())
                <div class="form-group">
                    <label for="">Category **</label>
                    <select id="scategory" class="form-control" name="category" disabled>
                        <option value="" selected disabled>Select a category</option>
                    </select>
                    <p id="errcategory" class="mb-0 text-danger em"></p>
                </div>
                @endif

                <div class="form-group">
                    <label for="">Summary **</label>
                    <textarea class="form-control" name="summary" placeholder="Enter summary" rows="3"></textarea>
                    <p id="errsummary" class="mb-0 text-danger em"></p>
                </div>

                <div class="form-group">
                    <label>Details Page **</label>
                    <div class="selectgroup w-100">
                        <label class="selectgroup-item">
                            <input type="radio" name="details_page_status" value="1" class="selectgroup-input" checked>
                            <span class="selectgroup-button">Enable</span>
                        </label>
                        <label class="selectgroup-item">
                            <input type="radio" name="details_page_status" value="0" class="selectgroup-input">
                            <span class="selectgroup-button">Disable</span>
                        </label>
                    </div>
                    <p id="errdetails_page_status" class="mb-0 text-danger em"></p>
                </div>

                <div class="form-group" id="contentFg">
                    <label for="">Content **</label>
                    <textarea id="serviceContent" class="form-control summernote" name="content" data-height="300" placeholder="Enter content"></textarea>
                    <p id="errcontent" class="mb-0 text-danger em"></p>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label for="">Serial Number **</label>
                            <input type="number" class="form-control ltr" name="serial_number" value="" placeholder="Enter Serial Number">
                            <p id="errserial_number" class="mb-0 text-danger em"></p>
                            <p class="text-warning"><small>The higher the serial number is, the later the service will be shown everywhere.</small></p>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Meta Keywords</label>
                    <input class="form-control" name="meta_keywords" value="" placeholder="Enter meta keywords" data-role="tagsinput">
                    <p id="errmeta_keywords" class="mb-0 text-danger em"></p>
                </div>
                <div class="form-group">
                    <label>Meta Description</label>
                    <textarea class="form-control" name="meta_description" rows="5" placeholder="Enter meta description"></textarea>
                    <p id="errmeta_description" class="mb-0 text-danger em"></p>
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
    function toggleDetails() {
        let val = $("input[name='details_page_status']:checked").val();

        // if 'details page' is 'enable', then show 'content' & hide 'summary'
        if (val == 1) {
            $("#contentFg").show();
        }
        // if 'details page' is 'disable', then show 'summary' & hide 'content'
        else if (val == 0) {
            $("#contentFg").hide();
        }
    }

    $("input[name='details_page_status']").on('change', function() {
        toggleDetails();
    });
</script>

@if(serviceCategory())
<script>
    function loadCategories() {
        $("#scategory").removeAttr('disabled');
        let langid = $("select[name='language_id']").val();
        let url = "{{url('/')}}/admin/service/" + langid + "/getcats";
        // console.log(url);
        $.get(url, function(data) {
            console.log(data);
            let options = `<option value="" disabled selected>Select a category</option>`;
            for (let i = 0; i < data.length; i++) {
                options += `<option value="${data[i].id}">${data[i].name}</option>`;
            }
            $("#scategory").html(options);

        });
    }

    $(document).ready(function() {

        loadCategories();

        $("select[name='language_id']").on('change', function() {
            loadCategories();
        });

    });
</script>
@endif

<script>
    $(document).ready(function() {
        // make input fields RTL
        $("select[name='language_id']").on('change', function() {
            $(".request-loader").addClass("show");
            let url = "{{url('/')}}/admin/rtlcheck/" + $(this).val();
            console.log(url);
            $.get(url, function(data) {
                $(".request-loader").removeClass("show");
                if (data == 1) {
                    $("form.modal-form input").each(function() {
                        if (!$(this).hasClass('ltr')) {
                            $(this).addClass('rtl');
                        }
                    });
                    $("form.modal-form select").each(function() {
                        if (!$(this).hasClass('ltr')) {
                            $(this).addClass('rtl');
                        }
                    });
                    $("form.modal-form textarea").each(function() {
                        if (!$(this).hasClass('ltr')) {
                            $(this).addClass('rtl');
                        }
                    });
                    $("form.modal-form .summernote").each(function() {
                        $(this).siblings('.note-editor').find('.note-editable').addClass('rtl text-right');
                    });

                } else {
                    $("form.modal-form input, form.modal-form select, form.modal-form textarea").removeClass('rtl');
                    $("form.modal-form .summernote").siblings('.note-editor').find('.note-editable').removeClass('rtl text-right');
                }
            })
        });
    });
</script>
@endsection
