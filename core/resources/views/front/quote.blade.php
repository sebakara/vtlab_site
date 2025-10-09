@extends("front.$version.layout")

@section('pagename')
 - {{__('Request A Quote')}}
@endsection

@section('meta-keywords', "$be->quote_meta_keywords")
@section('meta-description', "$be->quote_meta_description")

@section('breadcrumb-title', $bs->quote_title)
@section('breadcrumb-subtitle', $bs->quote_subtitle)
@section('breadcrumb-link', __('Quote Page'))

@section('content')


  <!--   quote area start   -->
  <div class="quote-area pt-115 pb-115">
    <div class="container">
      <div class="row">

        <div class="col-lg-12">
          <form action="{{route('front.sendquote')}}" enctype="multipart/form-data" method="POST">
            @csrf
            <div class="row">
                <div class="col-lg-6">
                    <div class="form-element mb-4">
                        <label>{{__('Name')}} <span>**</span></label>
                        <input name="name" type="text" value="{{old("name")}}" placeholder="{{__('Enter Name')}}">

                        @if ($errors->has("name"))
                        <p class="text-danger mb-0">{{$errors->first("name")}}</p>
                        @endif
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-element mb-4">
                        <label>{{__('Email')}} <span>**</span></label>
                        <input name="email" type="text" value="{{old("email")}}" placeholder="{{__('Enter Email Address')}}">

                        @if ($errors->has("email"))
                        <p class="text-danger mb-0">{{$errors->first("email")}}</p>
                        @endif
                    </div>
                </div>

                @foreach ($inputs as $input)
                    <div class="{{$input->type == 4 || $input->type == 3 ? 'col-lg-12' : 'col-lg-6'}}">
                        <div class="form-element mb-4">
                            @if ($input->type == 1)
                                <label>{{convertUtf8($input->label)}} @if($input->required == 1) <span>**</span> @endif</label>
                                <input name="{{$input->name}}" type="text" value="{{old("$input->name")}}" placeholder="{{convertUtf8($input->placeholder)}}">
                            @endif

                            @if ($input->type == 2)
                                <label>{{convertUtf8($input->label)}} @if($input->required == 1) <span>**</span> @endif</label>
                                <select name="{{$input->name}}">
                                    <option value="" selected disabled>{{convertUtf8($input->placeholder)}}</option>
                                    @foreach ($input->quote_input_options as $option)
                                        <option value="{{convertUtf8($option->name)}}" {{old("$input->name") == convertUtf8($option->name) ? 'selected' : ''}}>{{convertUtf8($option->name)}}</option>
                                    @endforeach
                                </select>
                            @endif

                            @if ($input->type == 3)
                                <label>{{convertUtf8($input->label)}} @if($input->required == 1) <span>**</span> @endif</label>
                                @foreach ($input->quote_input_options as $option)
                                    <div class="custom-control custom-checkbox custom-control-inline">
                                        <input type="checkbox" id="customCheckboxInline{{$option->id}}" name="{{$input->name}}[]" class="custom-control-input" value="{{convertUtf8($option->name)}}" {{is_array(old("$input->name")) && in_array(convertUtf8($option->name), old("$input->name")) ? 'checked' : ''}}>
                                        <label class="custom-control-label" for="customCheckboxInline{{$option->id}}">{{convertUtf8($option->name)}}</label>
                                    </div>
                                @endforeach
                            @endif

                            @if ($input->type == 4)
                                <label>{{convertUtf8($input->label)}} @if($input->required == 1) <span>**</span> @endif</label>
                                <textarea name="{{$input->name}}" id="" cols="30" rows="10" placeholder="{{convertUtf8($input->placeholder)}}">{{old("$input->name")}}</textarea>
                            @endif

                            @if ($input->type == 6)
                                <label>{{convertUtf8($input->label)}} @if($input->required == 1) <span>**</span> @endif</label>
                                <input class="datepicker" name="{{$input->name}}" type="text" value="{{old("$input->name")}}" placeholder="{{convertUtf8($input->placeholder)}}" autocomplete="off">
                            @endif

                            @if ($input->type == 7)
                                <label>{{convertUtf8($input->label)}} @if($input->required == 1) <span>**</span> @endif</label>
                                <input class="timepicker" name="{{$input->name}}" type="text" value="{{old("$input->name")}}" placeholder="{{convertUtf8($input->placeholder)}}" autocomplete="off">
                            @endif

                            @if ($input->type == 5)
                            <div class="row">
                              <div class="col-lg-12">
                                <div class="form-element mb-2">
                                  <label>{{$input->label}} @if($input->required == 1) <span>**</span> @endif</label>
                                  <input type="file" name="{{$input->name}}" value="">
                                </div>
                                <p class="text-warning mb-0">** {{__('Only zip file is allowed')}}</p>
                              </div>
                            </div>
                            @endif

                            @if ($errors->has("$input->name"))
                            <p class="text-danger mb-0">{{$errors->first("$input->name")}}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>


            @if ($bs->is_recaptcha == 1)
              <div class="row mb-4">
                <div class="col-lg-12">
                  {!! NoCaptcha::renderJs() !!}
                  {!! NoCaptcha::display() !!}
                  @if ($errors->has('g-recaptcha-response'))
                    @php
                        $errmsg = $errors->first('g-recaptcha-response');
                    @endphp
                    <p class="text-danger mb-0">{{__("$errmsg")}}</p>
                  @endif
                </div>
              </div>
            @endif

            <div class="row">
              <div class="col-lg-12 text-center">
                <button type="submit" name="button">{{__('Submit')}}</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <!--   quote area end   -->
@endsection
