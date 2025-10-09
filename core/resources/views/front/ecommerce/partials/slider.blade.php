<div class="hero-slide">
    @if (!empty($sliders))
        @foreach ($sliders as $key => $slider)
            <div class="single-hero bg_cover lazy" data-bg="{{asset('assets/front/img/sliders/'.$slider->image)}}">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-7">
                            <div class="hero-content">
                                <span style="font-size: {{$slider->title_font_size}}px;line-height: {{$slider->title_font_size + 10}}px">{{$slider->title}}</span>
                                <h1 style="font-size: {{$slider->text_font_size}}px;line-height: {{$slider->text_font_size + 10}}px">{{$slider->text}}</h1>
                                @if (!empty($slider->button_url) && !empty($slider->button_text))
                                    <a href="{{$slider->button_url}}" style="font-size: {{$slider->button_text_font_size}}px" class="main-btn">{{$slider->button_text}}<i class="fas fa-long-arrow-alt-right"></i></a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</div>