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
        <a href="#">Package Management</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">Manage Orders</a>
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
                            <label for="">Order Status</label>
                            <select name="order_status" class="form-control ml-1">
                                <option value="" selected>All</option>
                                <option value="0" {{request()->input('order_status') === "0" ? 'selected' : ''}}>Pending</option>
                                <option value="1" {{request()->input('order_status') == 1 ? 'selected' : ''}}>Processing</option>
                                <option value="2" {{request()->input('order_status') == 2 ? 'selected' : ''}}>Completed</option>
                                <option value="3" {{request()->input('order_status') == 3 ? 'selected' : ''}}>Rejected</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="">Payment Status</label>
                            <select name="payment_status" class="form-control ml-1">
                                <option value="" selected>All</option>
                                <option value="0" {{request()->input('payment_status') === "0" ? 'selected' : ''}}>Pending</option>
                                <option value="1" {{request()->input('payment_status') == 1 ? 'selected' : ''}}>Completed</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-sm ml-1">Submit</button>
                        </div>
                    </form>
              </div>
              <div class="col-lg-2">
                <form action="{{route('admin.package.export')}}" class="form-inline justify-content-end">
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
              @if (count($pos) > 0)
                <div class="table-responsive">
                  <table class="table table-striped mt-3">
                    <thead>
                      <tr>
                        <th scope="col">Order Number</th>
                        <th scope="col">Name</th>
                        <th scope="col">Email</th>
                        <th scope="col">Package</th>
                        <th scope="col">Amount</th>
                        <th scope="col">Gateway</th>
                        <th scope="col">Order Status</th>
                        <th scope="col">Payment Status</th>
                        <th scope="col">Date</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($pos as $key => $po)
                        <tr>
                          <td>#{{$po->order_number}}</td>
                          <td>{{$po->name}}</td>
                          <td>{{$po->email}}</td>
                          <td>{{$po->package_title}}</td>
                          <td>{{$bex->base_currency_symbol_position == 'left' ? $bex->base_currency_symbol : ''}}{{$po->package_price}}{{$bex->base_currency_symbol_position == 'right' ? $bex->base_currency_symbol : ''}}</td>
                          <td>{{ucfirst($po->method)}}</td>
                          <td>
                            @if ($po->status == 0)
                                <span class="badge badge-warning">Pending</span>
                            @elseif($po->status == 1)
                                <span class="badge badge-primary">Processing</span>
                            @elseif($po->status == 2)
                                <span class="badge badge-success">Completed</span>
                            @elseif($po->status == 3)
                                <span class="badge badge-danger">Rejected</span>
                            @endif
                          </td>
                          <td>
                            @if ($po->payment_status == 0)
                                <span class="badge badge-warning">Pending</span>
                            @elseif($po->payment_status == 1)
                                <span class="badge badge-success">Completed</span>
                            @endif
                          </td>
                          <td>{{$po->created_at}}</td>
                        </tr>

                      @endforeach
                    </tbody>
                  </table>
                </div>


              @endif
            </div>
          </div>
        </div>

        @if (!empty($pos))
            <div class="card-footer">
            <div class="row">
                <div class="d-inline-block mx-auto">
                {{$pos->links()}}
                </div>
            </div>
            </div>
        @endif
      </div>
    </div>
  </div>

@endsection
