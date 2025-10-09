<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Event Ticket</title>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="{{asset('assets/front/css/bootstrap3.min.css')}}">
    <style>
        .list-group-item span {
            float: right;
        }
    </style>
</head>
<body>
    <div class="row">
        <div class="col-xs-12 text-center">
            <img src="{{asset('assets/front/img/' . $bs->logo)}}" alt="">
        </div>
    </div>
    <div class="row">
        <div class="col-xs-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                  <h4><strong>Booking Details</strong></h4>
                </div>
                <ul class="list-group">
                  <li class="list-group-item">
                      <strong>Ticket ID:</strong>
                      <span>{{$event->transaction_id}}</span>
                  </li>
                  <li class="list-group-item">
                      <strong>Quantity:</strong>
                      <span>{{$event->quantity}}</span>
                  </li>
                  <li class="list-group-item">
                      <strong>Cost:</strong>
                      <span>
                        {{$bex->base_currency_symbol_position == 'left' ? $bex->base_currency_symbol : ''}}{{$event->event->cost}}{{$bex->base_currency_symbol_position == 'right' ? $bex->base_currency_symbol : ''}}
                        *
                        {{$event->quantity}}
                        =
                        {{$bex->base_currency_symbol_position == 'left' ? $bex->base_currency_symbol : ''}}{{$event->amount}}{{$bex->base_currency_symbol_position == 'right' ? $bex->base_currency_symbol : ''}}
                      </span>
                  </li>
                  <li class="list-group-item">
                      <strong>Booking Date:</strong>
                      <span>{{$event->created_at->format('d-m-Y')}}</span>
                  </li>
                </ul>
            </div>
        </div>
        <div class="col-xs-6 text-center">
            <br>
            <img src="data:image/png;base64, {!! base64_encode(QrCode::format('svg')->size(150)->generate("$event->transaction_id")) !!} ">
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                  <h4><strong>Event Details</strong></h4>
                </div>
                <ul class="list-group">
                  <li class="list-group-item">
                      <strong>Name:</strong>
                      <span>{{$event->event->title}}</span>
                  </li>
                  <li class="list-group-item">
                      <strong>Venue:</strong>
                      <span>{{$event->event->venue}}</span>
                  </li>
                  @if (!empty($event->venue_location))
                    <li class="list-group-item">
                        <strong>Location:</strong>
                        <span>{{$event->event->venue_location}}</span>
                    </li>
                  @endif
                  <li class="list-group-item">
                      <strong>Date:</strong>
                      <span>{{$event->event->date}}</span>
                  </li>
                  <li class="list-group-item">
                      <strong>Time:</strong>
                      <span>{{$event->event->time}}</span>
                  </li>
                  <li class="list-group-item">
                      <strong>Organizer:</strong>
                      <span>{{$event->event->organizer}}</span>
                  </li>
                  @if (!empty($event->organizer_email))
                    <li class="list-group-item">
                        <strong>Organizer Email:</strong>
                        <span>{{$event->event->organizer_email}}</span>
                    </li>
                  @endif
                  @if (!empty($event->organizer_phone))
                    <li class="list-group-item">
                        <strong>Organizer Phone:</strong>
                        <span>{{$event->event->organizer_phone}}</span>
                    </li>
                  @endif
                </ul>
            </div>
        </div>
    </div>
</body>
</html>
