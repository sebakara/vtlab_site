@extends('admin.layout')

@section('content')
  <div class="page-header">
    <h4 class="page-title">Roles</h4>
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
        <a href="#">{{$role->name}}</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">Permissions Management</a>
      </li>
    </ul>
  </div>
  <div class="row">
    <div class="col-md-12">

      <div class="card">
        <div class="card-header">
          <div class="card-title d-inline-block">Permissions Management</div>
          <a class="btn btn-info btn-sm float-right d-inline-block" href="{{route('admin.role.index')}}">
            <span class="btn-label">
              <i class="fas fa-backward" style="font-size: 12px;"></i>
            </span>
            Back
          </a>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-lg-8 offset-lg-2">
              {{--  onsubmit="return false;" --}}
              <form id="permissionsForm" class="" action="{{route('admin.role.permissions.update')}}" method="post">
                {{csrf_field()}}
                <input type="hidden" name="role_id" value="{{Request::route('id')}}">

                @php
                  $permissions = $role->permissions;
                  if (!empty($role->permissions)) {
                    $permissions = json_decode($permissions, true);
                    // dd($permissions);
                  }
                @endphp

                <div class="form-group">
                  <label for="">Permissions: </label>
                	<div class="selectgroup selectgroup-pills mt-2">
                		<label class="selectgroup-item">
                			<input type="hidden" name="permissions[]" value="Dashboard" class="selectgroup-input">
                		</label>
                		<label class="selectgroup-item">
                			<input type="checkbox" name="permissions[]" value="Theme & Home" class="selectgroup-input" @if(is_array($permissions) && in_array('Theme & Home', $permissions)) checked @endif>
                			<span class="selectgroup-button">Theme & Home</span>
                		</label>
                		<label class="selectgroup-item">
                			<input type="checkbox" name="permissions[]" value="Menu Builder" class="selectgroup-input" @if(is_array($permissions) && in_array('Menu Builder', $permissions)) checked @endif>
                			<span class="selectgroup-button">Website Menu Builder</span>
                		</label>
                		<label class="selectgroup-item">
                			<input type="checkbox" name="permissions[]" value="Content Management" class="selectgroup-input" @if(is_array($permissions) && in_array('Content Management', $permissions)) checked @endif>
                			<span class="selectgroup-button">Content Management</span>
                		</label>
                		<label class="selectgroup-item">
                			<input type="checkbox" name="permissions[]" value="Pages" class="selectgroup-input" @if(is_array($permissions) && in_array('Pages', $permissions)) checked @endif>
                			<span class="selectgroup-button">Custom Pages</span>
                		</label>
                		<label class="selectgroup-item">
                			<input type="checkbox" name="permissions[]" value="Event Calendar" class="selectgroup-input" @if(is_array($permissions) && in_array('Event Calendar', $permissions)) checked @endif>
                			<span class="selectgroup-button">Event Calendar</span>
                		</label>
                		<label class="selectgroup-item">
                			<input type="checkbox" name="permissions[]" value="Package Management" class="selectgroup-input" @if(is_array($permissions) && in_array('Package Management', $permissions)) checked @endif>
                			<span class="selectgroup-button">Package Management</span>
                		</label>
                		<label class="selectgroup-item">
                			<input type="checkbox" name="permissions[]" value="Quote Management" class="selectgroup-input" @if(is_array($permissions) && in_array('Quote Management', $permissions)) checked @endif>
                			<span class="selectgroup-button">Quote Management</span>
                		</label>
                		<label class="selectgroup-item">
                			<input type="checkbox" name="permissions[]" value="Shop Management" class="selectgroup-input" @if(is_array($permissions) && in_array('Shop Management', $permissions)) checked @endif>
                			<span class="selectgroup-button">Shop Management</span>
                		</label>
                		<label class="selectgroup-item">
                			<input type="checkbox" name="permissions[]" value="Course Management" class="selectgroup-input" @if(is_array($permissions) && in_array('Course Management', $permissions)) checked @endif>
                			<span class="selectgroup-button">Course Management</span>
                		</label>
                		<label class="selectgroup-item">
                			<input type="checkbox" name="permissions[]" value="Events Management" class="selectgroup-input" @if(is_array($permissions) && in_array('Events Management', $permissions)) checked @endif>
                			<span class="selectgroup-button">Events Management</span>
                		</label>
                		<label class="selectgroup-item">
                			<input type="checkbox" name="permissions[]" value="Donation Management" class="selectgroup-input" @if(is_array($permissions) && in_array('Donation Management', $permissions)) checked @endif>
                			<span class="selectgroup-button">Donations & Causes</span>
                		</label>
                		<label class="selectgroup-item">
                			<input type="checkbox" name="permissions[]" value="Knowledgebase" class="selectgroup-input" @if(is_array($permissions) && in_array('Knowledgebase', $permissions)) checked @endif>
                			<span class="selectgroup-button">Knowledgebase</span>
                		</label>
                		<label class="selectgroup-item">
                			<input type="checkbox" name="permissions[]" value="Tickets" class="selectgroup-input" @if(is_array($permissions) && in_array('Tickets', $permissions)) checked @endif>
                			<span class="selectgroup-button">Support Tickets</span>
                		</label>

						<label class="selectgroup-item">
                			<input type="checkbox" name="permissions[]" value="RSS Feeds" class="selectgroup-input" @if(is_array($permissions) && in_array('RSS Feeds', $permissions)) checked @endif>
                			<span class="selectgroup-button">RSS Feeds</span>
						</label>
                		<label class="selectgroup-item">
                			<input type="checkbox" name="permissions[]" value="Users Management" class="selectgroup-input" @if(is_array($permissions) && in_array('Users Management', $permissions)) checked @endif>
                			<span class="selectgroup-button">Users Management</span>
                		</label>
                		<label class="selectgroup-item">
                			<input type="checkbox" name="permissions[]" value="Announcement Popup" class="selectgroup-input" @if(is_array($permissions) && in_array('Announcement Popup', $permissions)) checked @endif>
                			<span class="selectgroup-button">Announcement Popup</span>
                		</label>
                		<label class="selectgroup-item">
                			<input type="checkbox" name="permissions[]" value="Basic Settings" class="selectgroup-input" @if(is_array($permissions) && in_array('Basic Settings', $permissions)) checked @endif>
                			<span class="selectgroup-button">Settings</span>
                		</label>
                		<label class="selectgroup-item">
                			<input type="checkbox" name="permissions[]" value="Admins Management" class="selectgroup-input" @if(is_array($permissions) && in_array('Admins Management', $permissions)) checked @endif>
                			<span class="selectgroup-button">Admins Management</span>
                		</label>
                		<label class="selectgroup-item">
                			<input type="checkbox" name="permissions[]" value="Client Feedbacks" class="selectgroup-input" @if(is_array($permissions) && in_array('Client Feedbacks', $permissions)) checked @endif>
                			<span class="selectgroup-button">Client Feedbacks</span>
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
                <button type="submit" id="submitBtn" class="btn btn-success" onclick="document.getElementById('permissionsForm').submit();">Update</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

@endsection
