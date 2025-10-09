<div class="nav-menu d-flex justify-content-end align-items-center">
    <!-- Navbar Close Icon -->
    <div class="navbar-close">
        <div class="cross-wrap"><span class="top"></span><span class="bottom"></span></div>
    </div>
    <!-- nav-menu -->
    <nav class="main-menu">
        <ul>
            @php
                $links = json_decode($menus, true);
                //  dd($links);
            @endphp

            @foreach ($links as $link)
                @php
                    $href = getHref($link);
                @endphp

                @if (strpos($link["type"], '-megamenu') !==  false)
                    @includeIf('front.ecommerce.partials.mega-menu')
                @else
                    @if (!array_key_exists("children",$link))
                        {{--- Level1 links which doesn't have dropdown menus ---}}
                        <li class="menu-item"><a href="{{$href}}" target="{{$link["target"]}}">{{$link["text"]}}</a></li>
                    @else
                        <li class="menu-item menu-item-has-children">
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
            <li>
                <a href="{{route('front.quote')}}" class="quote-btn">{{__('Request A Quote')}}</a>
            </li>
            @endif
        </ul>
    </nav>
</div>
<!-- Navbar Toggler -->
<div class="navbar-toggler">
    <span></span><span></span><span></span>
</div>