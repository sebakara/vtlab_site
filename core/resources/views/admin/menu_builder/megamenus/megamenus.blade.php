@extends('admin.layout')

@section('content')
  <div class="page-header">
    <h4 class="page-title">Mega Menus Management</h4>
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
        <a href="#">Webiste Menu Builder</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">Mega Menus Management</a>
      </li>
    </ul>
  </div>
  <div class="row">
    <div class="col-md-12">

      <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-lg-4">
                    <div class="card-title d-inline-block">Mega Menus</div>
                </div>
                <div class="col-lg-3 offset-lg-5">
                    @if (!empty($langs))
                        <select name="language" class="form-control" onchange="window.location='{{url()->current() . '?language='}}'+this.value">
                            <option value="" selected disabled>Select a Language</option>
                            @foreach ($langs as $lang)
                                <option value="{{$lang->code}}" {{$lang->code == request()->input('language') ? 'selected' : ''}}>{{$lang->name}}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
            </div>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-lg-12">

                <div class="table-responsive">
                  <table class="table table-striped mt-3">
                    <thead>
                      <tr>
                        <th scope="col">Type</th>
                        <th scope="col">Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                        <tr>
                          <td>Services</td>
                          <td>
                            <a class="btn btn-secondary btn-sm" href="{{route('admin.megamenu.edit', ['language' => request()->input('language'), 'type' => 'services'])}}">
                              <span class="btn-label">
                                <i class="fas fa-edit"></i>
                              </span>
                              Edit
                            </a>
                          </td>
                        </tr>
                        <tr>
                          <td>Portfolios</td>
                          <td>
                            <a class="btn btn-secondary btn-sm" href="{{route('admin.megamenu.edit', ['language' => request()->input('language'), 'type' => 'portfolios'])}}">
                              <span class="btn-label">
                                <i class="fas fa-edit"></i>
                              </span>
                              Edit
                            </a>
                          </td>
                        </tr>
                        <tr>
                          <td>Products</td>
                          <td>
                            <a class="btn btn-secondary btn-sm" href="{{route('admin.megamenu.edit', ['language' => request()->input('language'), 'type' => 'products'])}}">
                              <span class="btn-label">
                                <i class="fas fa-edit"></i>
                              </span>
                              Edit
                            </a>
                          </td>
                        </tr>
                        <tr>
                          <td>Courses</td>
                          <td>
                            <a class="btn btn-secondary btn-sm" href="{{route('admin.megamenu.edit', ['language' => request()->input('language'), 'type' => 'courses'])}}">
                              <span class="btn-label">
                                <i class="fas fa-edit"></i>
                              </span>
                              Edit
                            </a>
                          </td>
                        </tr>
                        <tr>
                          <td>Causes</td>
                          <td>
                            <a class="btn btn-secondary btn-sm" href="{{route('admin.megamenu.edit', ['language' => request()->input('language'), 'type' => 'causes'])}}">
                              <span class="btn-label">
                                <i class="fas fa-edit"></i>
                              </span>
                              Edit
                            </a>
                          </td>
                        </tr>
                        <tr>
                          <td>Events</td>
                          <td>
                            <a class="btn btn-secondary btn-sm" href="{{route('admin.megamenu.edit', ['language' => request()->input('language'), 'type' => 'events'])}}">
                              <span class="btn-label">
                                <i class="fas fa-edit"></i>
                              </span>
                              Edit
                            </a>
                          </td>
                        </tr>
                        <tr>
                          <td>Blogs</td>
                          <td>
                            <a class="btn btn-secondary btn-sm" href="{{route('admin.megamenu.edit', ['language' => request()->input('language'), 'type' => 'blogs'])}}">
                              <span class="btn-label">
                                <i class="fas fa-edit"></i>
                              </span>
                              Edit
                            </a>
                          </td>
                        </tr>
                    </tbody>
                  </table>
                </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

@endsection
