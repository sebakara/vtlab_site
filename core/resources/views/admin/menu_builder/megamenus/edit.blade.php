@extends('admin.layout')

@section('content')
    @php
    $type = request()->input('type');
    if($type == 'services') {
        $name = 'Services';
    } elseif($type == 'products') {
        $name = 'Products';
    } elseif($type == 'portfolios') {
        $name = 'Portfolios';
    } elseif($type == 'courses') {
        $name = 'Courses';
    } elseif($type == 'causes') {
        $name = 'Causes';
    } elseif($type == 'events') {
        $name = 'Events';
    } elseif($type == 'blogs') {
        $name = 'Blogs';
    } else {
        $name = '';
    }
    @endphp


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
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">Add {{$name}} to Mega Menu</a>
      </li>
    </ul>
  </div>
  <div class="row">
    <div class="col-md-12">

      <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-lg-4">
                    <div class="card-title d-inline-block">Add {{$name}} to Mega Menu</div>
                </div>
            </div>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-lg-8 offset-lg-2">

                <form action="{{route('admin.megamenu.update')}}" id="megaMenuForm" method="POST">
                    @csrf

                    <input type="hidden" name="language_id" value="{{$lang->id}}">
                    <input type="hidden" name="type" value="{{request()->input('type')}}">

                    @if (($type == 'services' && serviceCategory()) || ($type == 'products') || ($type == 'portfolios' && serviceCategory()) || ($type == 'courses') || ($type == 'events') || ($type == 'blogs'))
                        @foreach ($cats as $cat)
                            @php
                                $type = request()->input('type');
                                if($type == 'services') {
                                    $items = $cat->services;
                                } elseif($type == 'products') {
                                    $items = $cat->products;
                                } elseif($type == 'portfolios') {
                                    $services = $cat->services;
                                    $items = [];
                                    foreach ($services as $key => $service) {
                                        foreach ($service->portfolios as $key => $item) {
                                            $items[] = $item;
                                        }
                                    }
                                    $items = collect($items);
                                } elseif($type == 'courses') {
                                    $items = $cat->courses;
                                } elseif($type == 'events') {
                                    $items = $cat->events;
                                } elseif($type == 'blogs') {
                                    $items = $cat->blogs;
                                }
                            @endphp

                            <div class="form-group">
                                <label class="form-label">{{$cat->name}}</label>
                                <br>
                                <div class="selectgroup selectgroup-pills">
                                    @foreach ($items as $item)
                                        <label class="selectgroup-item">
                                            <input type="checkbox" name="items[]" value="{!! json_encode([$cat->id, $item->id]) !!}" class="selectgroup-input" {{array_key_exists("$cat->id", $mmenus) && in_array($item->id, $mmenus["$cat->id"]) ? 'checked' : ''}}>
                                            <span class="selectgroup-button">{{strlen($item->title) > 30 ? mb_substr($item->title,0,30,'utf-8') . '...' : $item->title}}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    @elseif ((!serviceCategory() && $type == 'services') || (!serviceCategory() && $type == 'portfolios') || ($type == 'causes'))
                        <div class="form-group">
                            <label class="form-label">{{$name}}</label>
                            <br>
                            <div class="selectgroup selectgroup-pills">
                                @foreach ($items as $item)
                                    <label class="selectgroup-item">
                                        <input type="checkbox" name="items[]" value="{{$item->id}}" class="selectgroup-input" {{in_array($item->id, $mmenus) ? 'checked' : ''}}>
                                        <span class="selectgroup-button">{{strlen($item->title) > 30 ? mb_substr($item->title,0,30,'utf-8') . '...' : $item->title}}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif

                </form>

            </div>
          </div>
        </div>

        <div class="card-footer text-center">
            <button class="btn btn-success" type="submit" form="megaMenuForm">Add to {{$name}} Mega Menu</button>
        </div>
      </div>
    </div>
  </div>

@endsection
