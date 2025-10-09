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
                        <form action="{{route('admin.homeSettings.update')}}" id="themeForm" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>Home Page - Page Builder **</label>
                                        <div class="selectgroup w-100">
                                            <label class="selectgroup-item">
                                                <input type="radio" name="home_page_pagebuilder" value="1" class="selectgroup-input" {{$abex->home_page_pagebuilder == 1 ? 'checked' : ''}}>
                                                <span class="selectgroup-button">Active</span>
                                            </label>
                                            <label class="selectgroup-item">
                                                <input type="radio" name="home_page_pagebuilder" value="0" class="selectgroup-input" {{$abex->home_page_pagebuilder == 0 ? 'checked' : ''}}>
                                                <span class="selectgroup-button">Deactive</span>
                                            </label>
                                        </div>
                                        <p class="text-warning mb-0">If <strong class="text-success">Active</strong>, then <strong>Content of Home Page Builder</strong> will be shown in <strong>Website's Home Page</strong></p>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="">Select a Theme **</label>
                                        <select class="form-control" name="theme_version">
                                            <option value="" selected disabled>Select a Theme</option>
                                            <option value="default" {{$abe->theme_version == 'default' ? 'selected' : ''}}>Default</option>
                                            <option value="dark" {{$abe->theme_version == 'dark' ? 'selected' : ''}}>Dark</option>
                                            <option value="gym" {{$abe->theme_version == 'gym' ? 'selected' : ''}}>Gym</option>
                                            <option value="car" {{$abe->theme_version == 'car' ? 'selected' : ''}}>Car</option>
                                            <option value="cleaning" {{$abe->theme_version == 'cleaning' ? 'selected' : ''}}>Cleaning</option>
                                            <option value="construction" {{$abe->theme_version == 'construction' ? 'selected' : ''}}>Construction</option>
                                            <option value="logistic" {{$abe->theme_version == 'logistic' ? 'selected' : ''}}>Logistic</option>
                                            <option value="lawyer" {{$abe->theme_version == 'lawyer' ? 'selected' : ''}}>Lawyer</option>
                                            <option value="ecommerce" {{$abe->theme_version == 'ecommerce' ? 'selected' : ''}}>Ecommerce</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="">Select a Home Version</label>
                                        <select name="home_version" class="form-control">
                                            <option value="" selected disabled>Select a Home Version</option>
                                            <option value="static" {{$bs->home_version == 'static' ? 'selected' : ''}}>Static</option>
                                            <option value="slider" {{$bs->home_version == 'slider' ? 'selected' : ''}}>Slider</option>
                                            <option value="video" {{$bs->home_version == 'video' ? 'selected' : ''}}>Video</option>
                                            <option value="water" {{$bs->home_version == 'water' ? 'selected' : ''}}>Water</option>
                                            <option value="particles" {{$bs->home_version == 'particles' ? 'selected' : ''}}>Particles</option>
                                            <option value="parallax" {{$bs->home_version == 'parallax' ? 'selected' : ''}}>Parallax</option>
                                        </select>
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
