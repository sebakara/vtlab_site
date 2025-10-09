        <!--====== Start header-area ======-->
        <header class="header-area">
            <div class="header-top">
                <div class="custom-container">
                    <div class="row align-items-center">
                        <div class="col-xl-4 d-xl-block d-none">
                            <div class="top-left">
                                <ul>
                                    <li><a href="tel:{{$bs->support_phone}}"><i class="fas fa-mobile-alt mr-2"></i> {{$bs->support_phone}}</a></li>
                                    <li><a href="mailto:{{$bs->support_email}}"><i class="far fa-envelope-open mr-2"></i> {{$bs->support_email}}</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-lg-6 col-xl-4">
                            <div class="nav-search">
                                @php
                                    $maxprice = App\Product::max('current_price');
                                    $minprice = 0;
                                @endphp
                                <form action="{{ route('front.product') }}">
                                    <div class="form_group">
                                        <input type="text" class="form_control" name="search" placeholder="{{__('Search Keywords')}}" value="{{ !empty(request()->input('search')) ? request()->input('search') : '' }}">
                                        <i class="fas fa-search"></i>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="col-lg-6 col-xl-4">
                            <div class="top-right">
                                <ul>
                                    <li>
                                        <form id="langForm" class="d-inline-block">
                                            <select name="language" class="form-control form-control-sm mb-0">
                                                @foreach ($langs as $lang)
                                                <option {{$lang->code == $currentLang->code ? 'selected' : ''}} value="{{$lang->code}}">{{$lang->name}}</option>
                                                @endforeach
                                            </select>
                                        </form>
                                    </li>
                                    @guest
                                        @if ($bex->is_user_panel == 1)
                                            <li><a href="{{route('user.login')}}" class="login">{{__('Login')}}<i class="far fa-user"></i></a></li>
                                        @endif
                                    @endguest
                                    @auth
                                        @if ($bex->is_user_panel == 1)
                                            <li><a href="{{route('user-dashboard')}}" class="login">{{__('Dashboard')}}<i class="far fa-user"></i></a></li>
                                        @endif
                                    @endauth
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="header-navigation">
                <div class="custom-container">
                    <div class="nav-container">
                        <div class="row align-items-center">
                            <div class="col-lg-2 col-5">
                                <div class="brand-logo">
                                    <a href="{{route('front.index')}}" class="logo"><img class="lazy" data-src="{{asset('assets/front/img/'.$bs->footer_logo)}}" alt=""></a>
                                </div>
                            </div>
                            <div class="col-lg-10 col-7">
                                @includeIf('front.ecommerce.partials.navbar')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header><!--====== End header-area ======-->