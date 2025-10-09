@extends('front.ecommerce.layout')

@section('meta-keywords', "$be->home_meta_keywords")
@section('meta-description', "$be->home_meta_description")

@section('styles')
@if (!empty($home->css))
<style>
    {!! replaceBaseUrl($home->css) !!}
</style>
@endif
@endsection

@section('content')
        <!--====== Start Banner Section ======-->
        <section class="banner-area">
            <div class="banner-wrapper d-flex">
                <div class="banner-left">
                    <div class="top-tools">
                        @if (!empty($socials))
                        <ul class="social-link">
                            @foreach ($socials as $key => $social)
                                <li><a target="_blank" href="{{$social->url}}"><i class="{{$social->icon}}"></i></a></li>
                            @endforeach
                        </ul>
                        @endif
                        <div class="hero-arrows"></div>
                    </div>
                </div>
                <div class="banner-right">
                    <!--   hero area start   -->
                    @if ($bs->home_version == 'static')
                        @includeif('front.ecommerce.partials.static')
                    @elseif ($bs->home_version == 'slider')
                        @includeif('front.ecommerce.partials.slider')
                    @elseif ($bs->home_version == 'video')
                        @includeif('front.ecommerce.partials.video')
                    @elseif ($bs->home_version == 'particles')
                        @includeif('front.ecommerce.partials.particles')
                    @elseif ($bs->home_version == 'water')
                        @includeif('front.ecommerce.partials.water')
                    @elseif ($bs->home_version == 'parallax')
                        @includeif('front.ecommerce.partials.parallax')
                    @endif
                </div>
            </div>
        </section><!--====== End Banner Section ======-->

        @if ($bs->feature_section == 1)
        <!--====== Start plus-features Section ======-->
        <section class="plus-features mt-100">
            <div class="custom-container">
                <div class="features-wrapper">
                    @foreach ($features as $key => $feature)
                        <div class="features-box d-flex align-items-center">
                            <div class="icon" style="color: #{{$feature->color}};">
                                <i class="{{$feature->icon}}"></i>
                            </div>
                            <div class="info">
                                <h5>{{$feature->title}}</h5>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section><!--====== End plus-features Section ======-->
        @endif

        @if (!empty($home->html))
        {!! convertHtml($home->html) !!}
        @else
          @includeIf('front.partials.pagebuilder-notice')
        @endif
@endsection