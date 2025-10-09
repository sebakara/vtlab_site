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
        <a href="#">Donations & Causes</a>
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
                            <label for="">Payment Status</label>
                            <select name="payment_status" class="form-control ml-1">
                                <option value="" selected>All</option>
                                <option value="Pending" {{request()->input('payment_status') == 'Pending' ? 'selected' : ''}}>Pending</option>
                                <option value="Success" {{request()->input('payment_status') == 'Success' ? 'selected' : ''}}>Success</option>
                                <option value="Rejected" {{request()->input('payment_status') == 'Rejected' ? 'selected' : ''}}>Rejected</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-sm ml-1">Submit</button>
                        </div>
                    </form>
              </div>
              <div class="col-lg-2">
                <form action="{{route('admin.donation.export')}}" class="form-inline justify-content-end">
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
              @if (count($donations) > 0)
                <div class="table-responsive">
                  <table class="table table-striped mt-3">
                    <thead>
                      <tr>
                        <th scope="col">Donation ID</th>
                        <th scope="col">Name</th>
                        <th scope="col">Email</th>
                        <th scope="col">Phone</th>
                        <th scope="col" width=>Event</th>
                        <th scope="col">Amount</th>
                        <th scope="col">Gateway</th>
                        <th scope="col">Payment Status</th>
                        <th scope="col">Date</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($donations as $key => $donation)
                        <tr>
                          <td>#{{$donation->transaction_id}}</td>
                          <td>{{$donation->name ? $donation->name : '-'}}</td>
                          <td>{{$donation->email ? $donation->email : '-'}}</td>
                          <td>{{$donation->phone ? $donation->phone : '-'}}</td>
                          <td>{{strlen($donation->cause->title) > 20 ? mb_substr($donation->cause->title,0,20,'utf-8') . '...' : $donation->cause->title}}</td>
                          <td>{{$bex->base_currency_symbol_position == 'left' ? $bex->base_currency_symbol : ''}}{{$donation->amount}}{{$bex->base_currency_symbol_position == 'right' ? $bex->base_currency_symbol : ''}}</td>
                          <td>{{$donation->payment_method}}</td>
                          <td>
                            @if ($donation->status == 'Success')
                                <span class="badge badge-success">Success</span>
                            @elseif ($donation->status == 'Pending')
                                <span class="badge badge-warning">Pending</span>
                            @elseif ($donation->status == 'Rejected')
                                <span class="badge badge-danger">Rejected</span>
                            @endif
                          </td>
                          <td>{{$donation->created_at}}</td>
                        </tr>

                      @endforeach
                    </tbody>
                  </table>
                </div>


              @endif
            </div>
          </div>
        </div>

        @if (!empty($donations))
            <div class="card-footer">
            <div class="row">
                <div class="d-inline-block mx-auto">
                {{$donations->links()}}
                </div>
            </div>
            </div>
        @endif
      </div>
    </div>
  </div>

@endsection
