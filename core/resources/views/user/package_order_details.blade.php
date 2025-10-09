@extends('user.layout')

@section('pagename')
 - {{__('Order')}} [{{$data->order_number}}]
@endsection

@section('styles')
    <style>
        p {
            word-break: break-word;
        }
    </style>
@endsection

@section('content')
    <!--   hero area start   -->
    <div class="breadcrumb-area services service-bg" style="background-image: url('{{asset  ('assets/front/img/' . $bs->breadcrumb)}}');background-size:cover;">
        <div class="container">
            <div class="breadcrumb-txt">
                <div class="row">
                    <div class="col-xl-7 col-lg-8 col-sm-10">
                        <h1>{{__('Order Details')}}</h1>
                        <ul class="breadcumb">
                            <li><a href="{{route('user-dashboard')}}">{{__('Dashboard')}}</a></li>
                            <li>{{__('Order Details')}}</li>
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
                                <div class="order-details">
                                    <div class="title">
                                        <h4>{{__('Package Order Details')}}</h4>
                                    </div>
                                    <div id="print">
                                    <div class="view-order-page">
                                        <div class="order-info-area">
                                            <div class="row align-items-center">
                                                <div class="col-lg-8">
                                                   <div class="order-info">
                                                       <h3>{{__('Order')}} {{$data->order_id}} [{{$data->order_number}}]</h3>
                                                   <p><strong>{{__('Order Date')}}</strong> {{$data->created_at->format('d-m-Y')}}</p>
                                                   </div>
                                                </div>
                                                <div class="col-lg-4 print-btn">
                                                    <div class="prinit">
                                                        <a href="{{asset('assets/front/invoices/' . $data->invoice)}}" download="invoice.pdf" id="print-click" class="btn"><i class="fas fa-print"></i>{{__('Download Invoice')}}</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="billing-add-area">
                                        <div class="row">

                                            <div class="col-md-4 ">
                                                <div class="payment-information">
                                                    <h5 class="text-white">{{__('Order Details')}} : </h5>
                                                    <p><span class="text-white">{{__('Order Number')}}</span> : #{{$data->order_number}}</p>
                                                    <p><span class="text-white">{{__('Payment Status')}}</span> :
                                                        @if($data->payment_status == 0)
                                                        <span class="badge badge-warning">{{__('Pending')}}  </span>
                                                        @else
                                                        <span class="badge badge-success">{{__('Completed')}}  </span>
                                                        @endif
                                                    </p>
                                                    <p><span class="text-white">{{__('Payment Method')}}</span> : {{$data->method}}</p>
                                                    <p><span class="text-white">{{__('Date')}}</span> : {{$data->created_at}}</p>

                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="main-info">
                                                    <h5>{{__('Package Details')}}</h5>
                                                    <ul class="list">
                                                        <li><p><span>{{__('Title')}}:</span>{{$data->package_title}}</p></li>
                                                        <li><p><span>{{__('Price')}}:</span>
                                                            {{$bex->base_currency_symbol_position == 'left' ? $bex->base_currency_symbol : ''}}

                                                            {{$data->package_price}}

                                                            {{$bex->base_currency_symbol_position == 'right' ? $bex->base_currency_symbol : ''}}
                                                        </p></li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="main-info">
                                                    <h5>{{__('Client Details')}}</h5>
                                                    <ul class="list">
                                                        <li><p><span>{{__('Name')}}:</span>{{$data->name}}</p></li>
                                                        <li><p><span>{{__('Email')}}:</span>{{$data->email}}</p></li>
                                                        @php
                                                            $fields = json_decode($data->fields, true);
                                                        @endphp

                                                        @if (!empty($fields))
                                                            @foreach ($fields as $key => $field)
                                                                @if (is_array($field['value']))
                                                                    @php
                                                                        $str = implode(", ", $field['value']);
                                                                    @endphp
                                                                    <li><p><span>{{str_replace("_"," ",$key)}}:</span>{{$str}}</p></li>
                                                                @else
                                                                    @if ($field['type'] == 5)
                                                                        <li class="mb-2">
                                                                            <p><span>{{str_replace("_"," ",$key)}}:</span>
                                                                                <a href="{{asset('assets/front/files/' . $field['value'])}}" class="btn btn-primary btn-sm" download="{{$key . ".zip"}}">Download</a>
                                                                            </p>
                                                                        </li>
                                                                    @else
                                                                        <li><p><span>{{str_replace("_"," ",$key)}}:</span>{{$field['value']}}</p></li>
                                                                    @endif
                                                                @endif
                                                            @endforeach
                                                        @endif

                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    </div>
                                    <div class="edit-account-info">
                                        <a href="{{ URL::previous() }}" class="btn btn-primary">{{__('Back')}}</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

