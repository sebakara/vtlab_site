@extends('admin.layout')

@section('content')
  <div class="page-header">
    <h4 class="page-title">
      @if (request()->path()=='admin/pending/orders')
        Pending
      @elseif (request()->path()=='admin/all/orders')
        All
      @elseif (request()->path()=='admin/processing/orders')
        Processing
      @elseif (request()->path()=='admin/completed/orders')
        Completed
      @elseif (request()->path()=='admin/rejected/orders')
        Rejcted
      @endif
      Subscriptions
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
        <a href="#">Subscriptions</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">
            @if (request()->input('type')=='all')
                All
            @elseif (request()->input('type')=='active')
                Active
            @elseif (request()->input('type')=='expired')
                Expired
            @endif
            Subscriptions
        </a>
      </li>
    </ul>
  </div>
  <div class="row">
    <div class="col-md-12">

      <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-lg-4">
                    <div class="card-title">
                        @if (request()->input('type')=='all')
                            All
                        @elseif (request()->input('type')=='active')
                            Active
                        @elseif (request()->input('type')=='expired')
                            Expired
                        @endif
                        Subscriptions
                    </div>
                </div>
                <div class="col-lg-7 offset-lg-1">
                    <button class="btn btn-danger float-right btn-md mr-2 d-none bulk-delete ml-2" data-href="{{route('admin.sub.bulk.delete')}}"><i class="flaticon-interface-5"></i> Delete</button>
                    <form class="float-right" action="{{route('admin.subscriptions')}}" id="searchForm">
                        <input type="hidden" name="type" value="{{request()->input('type')}}">
                        <div class="row no-gutters">
                            <div class="col-lg-6">
                                <input name="term" type="text" class="form-control" placeholder="Customer Name / Email" value="{{request()->input('term')}}">
                            </div>
                            <div class="col-lg-6">
                                <select name="package" class="form-control" onchange="document.getElementById('searchForm').submit()">
                                    <option value="">All Packages</option>
                                    @foreach ($packages as $package)
                                        <option value="{{$package->id}}" {{request()->input('package') == $package->id ? 'selected' : ''}}>{{$package->title}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-lg-12">
              @if (count($subscriptions) == 0)
                <h3 class="text-center">NO SUBSCRIPTION {{request()->input('type') == 'request' ? 'REQUEST' : ''}} FOUND</h3>
              @else
                <div class="table-responsive">
                  <table class="table table-striped mt-3">
                    <thead>
                      <tr>
                        <th scope="col">
                            <input type="checkbox" class="bulk-check" data-val="all">
                        </th>
                        <th scope="col">Name</th>
                        <th scope="col">Package</th>
                        @if (request()->input('type') == 'request')
                        <th scope="col">Decision</th>
                        @else
                        <th scope="col">Status</th>
                        @endif
                        <th scope="col">Details</th>
                        @if (request()->input('type') == 'request')
                        <th scope="col">Gateway</th>
                        <th scope="col">Receipt</th>
                        @endif
                        <th scope="col">Mail</th>
                        <th scope="col">Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($subscriptions as $key => $sub)
                        <tr>
                          <td>
                            <input type="checkbox" class="bulk-check" data-val="{{$sub->id}}">
                          </td>
                          <td>
                              {{$sub->name}}
                          </td>
                          <td>
                              @if (request()->input('type') == 'request')
                              {{$sub->pending_package ? $sub->pending_package->title : '-'}}
                              @else
                              {{$sub->current_package ? $sub->current_package->title : '-'}}
                              @endif
                          </td>
                          <td>
                              {{-- if subscription request exists --}}
                              @if (request()->input('type') == 'request')
                                <form action="{{route('admin.subscription.status')}}" class="inline-block" id="statusForm" method="POST">
                                    @csrf
                                    <input type="hidden" name="subscription_id" value="{{$sub->id}}">
                                    <select class="form-control form-control-sm" name="status" onchange="document.getElementById('statusForm').submit()">
                                        <option value="">Pending</option>
                                        <option value="accept">Accept</option>
                                        <option value="reject">Reject</option>
                                    </select>
                                </form>
                              @else
                                @if ($sub->status == 0)
                                    <span class="badge badge-danger"><i class="far fa-times-circle"></i> Expired</span>
                                @elseif ($sub->status == 1)
                                    <span class="badge badge-success"><i class="far fa-check-circle"></i> Active</span>
                                @endif
                              @endif
                          </td>
                          <td>
                              <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#detailsModal{{$sub->id}}">
                                  Details
                              </button>
                          </td>
                          @if (request()->input('type') == 'request')
                          <td>
                              {{$sub->pending_payment_method}}
                          </td>
                          <td>
                              @if (!empty($sub->receipt))
                                <a href="#" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#receiptModal{{$sub->id}}">Receipt</a>
                              @else
                                -
                              @endif
                          </td>
                          @endif
                          <td>
                              <button class="btn btn-secondary btn-sm editbtn" data-toggle="modal" data-target="#mailModal" data-email="{{$sub->email}}">Mail</button>
                          </td>
                          <td>
                            <form class="deleteform d-block" action="{{route('admin.package.subDelete')}}" method="post">
                                @csrf
                                <input type="hidden" name="subscription_id" value="{{$sub->id}}">
                                <button type="submit" class="btn btn-danger btn-sm deletebtn">
                                  Delete
                                </button>
                            </form>
                          </td>
                        </tr>


                        @if (request()->input('type') == 'request')
                        {{-- Receipt Modal --}}
                        <div class="modal fade" id="receiptModal{{$sub->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h5 class="modal-title" id="exampleModalLabel">Receipt Image</h5>
                                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                  </button>
                                </div>
                                <div class="modal-body">
                                    <img src="{{asset('assets/front/receipt/' . $sub->receipt)}}" alt="Receipt" width="100%">
                                </div>
                                <div class="modal-footer">
                                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                </div>
                              </div>
                            </div>
                        </div>
                        @endif

                        @includeif('admin.package.subscription-details')
                      @endforeach
                    </tbody>
                  </table>
                </div>

                <!-- Send Mail Modal -->
                <div class="modal fade" id="mailModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Send Mail</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                      </div>
                      <div class="modal-body">
                        <form id="ajaxEditForm" class="" action="{{route('admin.subscription.mail')}}" method="POST">
                          @csrf
                          <div class="form-group">
                            <label for="">Client Mail **</label>
                            <input id="inemail" type="text" class="form-control" name="email" value="" placeholder="Enter email">
                            <p id="eerremail" class="mb-0 text-danger em"></p>
                          </div>
                          <div class="form-group">
                            <label for="">Subject **</label>
                            <input id="insubject" type="text" class="form-control" name="subject" value="" placeholder="Enter subject">
                            <p id="eerrsubject" class="mb-0 text-danger em"></p>
                          </div>
                          <div class="form-group">
                            <label for="">Message **</label>
                            <textarea id="inmessage" class="form-control summernote" name="message" placeholder="Enter message" data-height="150"></textarea>
                            <p id="eerrmessage" class="mb-0 text-danger em"></p>
                          </div>
                        </form>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button id="updateBtn" type="button" class="btn btn-primary">Send Mail</button>
                      </div>
                    </div>
                  </div>
                </div>
              @endif
            </div>
          </div>
        </div>
        <div class="card-footer">
          <div class="row">
            <div class="d-inline-block mx-auto">
                {{ $subscriptions->links() }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

@endsection
