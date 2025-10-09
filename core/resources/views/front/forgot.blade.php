@extends("front.$version.layout")

@section('pagename')
 -
 {{__('Forgot Password')}}
@endsection

@section('meta-keywords', "$be->forgot_meta_keywords")
@section('meta-description', "$be->forgot_meta_description")

@section('breadcrumb-subtitle', __('Forgot Password'))
@section('breadcrumb-link', __('Forgot Password'))

@section('content')

<!--   hero area start    -->
<div class="login-area">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="login-content">
                    <div class="login-title">
                        <h3 class="title">{{__('Forgot Password')}}</h3>
                    </div>

                    <form  action="{{route('user-forgot-submit')}}" method="POST">
                        @csrf
                        <div class="input-box">
                            <span>{{__('Email')}} *</span>
                            <input type="email" name="email" value="{{Request::old('email')}}">
                            @error('email')
                            <p class="text-danger mb-2 mt-2">{{ convertUtf8($message) }}</p>
                            @enderror
                            @if(Session::has('err'))
                            <p class="text-danger mb-2 mt-2">{{ Session::get('err') }}</p>
                            @endif
                        </div>

                        <div class="input-btn mt-4">
                            <button type="submit">{{__('Send Mail')}}</button>
                            <p class="d-inline-block float-right"><a href="{{route('user.login')}}">{{__('Login Now')}}</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!--   hero area end    -->
@endsection
