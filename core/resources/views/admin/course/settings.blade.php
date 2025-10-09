@extends('admin.layout')
@section('content')

<div class="page-header">
   <h4 class="page-title">Settings</h4>
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
         <a href="#">Courses</a>
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
            <div class="card-title d-inline-block">Settings</div>
            <a class="btn btn-info btn-sm float-right d-inline-block" href="{{route('admin.course.index') . '?language=' . request()->input('language')}}">
            <span class="btn-label">
            <i class="fas fa-backward" style="font-size: 12px;"></i>
            </span>
            Back
            </a>
         </div>
         <div class="card-body pt-5 pb-5">
            <div class="row">
               <div class="col-lg-6 offset-lg-3">
                  <form id="settingsForm" class="" action="{{route('admin.course.settings')}}" method="post" enctype="multipart/form-data">
                     @csrf
                     <div class="form-group">
                        <label>Course **</label>
                        <div class="selectgroup w-100">
                              <label class="selectgroup-item">
                                  <input type="radio" name="is_course" value="1" class="selectgroup-input" {{$abex->is_course == 1 ? 'checked' : ''}}>
                                  <span class="selectgroup-button">Active</span>
                              </label>
                              <label class="selectgroup-item">
                                  <input type="radio" name="is_course" value="0" class="selectgroup-input" {{$abex->is_course == 0 ? 'checked' : ''}}>
                                  <span class="selectgroup-button">Deactive</span>
                              </label>
                        </div>
                        <p class="text-warning mb-0">By enabling / disabling, you can completely enable / disable the relevant pages of Course Module.</p>
                     </div>
                     <div class="form-group">
                        <label>Rating System **</label>
                        <div class="selectgroup w-100">
                              <label class="selectgroup-item">
                                  <input type="radio" name="is_course_rating" value="1" class="selectgroup-input" {{$abex->is_course_rating == 1 ? 'checked' : ''}}>
                                  <span class="selectgroup-button">Active</span>
                              </label>
                              <label class="selectgroup-item">
                                  <input type="radio" name="is_course_rating" value="0" class="selectgroup-input" {{$abex->is_course_rating == 0 ? 'checked' : ''}}>
                                  <span class="selectgroup-button">Deactive</span>
                              </label>
                        </div>
                     </div>
                  </form>
               </div>
            </div>
         </div>
         <div class="card-footer">
            <div class="form">
               <div class="form-group from-show-notify row">
                  <div class="col-12 text-center">
                     <button type="submit" form="settingsForm" class="btn btn-success">Submit</button>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
@endsection
