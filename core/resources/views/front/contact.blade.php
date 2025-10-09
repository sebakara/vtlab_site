@extends("front.$version.layout")

@section('pagename')
- {{__('Contact Us')}}
@endsection

@section('meta-keywords', "$be->contact_meta_keywords")
@section('meta-description', "$be->contact_meta_description")

@section('breadcrumb-title', $bs->contact_title)
@section('breadcrumb-subtitle', $bs->contact_subtitle)
@section('breadcrumb-link', __('Contact Us'))

@section('content')


<!--    contact form and map start   -->
<div class="contact-form-section">
    <div class="container">
        <div class="contact-infos mb-5">
            <div class="row no-gutters">
                <div class="col-lg-4 single-info-col">
                    <div class="single-info wow fadeInRight" data-wow-duration="1s" style="visibility: visible; animation-duration: 1s; animation-name: fadeInRight;">
                        <div class="icon-wrapper"><i class="fas fa-home"></i></div>
                        <div class="info-txt">
                            @php
                                $addresses = explode(PHP_EOL, $bex->contact_addresses);
                            @endphp
                            @foreach ($addresses as $address)
                            <p><i class="fas fa-map-pin base-color mr-1"></i> {{$address}}</p>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 single-info-col">
                    <div class="single-info wow fadeInRight" data-wow-duration="1s" data-wow-delay=".2s" style="visibility: visible; animation-duration: 1s; animation-delay: 0.2s; animation-name: fadeInRight;">
                        <div class="icon-wrapper"><i class="fas fa-phone"></i></div>
                        <div class="info-txt">
                            @php
                                $phones = explode(',', $bex->contact_numbers);
                            @endphp
                            @foreach ($phones as $phone)
                            <p>{{$phone}}</p>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 single-info-col">
                    <div class="single-info wow fadeInRight" data-wow-duration="1s" data-wow-delay=".4s" style="visibility: visible; animation-duration: 1s; animation-delay: 0.4s; animation-name: fadeInRight;">
                        <div class="icon-wrapper"><i class="far fa-envelope"></i></div>
                        <div class="info-txt">
                            @php
                                $mails = explode(',', $bex->contact_mails);
                            @endphp
                            @foreach ($mails as $mail)
                            <p>{{$mail}}</p>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6">
                <span class="section-title">{{convertUtf8($bs->contact_form_title)}}</span>
                <h2 class="section-summary">{{convertUtf8($bs->contact_form_subtitle)}}</h2>
                <form action="{{route('front.sendmail')}}" class="contact-form" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-element">
                                <input name="name" type="text" placeholder="{{__('Name')}}" required>
                            </div>
                            @if ($errors->has('name'))
                            <p class="text-danger mb-0">{{$errors->first('name')}}</p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <div class="form-element">
                                <input name="email" type="email" placeholder="{{__('Email')}}" required>
                            </div>
                            @if ($errors->has('email'))
                            <p class="text-danger mb-0">{{$errors->first('email')}}</p>
                            @endif
                        </div>
                        <div class="col-md-12">
                            <div class="form-element">
                                <input name="subject" type="text" placeholder="{{__('Subject')}}" required>
                            </div>
                            @if ($errors->has('subject'))
                            <p class="text-danger mb-0">{{$errors->first('subject')}}</p>
                            @endif
                        </div>
                        <div class="col-md-12">
                            <div class="form-element">
                                <textarea name="message" id="comment" cols="30" rows="10" placeholder="{{__('Comment')}}" required></textarea>
                            </div>
                            @if ($errors->has('message'))
                            <p class="text-danger mb-0">{{$errors->first('message')}}</p>
                            @endif
                        </div>
                        @if ($bs->is_recaptcha == 1)
                        <div class="col-lg-12 mb-4">
                            {!! NoCaptcha::renderJs() !!}
                            {!! NoCaptcha::display() !!}
                            @if ($errors->has('g-recaptcha-response'))
                            @php
                            $errmsg = $errors->first('g-recaptcha-response');
                            @endphp
                            <p class="text-danger mb-0">{{__("$errmsg")}}</p>
                            @endif
                        </div>
                        @endif

                        <div class="col-md-12">
                            <div class="form-element no-margin">
                                <input type="submit" value="{{__('Submit')}}">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-lg-6">
                <div class="map-wrapper">
                    <div id="map">
                        <iframe width="100%" height="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?width=100%25&amp;height=600&amp;hl=en&amp;q={{$bex->latitude}},%20{{$bex->longitude}}+(My%20Business%20Name)&amp;t=&amp;z={{$bex->map_zoom}}&amp;ie=UTF8&amp;iwloc=B&amp;output=embed"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--    contact form and map end   -->
@endsection
