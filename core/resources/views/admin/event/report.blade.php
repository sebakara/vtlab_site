@extends('admin.layout')

@section('content')
  <div class="page-header">
    <h4 class="page-title">
        Report
    </h4>
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
        <a href="#">Events Management</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">Report</a>
      </li>
    </ul>
  </div>
  <div class="row">
    <div class="col-md-12">

      <div class="card">
        <div class="card-header p-1">
            <div class="row">
                <div class="col-lg-10">
                    <form action="{{url()->full()}}" class="form-inline">
                        <div class="form-group">
                            <label for="">From</label>
                            <input class="form-control datepicker" type="text" name="from_date" placeholder="From" value="{{request()->input('from_date') ? request()->input('from_date') : '' }}" required autocomplete="off" />
                        </div>

                        <div class="form-group">
                            <label for="">To</label>
                            <input class="form-control datepicker ml-1" type="text" name="to_date" placeholder="To" value="{{request()->input('to_date') ? request()->input('to_date') : '' }}" required autocomplete="off" />
                        </div>

                        <div class="form-group">
                            <label for="">Payment Method</label>
                            <select name="payment_method" class="form-control ml-1">
                                <option value="" selected>All</option>
                                @if (!empty($onPms))
                                    @foreach ($onPms as $onPm)
                                    <option value="{{$onPm->name}}" {{request()->input('payment_method') == $onPm->name ? 'selected' : ''}}>{{$onPm->name}}</option>
                                    @endforeach
                                @endif
                                @if (!empty($offPms))
                                    @foreach ($offPms as $offPm)
                                    <option value="{{$offPm->name}}" {{request()->input('payment_method') == $offPm->name ? 'selected' : ''}}>{{$offPm->name}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="">Status</label>
                            <select name="status" class="form-control ml-1">
                                <option value="" selected>All</option>
                                <option value="pending" {{request()->input('status') == 'pending' ? 'selected' : ''}}>Pending</option>
                                <option value="success" {{request()->input('status') == 'success' ? 'selected' : ''}}>Success</option>
                                <option value="rejected" {{request()->input('status') == 'rejected' ? 'selected' : ''}}>Rejected</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-sm ml-1">Submit</button>
                        </div>
                    </form>
              </div>
              <div class="col-lg-2">
                <form action="{{route('admin.event.export')}}" class="form-inline justify-content-end">
                    <div class="form-group">
                        <button type="submit" class="btn btn-success btn-sm ml-1" title="CSV Format">Export</button>
                    </div>
                </form>
              </div>
            </div>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-lg-12">
              @if (count($bookings) > 0)
                <div class="table-responsive">
                  <table class="table table-striped mt-3">
                    <thead>
                      <tr>
                        <th scope="col">Ticket ID</th>
                        <th scope="col">Name</th>
                        <th scope="col">Email</th>
                        <th scope="col">Phone</th>
                        <th scope="col" width=>Event</th>
                        <th scope="col">Amount</th>
                        <th scope="col">Quantity</th>
                        <th scope="col">Gateway</th>
                        <th scope="col">Payment Status</th>
                        <th scope="col">Date</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($bookings as $key => $booking)
                        <tr>
                          <td>#{{$booking->transaction_id}}</td>
                          <td>{{$booking->name}}</td>
                          <td>{{$booking->email}}</td>
                          <td>{{$booking->phone}}</td>
                          <td>{{strlen($booking->event->title) > 20 ? mb_substr($booking->event->title,0,20,'utf-8') . '...' : $booking->event->title}}</td>
                          <td>{{$bex->base_currency_symbol_position == 'left' ? $bex->base_currency_symbol : ''}}{{$booking->amount}}{{$bex->base_currency_symbol_position == 'right' ? $bex->base_currency_symbol : ''}}</td>
                          <td>{{$booking->quantity}}</td>
                          <td>{{$booking->payment_method}}</td>
                          <td>
                              @if (strtolower($booking->status) == 'pending')
                                  <span class="badge badge-warning">Pending</span>
                              @elseif (strtolower($booking->status) == 'success')
                                  <span class="badge badge-success">Success</span>
                              @elseif (strtolower($booking->status) == 'rejected')
                                  <span class="badge badge-danger">Rejected</span>
                              @endif
                          </td>
                          <td>{{$booking->created_at}}</td>
                        </tr>

                      @endforeach
                    </tbody>
                  </table>
                </div>


              @endif
            </div>
          </div>
        </div>

        @if (!empty($bookings))
            <div class="card-footer">
            <div class="row">
                <div class="d-inline-block mx-auto">
                {{$bookings->links()}}
                </div>
            </div>
            </div>
        @endif
      </div>
    </div>
  </div>

@endsection
