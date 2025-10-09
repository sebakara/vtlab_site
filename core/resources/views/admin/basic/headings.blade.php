@extends('admin.layout')

@if(!empty($abs->language) && $abs->language->rtl == 1)
@section('styles')
<style>
  form input,
  form textarea,
  form select {
    direction: rtl;
  }

  form .note-editor.note-frame .note-editing-area .note-editable {
    direction: rtl;
    text-align: right;
  }
</style>
@endsection
@endif

@section('content')
<div class="page-header">
  <h4 class="page-title">Page Headings</h4>
  <ul class="breadcrumbs">
    <li class="nav-home">
      <a href="{{route('admin.dashboard')}}">
        <i class="flaticon-home"></i>
      </a>
    </li>
    <li class="separator">
      <i class="flaticon-right-arrow"></i>
    </li>
    <li class="nav-item">
      <a href="#">Basic Settings</a>
    </li>
    <li class="separator">
      <i class="flaticon-right-arrow"></i>
    </li>
    <li class="nav-item">
      <a href="#">Page Headings</a>
    </li>
  </ul>
</div>
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <form class="" action="{{route('admin.heading.update', $lang_id)}}" method="post">
        @csrf
        <div class="card-header">
          <div class="row">
            <div class="col-lg-10">
              <div class="card-title">Update Page Headings</div>
            </div>
            <div class="col-lg-2">
              @if (!empty($langs))
              <select name="language" class="form-control"
                onchange="window.location='{{url()->current() . '?language='}}'+this.value">
                <option value="" selected disabled>Select a Language</option>
                @foreach ($langs as $lang)
                <option value="{{$lang->code}}" {{$lang->code == request()->input('language') ? 'selected' : ''}}>
                  {{$lang->name}}</option>
                @endforeach
              </select>
              @endif
            </div>
          </div>
          <div class="card-body pt-5 pb-5">
            <div class="row">
              <div class="col-lg-8 offset-lg-2">
                @csrf

                <div class="row">
                  <div class="form-group col-lg-6">
                    <label>Service Title **</label>
                    <input class="form-control" name="service_title"
                      value="{{empty(old('service_title')) ? $abs->service_title : old('service_title')}}">
                    @if ($errors->has('service_title'))
                    <p class="mb-0 text-danger">{{$errors->first('service_title')}}</p>
                    @endif
                  </div>
                  <div class="form-group col-lg-6">
                    <label>Service Subtitle **</label>
                    <input class="form-control" name="service_subtitle"
                      value="{{empty(old('service_subtitle')) ? $abs->service_subtitle : old('service_subtitle')}}">
                    @if ($errors->has('service_subtitle'))
                    <p class="mb-0 text-danger">{{$errors->first('service_subtitle')}}</p>
                    @endif
                  </div>
                  <div class="form-group col-lg-6">
                    <label>Service Details Title **</label>
                    <input class="form-control" name="service_details_title"
                      value="{{empty(old('service_details_title')) ? $abs->service_details_title : old('service_details_title')}}">
                    @if ($errors->has('service_details_title'))
                    <p class="mb-0 text-danger">{{$errors->first('service_details_title')}}</p>
                    @endif
                  </div>
                  <div class="form-group col-lg-6">
                    <label>Portfolio Title **</label>
                    <input class="form-control" name="portfolio_title"
                      value="{{empty(old('portfolio_title')) ? $abs->portfolio_title : old('portfolio_title')}}">
                    @if ($errors->has('portfolio_title'))
                    <p class="mb-0 text-danger">{{$errors->first('portfolio_title')}}</p>
                    @endif
                  </div>
                  <div class="form-group col-lg-6">
                    <label>Portfolio Subtitle **</label>
                    <input class="form-control" name="portfolio_subtitle"
                      value="{{empty(old('portfolio_subtitle')) ? $abs->portfolio_subtitle : old('portfolio_subtitle')}}">
                    @if ($errors->has('portfolio_subtitle'))
                    <p class="mb-0 text-danger">{{$errors->first('portfolio_subtitle')}}</p>
                    @endif
                  </div>
                  <div class="form-group col-lg-6">
                    <label>Portfolio Details Title **</label>
                    <input class="form-control" name="portfolio_details_title"
                      value="{{empty(old('portfolio_details_title')) ? $abs->portfolio_details_title : old('portfolio_details_title')}}">
                    @if ($errors->has('portfolio_details_title'))
                    <p class="mb-0 text-danger">{{$errors->first('portfolio_details_title')}}</p>
                    @endif
                  </div>
                  <div class="form-group col-lg-6">
                    <label>FAQ Title **</label>
                    <input class="form-control" name="faq_title"
                      value="{{empty(old('faq_title')) ? $abs->faq_title : old('faq_title')}}">
                    @if ($errors->has('faq_title'))
                    <p class="mb-0 text-danger">{{$errors->first('faq_title')}}</p>
                    @endif
                  </div>
                  <div class="form-group col-lg-6">
                    <label>FAQ Subtitle **</label>
                    <input class="form-control" name="faq_subtitle"
                      value="{{empty(old('faq_subtitle')) ? $abs->faq_subtitle : old('faq_subtitle')}}">
                    @if ($errors->has('faq_subtitle'))
                    <p class="mb-0 text-danger">{{$errors->first('faq_subtitle')}}</p>
                    @endif
                  </div>

                  <div class="form-group col-lg-6">
                    <label>Pricing Title **</label>
                    <input class="form-control" name="pricing_title"
                      value="{{empty(old('pricing_title')) ? $abe->pricing_title : old('pricing_title')}}">
                    @if ($errors->has('pricing_title'))
                    <p class="mb-0 text-danger">{{$errors->first('pricing_title')}}</p>
                    @endif
                  </div>

                  <div class="form-group col-lg-6">
                    <label>Pricing Subtitle **</label>
                    <input class="form-control" name="pricing_subtitle"
                      value="{{empty(old('pricing_subtitle')) ? $abe->pricing_subtitle : old('pricing_subtitle')}}">
                    @if ($errors->has('pricing_subtitle'))
                    <p class="mb-0 text-danger">{{$errors->first('pricing_subtitle')}}</p>
                    @endif
                  </div>

                  <div class="form-group col-lg-6">
                    <label>Product Title **</label>
                    <input class="form-control" name="product_title"
                      value="{{empty(old('product_title')) ? $abe->product_title : old('product_title')}}">
                    @if ($errors->has('product_title'))
                    <p class="mb-0 text-danger">{{$errors->first('product_title')}}</p>
                    @endif
                  </div>

                  <div class="form-group col-lg-6">
                    <label>Product Subtitle **</label>
                    <input class="form-control" name="product_subtitle"
                      value="{{empty(old('product_subtitle')) ? $abe->product_subtitle : old('product_subtitle')}}">
                    @if ($errors->has('product_subtitle'))
                    <p class="mb-0 text-danger">{{$errors->first('product_subtitle')}}</p>
                    @endif
                  </div>

                  <div class="form-group col-lg-6">
                    <label>Product Details Title **</label>
                    <input class="form-control" name="product_details_title"
                      value="{{empty(old('product_details_title')) ? $abe->product_details_title : old('product_details_title')}}">
                    @if ($errors->has('product_details_title'))
                    <p class="mb-0 text-danger">{{$errors->first('product_details_title')}}</p>
                    @endif
                  </div>


                  <div class="form-group col-lg-6">
                    <label>Cart Title **</label>
                    <input class="form-control" name="cart_title"
                      value="{{empty(old('cart_title')) ? $abe->cart_title : old('cart_title')}}">
                    @if ($errors->has('cart_title'))
                    <p class="mb-0 text-danger">{{$errors->first('cart_title')}}</p>
                    @endif
                  </div>

                  <div class="form-group col-lg-6">
                    <label>Cart Subtitle **</label>
                    <input class="form-control" name="cart_subtitle"
                      value="{{empty(old('cart_subtitle')) ? $abe->cart_subtitle : old('cart_subtitle')}}">
                    @if ($errors->has('cart_subtitle'))
                    <p class="mb-0 text-danger">{{$errors->first('cart_subtitle')}}</p>
                    @endif
                  </div>

                  <div class="form-group col-lg-6">
                    <label>Checkout Title **</label>
                    <input class="form-control" name="checkout_title"
                      value="{{empty(old('checkout_title')) ? $abe->checkout_title : old('checkout_title')}}">
                    @if ($errors->has('checkout_title'))
                    <p class="mb-0 text-danger">{{$errors->first('checkout_title')}}</p>
                    @endif
                  </div>

                  <div class="form-group col-lg-6">
                    <label>Checkout Subtitle **</label>
                    <input class="form-control" name="checkout_subtitle"
                      value="{{empty(old('checkout_subtitle')) ? $abe->checkout_subtitle : old('checkout_subtitle')}}">
                    @if ($errors->has('checkout_subtitle'))
                    <p class="mb-0 text-danger">{{$errors->first('checkout_subtitle')}}</p>
                    @endif
                  </div>
                  <div class="form-group col-lg-6">
                    <label>Knowledgebase Title **</label>
                    <input class="form-control" name="knowledgebase_title"
                      value="{{empty(old('knowledgebase_title')) ? $abex->knowledgebase_title : old('knowledgebase_title')}}">
                    @if ($errors->has('knowledgebase_title'))
                    <p class="mb-0 text-danger">{{$errors->first('knowledgebase_title')}}</p>
                    @endif
                  </div>

                  <div class="form-group col-lg-6">
                    <label>Knowledgebase Subtitle **</label>
                    <input class="form-control" name="knowledgebase_subtitle"
                      value="{{empty(old('knowledgebase_subtitle')) ? $abex->knowledgebase_subtitle : old('knowledgebase_subtitle')}}">
                    @if ($errors->has('knowledgebase_subtitle'))
                    <p class="mb-0 text-danger">{{$errors->first('knowledgebase_subtitle')}}</p>
                    @endif
                  </div>

                  <div class="form-group col-lg-6">
                    <label>Knowledgebase Details Title **</label>
                    <input class="form-control" name="knowledgebase_details_title"
                      value="{{empty(old('knowledgebase_details_title')) ? $abex->knowledgebase_details_title : old('knowledgebase_details_title')}}">
                    @if ($errors->has('knowledgebase_details_title'))
                    <p class="mb-0 text-danger">{{$errors->first('knowledgebase_details_title')}}</p>
                    @endif
                  </div>
                  <div class="form-group col-lg-6">
                    <label>Blog Title **</label>
                    <input class="form-control" name="blog_title"
                      value="{{empty(old('blog_title')) ? $abs->blog_title : old('blog_title')}}">
                    @if ($errors->has('blog_title'))
                    <p class="mb-0 text-danger">{{$errors->first('blog_title')}}</p>
                    @endif
                  </div>
                  <div class="form-group col-lg-6">
                    <label>Blog Subtitle **</label>
                    <input class="form-control" name="blog_subtitle"
                      value="{{empty(old('blog_subtitle')) ? $abs->blog_subtitle : old('blog_subtitle')}}">
                    @if ($errors->has('blog_subtitle'))
                    <p class="mb-0 text-danger">{{$errors->first('blog_subtitle')}}</p>
                    @endif
                  </div>
                  <div class="form-group col-lg-6">
                    <label>Blog Details Title **</label>
                    <input class="form-control" name="blog_details_title"
                      value="{{empty(old('blog_details_title')) ? $abs->blog_details_title : old('blog_details_title')}}">
                    @if ($errors->has('blog_details_title'))
                    <p class="mb-0 text-danger">{{$errors->first('blog_details_title')}}</p>
                    @endif
                  </div>
                  <div class="form-group col-lg-6">
                    <label>RSS Title **</label>
                    <input class="form-control" name="rss_title"
                      value="{{empty(old('rss_title')) ? $abe->rss_title : old('rss_title')}}">
                    @if ($errors->has('rss_title'))
                    <p class="mb-0 text-danger">{{$errors->first('rss_title')}}</p>
                    @endif
                  </div>
                  <div class="form-group col-lg-6">
                    <label>RSS Subtitle **</label>
                    <input class="form-control" name="rss_subtitle"
                      value="{{empty(old('rss_subtitle')) ? $abe->rss_subtitle : old('rss_subtitle')}}">
                    @if ($errors->has('rss_subtitle'))
                    <p class="mb-0 text-danger">{{$errors->first('rss_subtitle')}}</p>
                    @endif
                  </div>
                  <div class="form-group col-lg-6">
                    <label>RSS Details Title **</label>
                    <input class="form-control" name="rss_details_title"
                      value="{{empty(old('rss_details_title')) ? $abe->rss_details_title : old('rss_details_title')}}">
                    @if ($errors->has('rss_details_title'))
                    <p class="mb-0 text-danger">{{$errors->first('rss_details_title')}}</p>
                    @endif
                  </div>
                  <div class="form-group col-lg-6">
                    <label>Gallery Title **</label>
                    <input class="form-control" name="gallery_title"
                      value="{{empty(old('gallery_title')) ? $abs->gallery_title : old('gallery_title')}}">
                    @if ($errors->has('gallery_title'))
                    <p class="mb-0 text-danger">{{$errors->first('gallery_title')}}</p>
                    @endif
                  </div>
                  <div class="form-group col-lg-6">
                    <label>Gallery Subtitle **</label>
                    <input class="form-control" name="gallery_subtitle"
                      value="{{empty(old('gallery_subtitle')) ? $abs->gallery_subtitle : old('gallery_subtitle')}}">
                    @if ($errors->has('gallery_subtitle'))
                    <p class="mb-0 text-danger">{{$errors->first('gallery_subtitle')}}</p>
                    @endif
                  </div>
                  <div class="form-group col-lg-6">
                    <label>Career Title **</label>
                    <input class="form-control" name="career_title"
                      value="{{empty(old('career_title')) ? $abe->career_title : old('career_title')}}">
                    @if ($errors->has('career_title'))
                    <p class="mb-0 text-danger">{{$errors->first('career_title')}}</p>
                    @endif
                  </div>
                  <div class="form-group col-lg-6">
                    <label>Career Subtitle **</label>
                    <input class="form-control" name="career_subtitle"
                      value="{{empty(old('career_subtitle')) ? $abe->career_subtitle : old('career_subtitle')}}">
                    @if ($errors->has('career_subtitle'))
                    <p class="mb-0 text-danger">{{$errors->first('career_subtitle')}}</p>
                    @endif
                  </div>
                  <div class="form-group col-lg-6">
                    <label>Course Title **</label>
                    <input class="form-control" name="course_title"
                      value="{{empty(old('course_title')) ? $abex->course_title : old('course_title')}}">
                    @if ($errors->has('course_title'))
                    <p class="mb-0 text-danger">{{$errors->first('course_title')}}</p>
                    @endif
                  </div>
                  <div class="form-group col-lg-6">
                    <label>Course Subtitle **</label>
                    <input class="form-control" name="course_subtitle"
                      value="{{empty(old('course_subtitle')) ? $abex->course_subtitle : old('course_subtitle')}}">
                    @if ($errors->has('course_subtitle'))
                    <p class="mb-0 text-danger">{{$errors->first('course_subtitle')}}</p>
                    @endif
                  </div>
                  <div class="form-group col-lg-6">
                    <label>Course Details Title **</label>
                    <input class="form-control" name="course_details_title"
                      value="{{empty(old('course_details_title')) ? $abex->course_details_title : old('course_details_title')}}">
                    @if ($errors->has('course_details_title'))
                    <p class="mb-0 text-danger">{{$errors->first('course_details_title')}}</p>
                    @endif
                  </div>
                  <div class="form-group col-lg-6">
                    <label>Event Calendar Title **</label>
                    <input class="form-control" name="event_calendar_title"
                      value="{{empty(old('event_calendar_title')) ? $abe->event_calendar_title : old('event_calendar_title')}}">
                    @if ($errors->has('event_calendar_title'))
                    <p class="mb-0 text-danger">{{$errors->first('event_calendar_title')}}</p>
                    @endif
                  </div>
                  <div class="form-group col-lg-6">
                    <label>Event Calendar Subtitle **</label>
                    <input class="form-control" name="event_calendar_subtitle"
                      value="{{empty(old('event_calendar_subtitle')) ? $abe->event_calendar_subtitle : old('event_calendar_subtitle')}}">
                    @if ($errors->has('event_calendar_subtitle'))
                    <p class="mb-0 text-danger">{{$errors->first('event_calendar_subtitle')}}</p>
                    @endif
                  </div>
                  <div class="form-group col-lg-6">
                    <label>Team Title **</label>
                    <input class="form-control" name="team_title"
                      value="{{empty(old('team_title')) ? $abs->team_title : old('team_title')}}">
                    @if ($errors->has('team_title'))
                    <p class="mb-0 text-danger">{{$errors->first('team_title')}}</p>
                    @endif
                  </div>
                  <div class="form-group col-lg-6">
                    <label>Team Subtitle **</label>
                    <input class="form-control" name="team_subtitle"
                      value="{{empty(old('team_subtitle')) ? $abs->team_subtitle : old('team_subtitle')}}">
                    @if ($errors->has('team_subtitle'))
                    <p class="mb-0 text-danger">{{$errors->first('team_subtitle')}}</p>
                    @endif
                  </div>
                  <div class="form-group col-lg-6">
                    <label>Contact Title **</label>
                    <input class="form-control" name="contact_title"
                      value="{{empty(old('contact_title')) ? $abs->contact_title : old('contact_title')}}">
                    @if ($errors->has('contact_title'))
                    <p class="mb-0 text-danger">{{$errors->first('contact_title')}}</p>
                    @endif
                  </div>
                  <div class="form-group col-lg-6">
                    <label>Contact Subtitle **</label>
                    <input class="form-control" name="contact_subtitle"
                      value="{{empty(old('contact_subtitle')) ? $abs->contact_subtitle : old('contact_subtitle')}}">
                    @if ($errors->has('contact_subtitle'))
                    <p class="mb-0 text-danger">{{$errors->first('contact_subtitle')}}</p>
                    @endif
                  </div>
                  <div class="form-group col-lg-6">
                    <label>Quote Title **</label>
                    <input class="form-control" name="quote_title"
                      value="{{empty(old('quote_title')) ? $abs->quote_title : old('quote_title')}}">
                    @if ($errors->has('quote_title'))
                    <p class="mb-0 text-danger">{{$errors->first('quote_title')}}</p>
                    @endif
                  </div>
                  <div class="form-group col-lg-6">
                    <label>Quote Subtitle **</label>
                    <input class="form-control" name="quote_subtitle"
                      value="{{empty(old('quote_subtitle')) ? $abs->quote_subtitle : old('quote_subtitle')}}">
                    @if ($errors->has('quote_subtitle'))
                    <p class="mb-0 text-danger">{{$errors->first('quote_subtitle')}}</p>
                    @endif
                  </div>
                  <div class="form-group col-lg-6">
                    <label>Error Page Title **</label>
                    <input class="form-control" name="error_title"
                      value="{{empty(old('error_title')) ? $abs->error_title : old('error_title')}}">
                    @if ($errors->has('error_title'))
                    <p class="mb-0 text-danger">{{$errors->first('error_title')}}</p>
                    @endif
                  </div>
                  <div class="form-group col-lg-6">
                    <label>Error Page Subtitle **</label>
                    <input class="form-control" name="error_subtitle"
                      value="{{empty(old('error_subtitle')) ? $abs->error_subtitle : old('error_subtitle')}}">
                    @if ($errors->has('error_subtitle'))
                    <p class="mb-0 text-danger">{{$errors->first('error_subtitle')}}</p>
                    @endif
                  </div>
                  <div class="form-group col-lg-6">
                    <label>Event Title **</label>
                    <input class="form-control" name="event_title"
                      value="{{empty(old('event_title')) ? $abs->event_title : old('event_title')}}">
                    @if ($errors->has('event_title'))
                    <p class="mb-0 text-danger">{{$errors->first('event_title')}}</p>
                    @endif
                  </div>
                  <div class="form-group col-lg-6">
                    <label>Event Subtitle **</label>
                    <input class="form-control" name="event_subtitle"
                      value="{{empty(old('event_subtitle')) ? $abs->event_subtitle : old('event_subtitle')}}">
                    @if ($errors->has('event_subtitle'))
                    <p class="mb-0 text-danger">{{$errors->first('event_subtitle')}}</p>
                    @endif
                  </div>
                  <div class="form-group col-lg-6">
                    <label>Event Details Title **</label>
                    <input class="form-control" name="event_details_title"
                      value="{{empty(old('event_details_title')) ? $abs->event_details_title : old('event_details_title')}}">
                    @if ($errors->has('event_details_title'))
                    <p class="mb-0 text-danger">{{$errors->first('event_details_title')}}</p>
                    @endif
                  </div>
                  <div class="form-group col-lg-6">
                    <label>Cause Title **</label>
                    <input class="form-control" name="cause_title"
                      value="{{empty(old('cause_title')) ? $abs->cause_title : old('cause_title')}}">
                    @if ($errors->has('cause_title'))
                    <p class="mb-0 text-danger">{{$errors->first('cause_title')}}</p>
                    @endif
                  </div>
                  <div class="form-group col-lg-6">
                    <label>Cause Subtitle **</label>
                    <input class="form-control" name="cause_subtitle"
                      value="{{empty(old('cause_subtitle')) ? $abs->cause_subtitle : old('cause_subtitle')}}">
                    @if ($errors->has('cause_subtitle'))
                    <p class="mb-0 text-danger">{{$errors->first('cause_subtitle')}}</p>
                    @endif
                  </div>
                  <div class="form-group col-lg-6">
                    <label>Cause Details Title **</label>
                    <input class="form-control" name="cause_details_title"
                      value="{{empty(old('cause_details_title')) ? $abs->cause_details_title : old('cause_details_title')}}">
                    @if ($errors->has('cause_details_title'))
                    <p class="mb-0 text-danger">{{$errors->first('cause_details_title')}}</p>
                    @endif
                  </div>
                  <div class="form-group col-lg-6">
                    <label>Client Feedback Title **</label>
                    <input class="form-control" name="client_feedback_title"
                      value="{{empty(old('client_feedback_title')) ? $abex->client_feedback_title : old('client_feedback_title')}}">
                    @if ($errors->has('client_feedback_title'))
                    <p class="mb-0 text-danger">{{$errors->first('client_feedback_title')}}</p>
                    @endif
                  </div>
                  <div class="form-group col-lg-6">
                    <label>Client Feedback Subtitle **</label>
                    <input class="form-control" name="client_feedback_subtitle"
                      value="{{empty(old('client_feedback_subtitle')) ? $abex->client_feedback_subtitle : old('client_feedback_subtitle')}}">
                    @if ($errors->has('client_feedback_subtitle'))
                    <p class="mb-0 text-danger">{{$errors->first('client_feedback_subtitle')}}</p>
                    @endif
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="card-footer">
          <div class="form">
            <div class="form-group from-show-notify row">
              <div class="col-12 text-center">
                <button type="submit" id="displayNotif" class="btn btn-success">Update</button>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
