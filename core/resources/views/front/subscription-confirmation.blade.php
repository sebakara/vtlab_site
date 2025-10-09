@extends("front.$version.layout")

@section('pagename')
 - {{__('Order Confirmation')}}
@endsection

@section('no-breadcrumb', 'no-breadcrumb')

@section('content')

<div class="order-comfirmation pt-80 pb-80">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="confirmation-message">
                    <h2 class="text-center">{{__('Thank you for your purchase')}} !</h2>
                    <p class="text-center">
                        <a href="{{route('front.index')}}">{{__('Get Back To Our Homepage')}}</a>
                    </p>
                </div>

                @php
                    // if online gateway
                    if ($packageOrder->gateway_type == 'online') {
                        // if the subscription was already active,
                        if (!empty($packageOrder->next_package_id)) {
                            $package = $packageOrder->next_package;
                            if ($package->duration == 'monthly') {
                                $days = 30;
                            } else {
                                $days = 365;
                            }

                            $paymentMethod = $packageOrder->next_payment_method;
                            $activationDate = \Carbon\Carbon::parse($packageOrder->expire_date);
                            $expireDate = \Carbon\Carbon::parse($packageOrder->expire_date)->addDays($days);
                        } else {
                            $package = $packageOrder->current_package;
                            if ($package->duration == 'monthly') {
                                $days = 30;
                            } else {
                                $days = 365;
                            }

                            $paymentMethod = $packageOrder->current_payment_method;
                            $activationDate = \Carbon\Carbon::now();
                            $expireDate = \Carbon\Carbon::now()->addDays($days);
                        }

                        $activationDate = $activationDate->toFormattedDateString();
                        $expireDate = $expireDate->toFormattedDateString();
                    }
                    // if offline gateway
                    else {
                        $package = $packageOrder->pending_package;
                        $paymentMethod = $packageOrder->pending_payment_method;
                        $activationDate = "Activation Date will be notified via mail once Admin accepts the subscription request";
                        $expireDate = "Expire Date will be notified via mail once Admin accepts the subscription request";
                    }
                @endphp

                <div class="row">
                    <div class="col-lg-6">
                        <table class="table">
                            <thead class="thead-dark">
                              <tr>
                                <th scope="col" colspan="2">{{__('Order Details')}}</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr>
                                <th scope="row">{{__('Order Date')}}:</th>
                                <td>{{$packageOrder->created_at}}</td>
                              </tr>
                              <tr>
                                <th scope="row">{{__('Payment Method')}}:</th>
                                <td class="text-capitalize">
                                    {{$paymentMethod}}
                                </td>
                              </tr>
                              <tr>
                                <th scope="row">{{__('Activation Date')}}:</th>
                                <td class="text-capitalize">
                                    {{$activationDate}}
                                </td>
                              </tr>
                              <tr>
                                <th scope="row">{{__('Expire Date')}}:</th>
                                <td class="text-capitalize">
                                    {{$expireDate}}
                                </td>
                              </tr>
                            </tbody>
                        </table>

                        <table class="table">
                            <thead class="thead-dark">
                              <tr>
                                <th scope="col" colspan="2">{{__('Package Details')}}</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr>
                                <th scope="row">{{__('Title')}}:</th>
                                <td>{{$package->title}}</td>
                              </tr>
                              <tr>
                                <th scope="row">{{__('Price')}}:</th>
                                <td>{{$bex->base_currency_text_position == 'left' ? $bex->base_currency_text : ''}} {{$package->price}} {{$bex->base_currency_text_position == 'right' ? $bex->base_currency_text : ''}}</td>
                              </tr>
                              <tr>
                                <th scope="row">{{__('Type')}}:</th>
                                <td>{{$package->duration}}</td>
                              </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-lg-6">
                        <table class="table">
                            <thead class="thead-dark">
                              <tr>
                                <th scope="col" colspan="2">{{__('Client Details')}}</th>
                              </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th scope="row">{{__('Client Name')}}:</th>
                                    <td>{{$packageOrder->name}}</td>
                                </tr>
                                <tr>
                                    <th scope="row">{{__('Client Email')}}:</th>
                                    <td>{{$packageOrder->email}}</td>
                                </tr>
                                @foreach ($fields as $key => $field)
                                    @php
                                    if (is_array($field['value'])) {
                                        $str = implode(", ", $field['value']);
                                        $value = $str;
                                    } else {
                                        $value = $field['value'];
                                    }
                                    @endphp


                                    @if ($field['type'] != 5)
                                    <tr>
                                        <th scope="row" class="text-capitalize">{{str_replace("_"," ",$key)}}:</th>
                                        <td>
                                            {{$value}}
                                        </td>
                                    </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>
@endsection
