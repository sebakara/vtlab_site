@extends("front.$version.layout")

@section('pagename','- All Rss')

@section('meta-keywords', "$be->rss_meta_keywords")
@section('meta-description', "$be->rss_meta_description")

@section('breadcrumb-title', $be->rss_title)
@section('breadcrumb-subtitle', $be->rss_subtitle)
@section('breadcrumb-link', __('Latest RSS Feeds'))

@section('content')

  <!--    blog lists start   -->
  <div class="blog-lists section-padding">
     <div class="container">
        <div class="row">
           <div class="col-lg-8">
              <div class="row">
                @if (count($rss_posts) == 0)
                  <div class="col-md-12">
                    <div class="bg-light py-5">
                      <h3 class="text-center">{{__('NO FEED FOUND')}}</h3>
                    </div>
                  </div>
                @else
                  @foreach ($rss_posts as $key => $post)
                    <div class="col-md-6">
                       <div class="single-blog">
                          <div class="blog-img-wrapper">
                             <img class="lazy" data-src="{{$post->photo}}" alt="">
                          </div>
                          <div class="blog-txt">
                            @php
                                if (!empty($currentLang)) {
                                    $postDate = \Carbon\Carbon::parse($post->created_at)->locale("$currentLang->code");
                                } else {
                                    $postDate = \Carbon\Carbon::parse($post->created_at)->locale("en");
                                }

                                $postDate = $postDate->translatedFormat('jS F, Y');
                            @endphp
                             <p class="date"><small>{{__('By')}} <span class="username">{{$post->category->feed_name}}</span></small> | <small>{{$postDate}}</small> </p>

                             <h4 class="blog-title"><a href="{{route('front.rssdetails', [$post->slug, $post->id])}}">{{strlen($post->title) > 40 ? mb_substr($post->title, 0, 40, 'utf-8') . '...' : $post->title}}</a></h4>

                             <p class="blog-summary">{!! (strlen(strip_tags($post->description)) > 100) ? mb_substr(strip_tags($post->description), 0, 100, 'utf-8') . '...' : strip_tags($post->description) !!}</p>

                             <a href="{{route('front.rssdetails', [$post->slug, $post->id])}}" class="readmore-btn"><span>{{__('Read More')}}</span></a>

                          </div>
                       </div>
                    </div>
                  @endforeach
                @endif
              </div>
              @if ($rss_posts->total() > 4)
                <div class="row">
                   <div class="col-md-12">
                      <nav class="pagination-nav {{$rss_posts->total() > 6 ? 'mb-4' : ''}}">
                        {{$rss_posts->appends(['id' => request()->input('id')])->links()}}
                      </nav>
                   </div>
                </div>
              @endif
           </div>
           <!--    blog sidebar section start   -->
           <div class="col-lg-4">
              <div class="sidebar">
                 <div class="blog-sidebar-widgets category-widget">
                    <div class="category-lists job">
                       <h4>{{__('Categories')}}</h4>
                       <ul>
                          <li class="single-category {{empty(request()->input('id')) ? 'active' : ''}}"><a href="{{route('front.rss')}}">{{__('All')}}</a></li>
                          @foreach ($categories as $key => $rcat)
                           <li class="single-category {{$rcat->id == request()->input('id') ? 'active' : ''}}"><a href="{{route('front.rss',['id' => $rcat->id])}}">{{convertUtf8($rcat->feed_name)}}</a></li>
                          @endforeach
                       </ul>
                    </div>
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
           <!--    blog sidebar section end   -->
        </div>
     </div>
  </div>
  <!--    blog lists end   -->
@endsection
