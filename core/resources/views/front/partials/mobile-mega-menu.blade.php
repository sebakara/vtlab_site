<li class="menu-item menu-item-has-children static mega-dropdown d-lg-none d-block"><a href="{{$href}}">{{$link["text"]}}</a>
    <ul class="mega-menu">
        <li class="mega-wrap">
                @if ($catAvailable)

                    <a href="{{$allUrl}}" data-tabid="all">{{__('All')}}</a>
                    @foreach ($megaMenus as $mCatId => $mItemIds)
                        @php
                            $mcat = $catModel::where('id', $mCatId);
                            if ($mcat->count() == 0) {
                                continue;
                            } else {
                                $mcat = $mcat->first();
                            }

                            if ($link["type"] == 'services-megamenu') {
                                $catUrl = route('front.services', ['category' => $mcat->id, 'term'=>request()->input('term')]);
                            } elseif ($link["type"] == 'products-megamenu') {
                                $catUrl = route('front.product', ['category_id' => $mcat->id]);
                            } elseif ($link["type"] == 'portfolios-megamenu') {
                                $catUrl = route('front.portfolios', ['category' => $mcat->id]);
                            } elseif ($link["type"] == 'courses-megamenu') {
                                $catUrl = route('courses', ['category_id' => $mcat->id]);
                            } elseif ($link["type"] == 'events-megamenu') {
                                $catUrl = route('front.events', ['category' => $mcat->id]);
                            } elseif ($link["type"] == 'blogs-megamenu') {
                                $catUrl = route('front.blogs', ['category' => $mcat->slug]);
                            }
                        @endphp
                        <a href="{{$catUrl}}" data-tabid="#megaTab{{$link["type"]}}{{$mcat->id}}">{{$mcat->name}}</a>
                    @endforeach

                @endif

                @php
                    if ($catAvailable) {
                        $colClass = 'col-lg-10';
                    } elseif (!$catAvailable) {
                        $colClass = 'col-lg-12';
                    }
                @endphp
                    @if (!$catAvailable)
                        @foreach ($megaMenus as $mItemId)

                            @php
                                $mItem = $itemModel::where('id', $mItemId);
                                if ($mItem->count() == 0) {
                                    continue;
                                } else {
                                    $mItem = $mItem->first();
                                }
                                if ($link['type'] == 'services-megamenu') {
                                    $detailsUrl = route('front.servicedetails', [$mItem->slug]);
                                    $imgSrc = asset('assets/front/img/services/' . $mItem->main_image);
                                } elseif ($link['type'] == 'portfolios-megamenu') {
                                    $detailsUrl = route('front.portfoliodetails',[$mItem->slug]);
                                    $imgSrc = asset('assets/front/img/portfolios/featured/' . $mItem->featured_image);
                                } elseif ($link["type"] == 'causes-megamenu') {
                                    $detailsUrl = route('front.cause_details',[$mItem->slug]);
                                    $imgSrc = asset('assets/front/img/donations/' . $mItem->image);
                                }
                            @endphp
                            <a href="{{$detailsUrl}}">{{strlen($mItem->title) > 30 ? mb_substr($mItem->title,0,30,'utf-8') . '...' : $mItem->title}}</a>

                        @endforeach
                    @endif
        </li>
    </ul>
</li>
