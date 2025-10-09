@extends('front.cleaning.layout')

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
        @includeif('front.cleaning.partials.static')
    @elseif ($bs->home_version == 'slider')
        @includeif('front.cleaning.partials.slider')
    @elseif ($bs->home_version == 'video')
        @includeif('front.cleaning.partials.video')
    @elseif ($bs->home_version == 'particles')
        @includeif('front.cleaning.partials.particles')
    @elseif ($bs->home_version == 'water')
        @includeif('front.cleaning.partials.water')
    @elseif ($bs->home_version == 'parallax')
        @includeif('front.cleaning.partials.parallax')
    @endif
    <!--   hero area end    -->



    <!-- CATAGORIES PART START -->
    @if (count($features) > 0)
    <section class="catagories-area pt-100 pb-100">
        <div class="container">
            <div class="catagories-carousel-active">
                <div class="row">
                    @foreach ($features as $key => $feature)
                        <div class="col-lg-3 col-md-6">
                            <div class="single-catagories-item text-center">
                                <span style="background: #{{$feature->color}}2a;"><i style="color: #{{$feature->color}};" class="{{$feature->icon}}"></i></span>
                                <h4>{{convertUtf8($feature->title)}}</h4>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
    @endif
    <!-- CATAGORIES PART END -->

    @if (!empty($home->html))
    {!! convertHtml(convertUtf8($home->html)) !!}
    @else
      @includeIf('front.partials.pagebuilder-notice')
    @endif


@endsection
