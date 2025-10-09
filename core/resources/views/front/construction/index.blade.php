@extends('front.construction.layout')

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
        @includeif('front.construction.partials.static')
    @elseif ($bs->home_version == 'slider')
        @includeif('front.construction.partials.slider')
    @elseif ($bs->home_version == 'video')
        @includeif('front.construction.partials.video')
    @elseif ($bs->home_version == 'particles')
        @includeif('front.construction.partials.particles')
    @elseif ($bs->home_version == 'water')
        @includeif('front.construction.partials.water')
    @elseif ($bs->home_version == 'parallax')
        @includeif('front.construction.partials.parallax')
    @endif
    <!--   hero area end    -->

    <!-- Start finlance_feature section -->
    @if (count($features) > 0)
    <section class="finlance_feature feature_v1">
        <div class="container-fluid">
            <div class="row no-gutters">
                @foreach ($features as $key => $feature)
                    <div class="col-lg-3 col-md-6 col-sm-12 single_feature">
                        <div class="grid_item">
                            <div class="grid_inner_item d-flex align-items-center">
                                <div class="finlance_icon">
                                    <i class="{{$feature->icon}}"></i>
                                </div>
                                <div class="finlance_content">
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
    <!-- End finlance_feature area -->

    @if (!empty($home->html))
    {!! convertHtml(convertUtf8($home->html)) !!}
    @else
      @includeIf('front.partials.pagebuilder-notice')
    @endif

@endsection
