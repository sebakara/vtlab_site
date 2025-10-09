@extends('front.ecommerce.layout')

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

        @if ($be->categories_section == 1)
        <!--====== Start plus-categories Section ======-->
        <section class="plus-categories pt-105 pb-100">
            <div class="custom-container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="section-title mb-20">
                            <h2>{{__('Featured Categories')}}</h2>
                        </div>
                    </div>
                </div>
                <div class="categories-slide">
                    @foreach ($fcategories as $category)
                    <a href="{{route('front.product', ['category_id' => $category->id])}}" class="categories-item">
                        <div class="cat-img">
                            <img src="{{asset('assets/front/img/product/categories/' . $category->image)}}" alt="">
                        </div>
                        <div class="cat-content">
                            <h5>{{$category->name}}</h5>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
        </section><!--====== End plus-categories Section ======-->
        @endif

        @if ($be->featured_products_section == 1)
        <!--====== Start plus-featured Section ======-->
        <section class="plus-featured-ection pb-100">
            <div class="custom-container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="featured-tabs">
                            <ul class="nav nav-tabs">
                                <li class="nav-item">
                                  <a class="nav-link active" data-toggle="tab" href="#cat1">{{__('Featured')}}</a>
                                </li>
                                <li class="nav-item">
                                  <a class="nav-link" data-toggle="tab" href="#cat3">{{__('New Arrivals')}}</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="featured-arrows"></div>
                <div class="tab-content">
                    <div id="cat1" class="tab-pane active">
                        <div class="featured-slide">
                            
                            @foreach ($fproducts as $product)
                                <div class="shop-item">
                                    <a class="shop-img" href="{{route('front.product.details',$product->slug)}}">
                                        <img class="lazy" data-src="{{asset('assets/front/img/product/featured/'.$product->feature_image)}}" alt="">
                                    </a>
                                    <div class="shop-info">
                                        @if ($bex->product_rating_system == 1 && $bex->catalog_mode == 0)
                                        <div class="rate">
                                            <div class="rating" style="width:{{$product->rating * 20}}%"></div>
                                        </div>
                                        @endif
                                        <h3><a href="{{route('front.product.details',$product->slug)}}">{{strlen($product->title) > 40 ? mb_substr($product->title,0,40,'utf-8') . '...' : $product->title}}</a></h3>
                                        @if ($bex->catalog_mode == 0)
                                            <div class="shop-price">
                                                <p class="price">
                                                    <span class="off-price">
                                                        {{ $bex->base_currency_symbol_position == 'left' ? $bex->base_currency_symbol : '' }}{{$product->current_price}}{{ $bex->base_currency_symbol_position == 'right' ? $bex->base_currency_symbol : '' }}
                                                    </span>
                                                    @if (!empty($product->previous_price))
                                                    <span class="main-price">
                                                        {{$bex->base_currency_symbol_position == 'left' ? $bex->base_currency_symbol : ''}}{{$product->previous_price}}{{$bex->base_currency_symbol_position == 'right' ? $bex->base_currency_symbol : ''}}
                                                    </span>
                                                    @endif
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div id="cat3" class="tab-pane fade">
                        <div class="featured-slide">
                            @foreach ($products as $product)
                                <div class="shop-item">
                                    <a class="shop-img" href="{{route('front.product.details',$product->slug)}}">
                                        <img class="lazy" data-src="{{asset('assets/front/img/product/featured/'.$product->feature_image)}}" alt="">
                                    </a>
                                    <div class="shop-info">
                                        @if ($bex->product_rating_system == 1 && $bex->catalog_mode == 0)
                                        <div class="rate">
                                            <div class="rating" style="width:{{$product->rating * 20}}%"></div>
                                        </div>
                                        @endif
                                        <h3><a href="{{route('front.product.details',$product->slug)}}">{{strlen($product->title) > 40 ? mb_substr($product->title,0,40,'utf-8') . '...' : $product->title}}</a></h3>
                                        @if ($bex->catalog_mode == 0)
                                            <div class="shop-price">
                                                <p class="price">
                                                    <span class="off-price">
                                                        {{ $bex->base_currency_symbol_position == 'left' ? $bex->base_currency_symbol : '' }}{{$product->current_price}}{{ $bex->base_currency_symbol_position == 'right' ? $bex->base_currency_symbol : '' }}
                                                    </span>
                                                    @if (!empty($product->previous_price))
                                                    <span class="main-price">
                                                        {{$bex->base_currency_symbol_position == 'left' ? $bex->base_currency_symbol : ''}}{{$product->previous_price}}{{$bex->base_currency_symbol_position == 'right' ? $bex->base_currency_symbol : ''}}
                                                    </span>
                                                    @endif
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                </div>
            </div>
        </section><!--====== End plus-featured Section ======-->
        @endif
        
        @if ($be->category_products_section == 1)
            <!--====== Start product-categories Section ======-->
            @foreach ($hcategories as $category)
            <section class="product-categories pb-100">
                <div class="custom-container">
                    <div class="section-header mb-40">
                        <div class="row align-items-center">
                            <div class="col-lg-4 col-md-12 col-sm-12">
                                <div class="section-title">
                                    <h2>{{$category->name}}</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="shop-categories-slide">

                        @if ($category->products()->count() > 0)
                            @foreach ($category->products as $product)
                                <div class="shop-item">
                                    <a class="shop-img" href="{{route('front.product.details',$product->slug)}}">
                                        <img class="lazy" data-src="{{asset('assets/front/img/product/featured/'.$product->feature_image)}}" alt="">
                                    </a>
                                    <div class="shop-info">
                                        @if ($bex->product_rating_system == 1 && $bex->catalog_mode == 0)
                                        <div class="rate">
                                            <div class="rating" style="width:{{$product->rating * 20}}%"></div>
                                        </div>
                                        @endif
                                        <h3><a href="{{route('front.product.details',$product->slug)}}">{{strlen($product->title) > 40 ? mb_substr($product->title,0,40,'utf-8') . '...' : $product->title}}</a></h3>
                                        @if ($bex->catalog_mode == 0)
                                            <div class="shop-price">
                                                <p class="price">
                                                    <span class="off-price">
                                                        {{ $bex->base_currency_symbol_position == 'left' ? $bex->base_currency_symbol : '' }}{{$product->current_price}}{{ $bex->base_currency_symbol_position == 'right' ? $bex->base_currency_symbol : '' }}
                                                    </span>
                                                    @if (!empty($product->previous_price))
                                                    <span class="main-price">
                                                        {{$bex->base_currency_symbol_position == 'left' ? $bex->base_currency_symbol : ''}}{{$product->previous_price}}{{$bex->base_currency_symbol_position == 'right' ? $bex->base_currency_symbol : ''}}
                                                    </span>
                                                    @endif
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </section>
            @endforeach<!--====== End product-categories Section ======-->
        @endif

        @if ($bs->news_section == 1)
            <!--====== Start blog-grid-section Section ======-->
            <section class="blog-grid-section">
                <div class="custom-container">
                    <div class="row justify-content-center">
                        <div class="col-lg-8">
                            <div class="section-title text-center mb-20">
                                <h2>{{$bs->blog_section_title}}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="row blog-slide">
                        @foreach ($blogs as $key => $blog)
                            <div class="blog-item mb-40">
                                <div class="post-thumb">
                                    <img class="lazy" data-src="{{asset('assets/front/img/blogs/'.$blog->main_image)}}" alt="">
                                    <a href="#" class="cat">{{!empty($blog->bcategory) ? $blog->bcategory->name : ''}}</a>
                                </div>
                                <div class="entry-content">
                                    <h3 class="title"><a href="{{route('front.blogdetails', [$blog->slug, $blog->id])}}">{{strlen($blog->title) > 40 ? mb_substr($blog->title, 0, 40, 'utf-8') . '...' : $blog->title}}</a></h3>
                                    <div class="post-meta">
                                        <ul>
                                            @php
                                                $blogDate = \Carbon\Carbon::parse($blog->created_at)->locale("$currentLang->code");
                                                $blogDate = $blogDate->translatedFormat('jS F, Y');
                                            @endphp
                                            <li><span><a href="#">{{$blogDate}}</a></span></li>
                                            <li><span><a href="#">By {{__('Admin')}}</a></span></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section><!--====== End blog-grid-section Section ======-->
        @endif

        @if ($bs->partner_section == 1)
        <!--====== Start Plus-sponsor Section ======-->
        <section class="plus-sponsor pb-60 pt-100">
            <div class="custom-container">
                <div class="sponsor-wrapper pt-60 pb-65">
                    <div class="sponsor-slide">
                        @foreach ($partners as $key => $partner)
                        <div class="sponsor-item">
                            <a href="{{$partner->url}}" target="_blank"><img data-src="{{asset('assets/front/img/partners/'.$partner->image)}}" class="lazy" alt=""></a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section><!--====== End Plus-sponsor Section ======-->
        @endif


        @if ($bs->newsletter_section == 1)
        <!--====== Start Plus-newsletter Section ======-->
        <section class="plus-newsletter-section pb-50 pt-50">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <div class="section-title section-title-white">
                            <h2>{{$bs->newsletter_text}}</h2>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="newsletter-form">
                            <form class="footer-newsletter" id="footerSubscribeForm" action="{{route('front.subscribe')}}" method="post">
                                @csrf
                                <div class="form_group">
                                    <input type="email" class="form_control" placeholder="{{__('Enter Email Address')}}" name="email" value="" required>
                                    <button class="main-btn">{{__('Subscribe')}}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section><!--====== End Plus-newsletter Section ======-->    
        @endif
@endsection