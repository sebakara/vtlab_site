@extends("front.$version.layout")

@section('pagename')
    - {{__('Causes')}}
@endsection

@section('meta-keywords', "$be->causes_meta_keywords")
@section('meta-description', "$be->causes_meta_description")

@section('breadcrumb-title', $bs->cause_title)
@section('breadcrumb-subtitle', $bs->cause_subtitle)
@section('breadcrumb-link', __('Causes'))

@section('content')
    <!--====== Start charity-causes Section ======-->
    <section class="charity-causes pt-120 pb-50">
        <div class="container">
            <div class="row">
                @if(count($causes) > 0)
                    @foreach($causes as $cause)
                        <div class="col-lg-4 col-md-6 col-sm-12">
                            <div class="causes-box mb-40">
                                <div class="causes-img">
                                    @if(!empty($cause->image))
                                        <img class="lazy" data-src="{{asset('/assets/front/img/donations/'.$cause->image)}}" alt="">
                                    @endif
                                    <div class="causes-overlay">
                                        <div class="goal">
                                            <span>{{__('Goal')}}<br>{{$bex->base_currency_symbol_position == 'left' ? $bex->base_currency_symbol : ''}}{{convertUtf8($cause->goal_amount)}}{{$bex->base_currency_symbol_position == 'right' ? $bex->base_currency_symbol : ''}}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="causes-content">
                                    <div class="single-progress">
                                        <div class="single-progress-bar">
                                            <div class="progress-bar-inner width-20" data-aos="fade-right" style="width: {{$cause->goal_percentage == 0 ? 4 : $cause->goal_percentage}}%">
                                                <div class="progress-bar-style">{{$cause->goal_percentage}}%</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="content-info">
                                        <h3><a href="{{route('front.cause_details', [$cause->slug])}}">{{strlen($cause->title) > 30 ? mb_substr($cause->title, 0, 30, 'utf-8') . '...' : $cause->title}}</a></h3>
                                        <div class="causes-meta">
                                            <p><span>{{__('Goal')}}</span>- {{$bex->base_currency_symbol_position == 'left' ? $bex->base_currency_symbol : ''}}{{convertUtf8($cause->goal_amount)}}{{$bex->base_currency_symbol_position == 'right' ? $bex->base_currency_symbol : ''}}</p>
                                            <p><span>{{__('Raised')}}</span>- {{$bex->base_currency_symbol_position == 'left' ? $bex->base_currency_symbol : ''}}{{convertUtf8($cause->raised_amount)}}{{$bex->base_currency_symbol_position == 'right' ? $bex->base_currency_symbol : ''}}</p>
                                        </div>
                                        <p>{!! (strlen(strip_tags($cause->content)) > 100) ? mb_substr(strip_tags($cause->content), 0, 100, 'utf-8') . '...' : strip_tags($cause->content) !!}</p>
                                        <a href="{{route('front.cause_details', [$cause->slug])}}" class="readmore-btn"><span>{{__('Read More')}}</span></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
            <div class="row">
                <div class="col-md-12">
                    <nav class="pagination-nav {{$causes->total() > 1 ? 'mb-4' : ''}}">
                        {{$causes->appends(['language' => request()->input('language')])->links()}}
                    </nav>
                </div>
            </div>
        </div>
    </section><!--====== End charity-causes Section ======-->
    @endsection
