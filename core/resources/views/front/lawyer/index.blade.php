@extends('front.lawyer.layout')

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
    <!--   hero area start   -->
    @if ($bs->home_version == 'static')
        @includeif('front.lawyer.partials.static')
    @elseif ($bs->home_version == 'slider')
        @includeif('front.lawyer.partials.slider')
    @elseif ($bs->home_version == 'video')
        @includeif('front.lawyer.partials.video')
    @elseif ($bs->home_version == 'particles')
        @includeif('front.lawyer.partials.particles')
    @elseif ($bs->home_version == 'water')
        @includeif('front.lawyer.partials.water')
    @elseif ($bs->home_version == 'parallax')
        @includeif('front.lawyer.partials.parallax')
    @endif
    <!--   hero area end    -->

    <!-- Start lawyer_feature section -->
    @if (count($features) > 0)
    <section class="lawyer_feature feature_v1">
        <div class="container">
            <div class="row no-gutters">
                @foreach ($features as $key => $feature)
                    <div class="col-lg-3 col-md-6 col-sm-12 single_feature">
                        <div class="grid_item">
                            <div class="grid_inner_item">
                                <div class="lawyer_icon">
                                    <i class="{{$feature->icon}}"></i>
                                </div>
                                <div class="lawyer_content">
                                    <h4>{{convertUtf8($feature->title)}}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif
    <!-- End lawyer_feature area -->

    @if (!empty($home->html))
    {!! convertHtml(convertUtf8($home->html)) !!}
    @else
      @includeIf('front.partials.pagebuilder-notice')
    @endif
@endsection
