
        <!--====== Start Plus-footer Section ======-->
        <footer class="plus-footer">
            @if ($bs->top_footer_section == 1)
            <div class="footer-widget pt-90 pb-60">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-5">
                            <div class="widget about-widget mb-40">
                                <a href="{{route('front.index')}}">
                                    <img class="lazy" data-src="{{asset('assets/front/img/'.$bs->footer_logo)}}" alt="">
                                </a>
                                <p>
                                    {{$bs->footer_text}}
                                </p>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="widget widget-catgories">
                                <h4 class="widget-title">{{__('Useful Links')}}</h4>
                                <ul class="link">
                                    @foreach ($ulinks as $key => $ulink)
                                      <li><a href="{{$ulink->url}}">{{$ulink->name}}</a></li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                            <div class="col-lg-4">
                                <div class="widget about-widget">
                                    <h4 class="widget-title">{{__('Contact Us')}}</h4>
                                    @php
                                    $addresses = explode(PHP_EOL, $bex->contact_addresses);
                                    @endphp
                                    @if (!empty($addresses))
                                    <p class="ip">
                                        <i class="fas fa-map-marker-alt"></i>
                                        @foreach ($addresses as $address)
                                            {{$address}}
                                            @if (!$loop->last)
                                                |
                                            @endif
                                        @endforeach
                                    </p>
                                    @endif
                                    
                                    @php
                                        $mails = explode(',', $bex->contact_mails);
                                    @endphp
                                    @if (!empty($mails))
                                    <p class="ip">
                                        <i class="fas fa-envelope"></i>
                                        @foreach ($mails as $mail)
                                            <a href="mailto:{{$mail}}" class="d-inline-block">{{$mail}}</a>
                                            @if (!$loop->last)
                                                ,
                                            @endif
                                        @endforeach
                                    </p>
                                    @endif

                                    @php
                                     $phones = explode(',', $bex->contact_numbers);
                                    @endphp
                                    <p class="ip"><i class="fas fa-mobile-alt"></i>
                                        @foreach ($phones as $phone)
                                            <a href="tel:{{$phone}}">{{$phone}}</a>
                                            @if (!$loop->last)
                                                ,
                                            @endif
                                        @endforeach
                                    </p>
                                </div>
                            </div>
                    </div>
                </div>
            </div>
            @endif

            @if ($bs->copyright_section == 1)
            <div class="copyright-area">
                <div class="container">
                    <div class="row align-items-center justify-content-center">
                        <div class="col-lg-6">
                            <div class="copyright-text text-center">
                                <p>{!! replaceBaseUrl($bs->copyright_text) !!}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </footer><!--====== End Plus-footer Section ======-->