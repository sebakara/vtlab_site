@extends("front.$version.layout")

@section('pagename')
- {{convertUtf8($portfolio->title)}}
@endsection

@section('meta-keywords', "$portfolio->meta_keywords")
@section('meta-description', "$portfolio->meta_description")

@section('breadcrumb-title', convertUtf8($bs->portfolio_details_title))
@section('breadcrumb-subtitle', convertUtf8($portfolio->title))
@section('breadcrumb-link', __('Portfolio Details'))

@section('content')
<!--    case details section start   -->
<div class="case-details-section">
  <div class="container">
    <div class="row">
      <div class="col-lg-7 col-xl-7">
        <div class="project-ss-carousel owl-carousel owl-theme common-carousel">
          @foreach ($portfolio->portfolio_images as $key => $pi)
          <a href="#" class="single-ss" data-id="{{$pi->id}}">
            <img class="lazy" data-src="{{asset('assets/front/img/portfolios/sliders/'.$pi->image)}}" alt="">
          </a>
          @endforeach
        </div>
        @foreach ($portfolio->portfolio_images as $key => $pi)
        <a id="singleMagnificSs{{$pi->id}}" class="single-magnific-ss d-none"
          href="{{asset('assets/front/img/portfolios/sliders/'.$pi->image)}}"></a>
        @endforeach
        <div class="case-details">
          {!! replaceBaseUrl(convertUtf8($portfolio->content)) !!}
        </div>
      </div>
      <!--    appoint section start   -->
      <div class="col-lg-5 offset-xl-1 col-xl-4">
        <div class="right-side">
          <div class="row">
            <div class="col-xl-12 col-lg-12 col-md-12">
              <div class="project-infos">
                <h3>{{convertUtf8($portfolio->title)}}</h3>
                <div class="row mb-2">
                  <div class="col-5 {{$rtl == 1 ? 'pl-0' : 'pr-0'}}"><strong>{{__('Client Name')}}</strong></div>
                  <div class="col-7"><span>:</span> {{convertUtf8($portfolio->client_name)}}</div>
                </div>
                <div class="row mb-2">
                  <div class="col-5 {{$rtl == 1 ? 'pl-0' : 'pr-0'}}"><strong>{{__('Service')}}</strong></div>
                  @if (!empty($portfolio->service->title))
                  <div class="col-7"><span>:</span> {{convertUtf8($portfolio->service->title)}}</div>
                  @endif
                </div>

                @if ($portfolio->start_date != null)
                  @php
                    $startDate = Carbon\Carbon::parse($portfolio->start_date);
                  @endphp
                  <div class="row mb-2">
                    <div class="col-5 {{$rtl == 1 ? 'pl-0' : 'pr-0'}}"><strong>{{__('Start Date')}}</strong></div>
                    <div class="col-7"><span>:</span> {{date_format($startDate, 'M d, Y')}}</div>
                  </div>
                @endif

                @if ($portfolio->submission_date != null)
                  @php
                    $submissionDate = Carbon\Carbon::parse($portfolio->submission_date);
                  @endphp
                  <div class="row mb-2">
                    <div class="col-5 {{$rtl == 1 ? 'pl-0' : 'pr-0'}}"><strong>{{__('End Date')}}</strong></div>
                    <div class="col-7"><span>:</span> {{date_format($submissionDate, 'M d, Y')}}</div>
                  </div>
                @endif

                <div class="row {{ $portfolio->website_link != null ? 'mb-2' : 'mb-0' }}">
                  <div class="col-5 {{$rtl == 1 ? 'pl-0' : 'pr-0'}}"><strong>{{__('Status')}}</strong></div>
                  <div class="col-7"><span>:</span> {{$portfolio->status}}</div>
                </div>

                @if ($portfolio->website_link != null)
                  <div class="row mb-0">
                    <div class="col-12">
                        <a href="{{$portfolio->website_link}}" class="btn base-bg text-white btn-sm" target="_blank">{{__('Live Demo')}}</a>
                    </div>
                  </div>
                @endif
              </div>
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
          </div>
        </div>
      </div>
      <!--    appoint section end   -->
    </div>
  </div>
</div>
<!--    case details section end   -->

@endsection
