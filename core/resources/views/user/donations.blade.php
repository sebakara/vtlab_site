@extends('user.layout')

@section('pagename')
 - {{__('Orders')}}
@endsection

@section('content')
  <!--   hero area start   -->
  <div class="breadcrumb-area services service-bg" style="background-image: url('{{asset  ('assets/front/img/' . $bs->breadcrumb)}}');background-size:cover;">
    <div class="container">
        <div class="breadcrumb-txt">
            <div class="row">
                <div class="col-xl-7 col-lg-8 col-sm-10">
                    <h1>{{__('Donations')}}</h1>
                    <ul class="breadcumb">
                        <li><a href="{{route('user-dashboard')}}">{{__('Dashboard')}}</a></li>
                        <li>{{__('Donations')}}</li>
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
                    <div class="col-lg-12">
                        <div class="user-profile-details">
                            <div class="account-info">
                                <div class="title">
                                    <h4>{{__('Donations')}}</h4>
                                </div>
                                <div class="main-info">
                                    <div class="main-table">
                                    <div class="table-responsiv">
                                        <table id="donationTable" class="dataTables_wrapper dt-responsive table-striped dt-bootstrap4" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th>{{__('Transaction ID')}}</th>
                                                    <th>{{__('Cause')}}</th>
                                                    <th>{{__('Amount')}}</th>
                                                    <th>{{__('Gateway')}}</th>
                                                    <th>{{__('Payment')}}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if($donations)
                                                @foreach ($donations as $donation)
                                                <tr>
                                                    <td>{{$donation->transaction_id}}</td>
                                                     <td>{{$donation->cause ? $donation->cause->title : ''}}</td>
                                                    <td>{{$bex->base_currency_symbol_position == 'left' ? $bex->base_currency_symbol : ''}} {{$donation->amount}} {{$bex->base_currency_symbol_position == 'right' ? $bex->base_currency_symbol : ''}}</td>
                                                    <td>{{$donation->payment_method}}</td>
                                                    <td>
                                                        @if ($donation->status == 'Success')
                                                            <span class="badge badge-success">{{__('Completed')}}</span>
                                                        @elseif ($donation->status == 'Pending')
                                                            <span class="badge badge-warning">{{__('Pending')}}</span>
                                                        @elseif ($donation->status == 'Rejected')
                                                            <span class="badge badge-danger">{{__('Rejected')}}</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                                @else
                                                <tr class="text-center">
                                                    <td colspan="4">
                                                        {{__('No Donation Found')}}
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
        $('#donationTable').DataTable({
            responsive: true,
            ordering: false
        });
    });
</script>
@endsection

