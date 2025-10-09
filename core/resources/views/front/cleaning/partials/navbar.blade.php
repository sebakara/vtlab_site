    @php
        $links = json_decode($menus, true);
        //  dd($links);
    @endphp

    <!-- HEADER START -->
    <header class="@yield('no-breadcrumb')">
        <section class="top-header-area">
            <div class="container-fluid">
                <div class="row align-items-center">
                    <div class="col-lg-6 col-md-6 col-12">
                        <div class="top-header-address">
                            <span><i class="fas fa-headphones-alt"></i> {{$bs->support_phone}}</span>
                            <span><i class="fas fa-envelope-open-text"></i> {{$bs->support_email}}</span>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-12">
                        <div class="top_right d-flex">
                            @if (!empty($currentLang) && count($langs) > 1)
                                <ul class="top-header-language">
                                    <li><a href="#"><i class="fas fa-globe"></i>{{convertUtf8($currentLang->name)}} <i class="fa fa-angle-down"></i></a>
                                        <ul class="language-dropdown">
                                            @foreach ($langs as $key => $lang)
                                                <li><a href='{{ route('changeLanguage', $lang->code) }}'>{{convertUtf8($lang->name)}}</a></li>
                                            @endforeach
                                        </ul>
                                    </li>
                                </ul>
                            @endif
                            <div class="top-header-social-links">
                                <ul>
                                    @foreach ($socials as $key => $social)
                                        <li><a href="{{$social->url}}"><i class="{{$social->icon}}"></i></a></li>
                                    @endforeach
                                </ul>
                            </div>

                            @guest
                                @if ($bex->is_user_panel == 1)
                                    <ul class="login">
                                        <li><a href="{{route('user.login')}}">{{__('Login')}}</a></li>
                                    </ul>
                                @endif
                            @endguest

                            @auth
                            <ul class="top-header-language ml-4">
                                <li><a href="#"><i class="far fa-user"></i>{{Auth::user()->username}} <i class="fa fa-angle-down"></i></a>
                                    <ul class="language-dropdown">
                                        <li>
                                            <a href="{{route('user-dashboard')}}">{{__('Dashboard')}}</a>
                                        </li>

                                        @if ($bex->recurring_billing == 1)
                                            <li><a href="{{route('user-packages')}}">{{__('Packages')}}</a></li>
                                        @endif

                                        @if ($bex->is_shop == 1 && $bex->catalog_mode == 0)
                                            <li><a href="{{route('user-orders')}}">{{__('Product Orders')}} </a></li>
                                        @endif

                                        @if ($bex->recurring_billing == 0)
                                            <li><a href="{{route('user-package-orders')}}">{{__('Package Orders')}} </a></li>
                                        @endif


                                        @if ($bex->is_course == 1)
                                        <li>
                                            <a href="{{route('user.course_orders')}}" >{{__('Courses')}}</a>
                                        </li>
                                        @endif


                                        @if ($bex->is_event == 1)
                                        <li>
                                            <a href="{{route('user-events')}}" >{{__('Event Bookings')}}</a>
                                        </li>
                                        @endif


                                        @if ($bex->is_donation == 1)
                                        <li>
                                            <a href="{{route('user-donations')}}" >{{__('Donations')}}</a>
                                        </li>
                                        @endif

                                        @if ($bex->is_ticket == 1)
                                        <li>
                                            <a href="{{route('user-tickets')}}">{{__('Support Tickets')}}</a>
                                        </li>
                                        @endif

                                        <li>
                                            <a href="{{route('user-profile')}}">{{__('Edit Profile')}}</a>
                                        </li>

                                        @if ($bex->is_shop == 1 && $bex->catalog_mode == 0)
                                            <li>
                                                <a href="{{route('shpping-details')}}">{{__('Shipping Details')}}</a>
                                            </li>
                                            <li>
                                                <a href="{{route('billing-details')}}">{{__('Billing Details')}}</a>
                                            </li>
                                            <li>
                                                <a href="{{route('user-reset')}}">{{__('Change Password')}}</a>
                                            </li>
                                        @endif
                                        <li>
                                            <a href="{{route('user-logout')}}" target="_self">{{__('Logout')}}</a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="bottom-header-area">
            <div class="container-fluid">
                <div class="row align-items-center position-relative">
                    <div class="col-lg-2 col-6">
                        <div class="logo">
                            <a href="{{route('front.index')}}"><img data-src="{{asset('assets/front/img/'.$bs->logo)}}" class="img-fluid lazy" alt=""></a>
                        </div>
                    </div>
                    <div class="col-lg-10 col-6">
                        <div class="header-menu-area">
                            <div class="primary_menu">
                                <nav class="main-menu {{$bs->is_quote == 0 ? 'mr-0' : ''}}">
                                    @php
                                        $links = json_decode($menus, true);
                                        //  dd($links);
                                    @endphp
                                    <ul>

                                        @foreach ($links as $link)
                                            @php
                                                $href = getHref($link);
                                            @endphp

                                            @if (strpos($link["type"], '-megamenu') !==  false)
                                                @includeIf('front.gym.partials.mega-menu')

                                            @else

                                                @if (!array_key_exists("children",$link))
                                                    {{--- Level1 links which doesn't have dropdown menus ---}}
                                                    <li><a href="{{$href}}" target="{{$link["target"]}}">{{$link["text"]}}</a></li>

                                                @else
                                                    <li class="menu-item-has-children">
                                                        {{--- Level1 links which has dropdown menus ---}}
                                                        <a href="{{$href}}" target="{{$link["target"]}}">{{$link["text"]}}</a>

                                                        <ul class="sub-menu">



                                                            {{-- START: 2nd level links --}}
                                                            @foreach ($link["children"] as $level2)
                                                                @php
                                                                    $l2Href = getHref($level2);
                                                                @endphp

                                                                <li @if(array_key_exists("children", $level2)) class="submenus" @endif>
                                                                    <a  href="{{$l2Href}}" target="{{$level2["target"]}}">{{$level2["text"]}}</a>

                                                                    {{-- START: 3rd Level links --}}
                                                                    @php
                                                                        if (array_key_exists("children", $level2)) {
                                                                            create_menu($level2);
                                                                        }
                                                                    @endphp
                                                                    {{-- END: 3rd Level links --}}

                                                                </li>
                                                            @endforeach
                                                            {{-- END: 2nd level links --}}



                                                        </ul>

                                                    </li>
                                                @endif


                                            @endif



                                        @endforeach

                                        @if ($bs->is_quote == 1)
                                            <li class="d-block d-lg-none"><a href="{{route('front.quote')}}">{{__('Get Quote')}}</a></li>
                                        @endif
                                    </ul>
                                </nav>
                            </div>

                            @if ($bs->is_quote == 1)
                                <a href="{{route('front.quote')}}" class="cleaning-main-btn header-bgn d-none d-lg-inline-block">{{__('Get Quote')}}</a>
                            @endif
                        </div>
                    </div>
                    <div class="col-sm-12 position-static"><div class="mobile_menu"></div></div>
                </div>
            </div>
        </section>
    </header>
    <!-- HEADER END -->
