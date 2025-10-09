@extends("front.$version.layout")

@section('styles')
    <link rel="stylesheet" href="{{asset('assets/front/css/nice-select.css')}}">
@endsection

@section('pagename')
    - {{__('Events')}}
@endsection

@section('meta-keywords', "$be->events_meta_keywords")
@section('meta-description', "$be->events_meta_description")

@section('breadcrumb-title', convertUtf8($bs->event_title))
@section('breadcrumb-subtitle', convertUtf8($bs->event_subtitle))
@section('breadcrumb-link', __('Events'))

@section('content')
    <section class="event-search-section">
        <div class="container">
          <form action="{{route('front.events')}}" method="GET" id="event-search">
            <div class="event-search-form pt-50 pb-50">
                <div class="row no-gutters">
                       <div class="col-lg-3">
                           <div class="form_group border-right">
                               <input type="text" class="form_control" name="title" placeholder="{{__('Event Name')}}" value="{{request()->input('title')}}">
                           </div>
                       </div>
                    <div class="col-lg-3">
                        <div class="form_group border-right">
                            <input type="text" class="form_control" name="location" placeholder="{{__('Location')}}" value="{{request()->input('location')}}">
                        </div>
                    </div>
                    <div class="col-lg-2">
                           <div class="form_group border-right">
                               <select name="category" onchange="document.getElementById('event-search').submit()">
                                   <option data-display="Catagory" value="">{{__('Choose an option')}}</option>
                                    @foreach($event_categories as $event_category)
                                        <option value="{{$event_category->id}}" {{request()->input('category') == $event_category->id ? 'selected' : ''}}>{{$event_category->name}}</option>
                                    @endforeach
                               </select>
                           </div>
                       </div>

                       <div class="col-lg-2">
                           <div class="form_group">
                               <input type="date" name="date" placeholder="dd-mm-yyyy" class="form_control" value="{{request()->input('date')}}" onchange="document.getElementById('event-search').submit()">
                           </div>
                       </div>
                       <div class="col-lg-2">
                           <div class="form_group">
                               <button class="main-btn" type="submit" style="height: 45px;justify-content: center;text-align: center;padding-top: 0;padding-bottom: 0;">
                                   {{__('Search')}}</button>
                           </div>
                       </div>

                </div>
            </div>
          </form>
        </div>
    </section><!--====== End Event-search Section ======-->
    <section class="event-area-section event-area-v2 bg_cover pt-130" id="event-filter">
        <div class="container">
            <div class="contact-form-section pt-0 pb-4">
                <div class="container">
                    <div class="row justify-content-center text-center">
                        <div class="col-lg-6">
                            <span class="section-title text-center">{{__('Events')}}</span>
                            <h2 class="section-summary px-0 text-center">{{__('Our Upcoming Events')}}</h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="filter_grid row">
                @if (count($events) > 0)
                    @foreach($events as $event)
                        <div class="col-lg-6 col-md-12 col-sm-12 grid-column cat1 cat4">
                            <div class="event-item">
                                <div class="event-img">
                                    @php
                                        $images = json_decode($event->image, true);
                                    @endphp
                                    <img data-src="{{(!empty($images)) ? asset('/assets/front/img/events/sliders/'.$images[0]) : ''}}" class="img-fluid lazy" alt="">
                                    <div class="event-overlay">
                                        <a href="{{route('front.event_details',[$event->slug])}}" class="main-btn">{{__('BUY TICKET')}}</a>
                                    </div>
                                </div>
                                <div class="event-content">
                                    <a class="cat c-1 base-bg text-white">{{$event->eventCategories->name}}</a>
                                    <a class="cat c-1" href="{{route('front.events', ['category' => $event->eventCategories->id])}}">
                                        {{$bex->base_currency_symbol_position == 'left' ? $bex->base_currency_symbol : ''}}{{$event->cost}}{{$bex->base_currency_symbol_position == 'right' ? $bex->base_currency_symbol : ''}}
                                    </a>
                                    <h3><a href="{{route('front.event_details',[$event->slug])}}">
                                        {{strlen($event->title) > 30 ? mb_substr($event->title,0,30,'utf-8') . '...' : $event->title}}
                                    </a></h3>
                                    <div class="event-meta">
                                        <span>{{$event->organizer}}</span>
                                        <span>{{date_format(date_create($event->date),"d/m/Y")}}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="col-12">
                        <div class="bg-secondary-py-5 text-center">
                            <h3>{{__('NO EVENT FOUND')}}</h3>
                        </div>
                    </div>
                @endif
            </div>
            <div class="row">
                <div class="col-md-12">
                    <nav class="pagination-nav {{$events->total() > 1 ? 'mb-4' : ''}}">
                        {{$events->appends(['title' => request()->input('title'),'location' => request()->input('location'),'category' => request()->input('category'),'date' => request()->input('date')])->links()}}
                    </nav>
                </div>
            </div>
        </div>
    </section><!--====== End Event-area Section ======-->
@endsection
@section('scripts')
    <script src="{{asset('/assets/front/js/jquery.nice-select.min.js')}}"></script>
@endsection
