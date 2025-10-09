@extends("front.$version.layout")

@section('pagename')
 - {{__('Service')}} - {{convertUtf8($service->title)}}
@endsection

@section('meta-keywords', "$service->meta_keywords")
@section('meta-description', "$service->meta_description")

@section('breadcrumb-title', convertUtf8($bs->service_details_title))
@section('breadcrumb-subtitle', convertUtf8($service->title))
@section('breadcrumb-link', __('Service Details'))

@section('content')


  <!--    services details section start   -->
  <div class="pt-115 pb-110 service-details-section">
     <div class="container">
        <div class="row">
           <div class="{{$service->sidebar == 1 ? 'col-lg-7' : 'col-12'}}">
              <div class="service-details">
                {!! replaceBaseUrl(convertUtf8($service->content)) !!}
              </div>
           </div>
           <!--    service sidebar start   -->
           @if ($service->sidebar == 1)
            <div class="col-lg-4">
                <div class="blog-sidebar-widgets">
                    <div class="searchbar-form-section">
                    <form action="{{route('front.services')}}">
                        <div class="searchbar">
                            <input name="category" type="hidden" value="{{request()->input('category')}}">
                            <input name="term" type="text" placeholder="{{__('Search Services')}}" value="{{request()->input('term')}}">
                            <button type="submit"><i class="fa fa-search"></i></button>
                        </div>
                    </form>
                    </div>
                </div>
                @if (serviceCategory())
                <div class="blog-sidebar-widgets category-widget">
                    <div class="category-lists job">
                    <h4>{{__('Categories')}}</h4>
                    <ul>
                        @foreach ($scats as $key => $scat)
                            <li class="single-category {{(!empty($service->scategory) && $service->scategory->id == $scat->id) ? 'active' : ''}}"><a href="{{route('front.services', ['category' => $scat->id, 'term'=>request()->input('term')])}}">{{convertUtf8($scat->name)}}</a></li>
                        @endforeach
                    </ul>
                    </div>
                </div>
                @endif
                <div class="subscribe-section">
                    <span>{{__('SUBSCRIBE')}}</span>
                    <h3>{{__('SUBSCRIBE FOR NEWSLETTER')}}</h3>
                    <form id="subscribeForm" class="subscribe-form" action="{{route('front.subscribe')}}" method="POST">
                    @csrf
                    <div class="form-element"><input name="email" type="email" placeholder="{{__('Email')}}"></div>
                    <p id="erremail" class="text-danger mb-3 err-email"></p>
                    <div class="form-element"><input type="submit" value="{{__('Subscribe')}}"></div>
                    </form>
                </div>
            </div>
           @endif
           <!--    service sidebar end   -->
        </div>
     </div>
  </div>
  <!--    services details section end   -->

@endsection
