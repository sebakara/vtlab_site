<div class="hero-slide">
    <div class="single-hero bg_cover lazy" id="hero-home-5" data-bg="{{asset('assets/front/img/'.$bs->hero_bg)}}">
        <div id="bgndVideo" data-property="{videoURL:'{{$bs->hero_section_video_link}}',containment:'#hero-home-5', quality:'large', autoPlay:true, loop:true, mute:true, opacity:1}"></div>
        <div class="container">
            <div class="row">
                <div class="col-lg-7">
                    <div class="hero-content">
                        <span style="font-size: {{$be->hero_section_title_font_size}}px;line-height: {{$be->hero_section_title_font_size + 10}}px">{{$bs->hero_section_title}}</span>
                        <h1 style="font-size: {{$be->hero_section_text_font_size}}px;line-height: {{$be->hero_section_text_font_size + 10}}px">{{$bs->hero_section_text}}</h1>
                        @if (!empty($bs->hero_section_button_url) && !empty($bs->hero_section_button_text))
                            <a href="{{$bs->hero_section_button_url}}" style="font-size: {{$be->hero_section_button_text_font_size}}px" class="main-btn">{{$bs->hero_section_button_text}}<i class="fas fa-long-arrow-alt-right"></i></a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>