@extends("front.$version.layout")

@section('pagename')
 - {{__('Team Members')}}
@endsection

@section('meta-keywords', "$be->team_meta_keywords")
@section('meta-description', "$be->team_meta_description")

@section('breadcrumb-title', $bs->team_title)
@section('breadcrumb-subtitle', $bs->team_subtitle)
@section('breadcrumb-link', __('Team Members'))

@section('content')

  <!--   team page start   -->
  <div class="team-page">
    <div class="container">
      <div class="row">
        @foreach ($members as $key => $member)
          <div class="col-lg-3 col-sm-6">
            <div class="single-team-member">
               <div class="team-img-wrapper">
                  <img class="lazy" data-src="{{asset('assets/front/img/members/'.$member->image)}}" alt="">
                  <div class="social-accounts">
                     <ul class="social-account-lists">
                        @if (!empty($member->facebook))
                          <li class="single-social-account"><a href="{{$member->facebook}}"><i class="fab fa-facebook-f"></i></a></li>
                        @endif
                        @if (!empty($member->twitter))
                          <li class="single-social-account"><a href="{{$member->twitter}}"><i class="fab fa-twitter"></i></a></li>
                        @endif
                        @if (!empty($member->linkedin))
                          <li class="single-social-account"><a href="{{$member->linkedin}}"><i class="fab fa-linkedin-in"></i></a></li>
                        @endif
                        @if (!empty($member->instagram))
                          <li class="single-social-account"><a href="{{$member->instagram}}"><i class="fab fa-instagram"></i></a></li>
                        @endif
                     </ul>
                  </div>
               </div>
               <div class="member-info">
                  <h5 class="member-name">{{convertUtf8($member->name)}}</h5>
                  <small>{{convertUtf8($member->rank)}}</small>
               </div>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </div>
  <!--   team page end   -->
@endsection
