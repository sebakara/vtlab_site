@extends('front.gym.layout')

@section('meta-keywords', "$be->home_meta_keywords")
@section('meta-description', "$be->home_meta_description")

@section('styles')
@if (!empty($home->css))
<style>
    {!! replaceBaseUrl($home->css) !!}
</style>
@endif
@if (count($features) == 0)
<style>
.hero_slide_v1 .single_slider {
    padding: 346px 0 225px;
}
</style>
@endif
@endsection

@section('content')
        <!--   hero area start   -->
        @if ($bs->home_version == 'static')
            @includeif('front.gym.partials.static')
        @elseif ($bs->home_version == 'slider')
            @includeif('front.gym.partials.slider')
        @elseif ($bs->home_version == 'video')
            @includeif('front.gym.partials.video')
        @elseif ($bs->home_version == 'particles')
            @includeif('front.gym.partials.particles')
        @elseif ($bs->home_version == 'water')
            @includeif('front.gym.partials.water')
        @elseif ($bs->home_version == 'parallax')
            @includeif('front.gym.partials.parallax')
        @endif
        <!--   hero area end    -->



        <!-- Start finlance_feature section -->
        @if (count($features) > 0)
        <section class="finlance_feature feature_v1 {{count($features) == 0 ? 'mt-0' : ''}}">
            <div class="container-fluid">
                <div class="row no-gutters">
                    @foreach ($features as $key => $feature)
                        <div class="col-lg-3 col-md-6 col-sm-12">
                            <div class="grid_item text-center" style="background-color: #{{$feature->color}};">
                                <div class="grid_inner_item">
                                    <div class="finlance_icon">
                                        <i class="{{$feature->icon}}"></i>
                                    </div>
                                    <div class="finlance_content">
                                        <h3>{{convertUtf8($feature->title)}}</h3>
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
