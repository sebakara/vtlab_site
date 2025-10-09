@extends('user.layout')

@section('pagename')
 - {{__('Packages')}}
@endsection

@section('content')
  <!--   hero area start   -->
  <div class="breadcrumb-area services service-bg" style="background-image: url('{{asset  ('assets/front/img/' . $bs->breadcrumb)}}');background-size:cover;">
    <div class="container">
        <div class="breadcrumb-txt">
            <div class="row">
                <div class="col-xl-7 col-lg-8 col-sm-10">
                    <h1>{{__('Packages')}}</h1>
                    <ul class="breadcumb">
                        <li><a href="{{route('user-dashboard')}}">{{__('Dashboard')}}</a></li>
                        <li>{{__('Packages')}}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="breadcrumb-area-overlay"></div>
</div>
<!--   hero area end    -->


<!--====== CHECKOUT PART START ======-->
<section class="user-dashbord">
    <div class="container">
        <div class="row">
            @include('user.inc.site_bar')
            <div class="col-lg-9">
                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-warning" style="border-left: 5px solid #000;">
                            @if ($activeSub->count() == 0)
                                {{__("You didn't purchase any package")}}.
                            @endif
                            @if ($activeSub->count() > 0 && !empty($activeSub->first()->next_package_id))
                                {{__('You already have another package in stock to activate along side the current package.')}} <br>
                                {{__('You cannot purchase / extend / change to any package, until the next package is activated.')}}<br><br>
                            @endif
                            @if ($activeSub->count() > 0)
                                <strong>{{__('Current Package')}}:</strong> {{$activeSub->first()->current_package->title}} ({{__('Expire Date')}}: {{\Carbon\Carbon::parse($activeSub->first()->expire_date)->toFormattedDateString() }})<br>
                            @endif
                            @if ($activeSub->count() > 0 && !empty($activeSub->first()->next_package_id))
                                <strong>{{__('Next Package to Activate')}}:</strong> {{$activeSub->first()->next_package->title}}<br>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="user-profile-details">
                            <div class="account-info">
                                <div class="title">
                                    <h4>{{__('Packages')}}</h4>
                                </div>
                                <div class="main-info">
                                    <div class="main-table">
                                    <div class="table-responsiv">
                                        <table id="packagesTable" class="dataTables_wrapper dt-responsive table-striped dt-bootstrap4" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th>{{__('Title')}}</th>
                                                    <th>{{__('Price')}}</th>
                                                    <th>{{__('Duration')}}</th>
                                                    @if ($activeSub->count() == 0 || ($activeSub->count() > 0 && empty($activeSub->first()->next_package_id)))
                                                    <th>{{__('Action')}}</th>
                                                    @endif
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if($packages)
                                                @foreach ($packages as $package)
                                                <tr>
                                                    <td>{{$package->title}}</td>
                                                    <td>{{$bex->base_currency_symbol_position == 'left' ? $bex->base_currency_symbol : ''}} {{$package->price}} {{$bex->base_currency_symbol_position == 'right' ? $bex->base_currency_symbol : ''}}</td>
                                                    <td>{{$package->duration == 'monthly' ? __('Monthly') : __('Yearly')}}</td>
                                                    @if ($activeSub->count() > 0 && empty($activeSub->first()->next_package_id))
                                                        <td>
                                                            @if ($activeSub->first()->current_package_id == $package->id)
                                                                <a href="{{route('front.packageorder.index',$package->id)}}" class="btn base-bg text-white">{{__('Extend')}}</a>
                                                            @else
                                                                <a href="{{route('front.packageorder.index',$package->id)}}" class="btn base-bg text-white">{{__('Change')}}</a>
                                                            @endif
                                                        </td>
                                                    @elseif ($activeSub->count() == 0)
                                                        <td>
                                                            <a href="{{route('front.packageorder.index',$package->id)}}" class="btn base-bg text-white">{{__('Purchase')}}</a>
                                                        </td>
                                                    @endif
                                                </tr>
                                                @endforeach
                                                @else
                                                <tr class="text-center">
                                                    <td colspan="4">
                                                        {{__('No Packages')}}
                                                    </td>
                                                </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!--    footer section start   -->
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#packagesTable').DataTable({
            responsive: true,
            ordering: false
        });
    });
</script>
@endsection

