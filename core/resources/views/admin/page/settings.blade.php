@extends('admin.layout')

@section('content')
<div class="page-header">
    <h4 class="page-title">Settings</h4>
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
            <a href="#">Theme & Home</a>
        </li>
        <li class="separator">
            <i class="flaticon-right-arrow"></i>
        </li>
        <li class="nav-item">
            <a href="#">Settings</a>
        </li>
    </ul>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3>Settings</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-6 offset-lg-3">
                        <form action="{{route('admin.page.updateSettings')}}" id="themeForm" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>Custom Page's Page Builder **</label>
                                        <div class="selectgroup w-100">
                                            <label class="selectgroup-item">
                                                <input type="radio" name="custom_page_pagebuilder" value="1" class="selectgroup-input" {{$abex->custom_page_pagebuilder == 1 ? 'checked' : ''}}>
                                                <span class="selectgroup-button">Active</span>
                                            </label>
                                            <label class="selectgroup-item">
                                                <input type="radio" name="custom_page_pagebuilder" value="0" class="selectgroup-input" {{$abex->custom_page_pagebuilder == 0 ? 'checked' : ''}}>
                                                <span class="selectgroup-button">Deactive</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="card-footer text-center">
                <button type="submit" class="btn btn-success" form="themeForm">Update</button>
            </div>
        </div>

    </div>
</div>



@endsection
