@php
    $version = $be->theme_version;

    if ($version == 'dark') {
        $version = 'default';
    }
@endphp

@extends("front.$version.layout")

@section('breadcrumb-title', $bs->error_title)
@section('breadcrumb-subtitle', $bs->error_subtitle)
@section('breadcrumb-link', __('404'))

@section('content')


  <!--    Error section start   -->
  <div class="error-section">
     <div class="container">
        <div class="row">
           <div class="col-lg-6">
              <div class="not-found">
                 <img src="{{asset('assets/front/img/404.png')}}" alt="">
              </div>
           </div>
           <div class="col-lg-6">
              <div class="error-txt">
                 <div class="oops">
                    <img src="{{asset('assets/front/img/oops.png')}}" alt="">
                 </div>
                 <h2>You're lost...</h2>
                 <p>The page you are looking for might have been moved, renamed, or might never existed.</p>
                 <a href="{{route('front.index')}}" class="go-home-btn">Back Home</a>
              </div>
           </div>
        </div>
     </div>
  </div>
  <!--    Error section end   -->
@endsection
