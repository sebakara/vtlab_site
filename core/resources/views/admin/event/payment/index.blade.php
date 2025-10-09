@extends('admin.layout')

@php
    $selLang = \App\Language::where('code', request()->input('language'))->first();
@endphp
@if(!empty($selLang) && $selLang->rtl == 1)
@section('styles')
    <style>
        form:not(.modal-form) input,
        form:not(.modal-form) textarea,
        form:not(.modal-form) select,
        select[name='language'] {
            direction: rtl;
        }

        form:not(.modal-form) .note-editor.note-frame .note-editing-area .note-editable {
            direction: rtl;
            text-align: right;
        }
    </style>
@endsection
@endif

@section('content')
    <div class="page-header">
        <h4 class="page-title">Event Bookings</h4>
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
                <a href="#">Event Bookings</a>
            </li>
        </ul>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="card-title d-inline-block">Event Bookings</div>
                        </div>
                        <div class="col-lg-4 offset-lg-4 mt-2 mt-lg-0">
                            <button class="btn btn-danger float-right btn-md ml-4 d-none bulk-delete" data-href="{{route('admin.event.payment.bulk.delete')}}"><i class="flaticon-interface-5"></i> Delete</button>
                            <form action="{{url()->current()}}" class="d-inline-block float-right">
                                <input class="form-control" type="text" name="search"
                                       placeholder="Search by Transaction ID"
                                       value="{{request()->input('search') ? request()->input('search') : '' }}">
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12">
                            @if (count($events) == 0)
                                <h3 class="text-center">NO LOG FOUND</h3>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-striped mt-3">
                                        <thead>
                                        <tr>
                                            <th scope="col">
                                                <input type="checkbox" class="bulk-check" data-val="all">
                                            </th>
                                            <th scope="col" width="15%">Ticket ID</th>
                                            <th scope="col" width="15%">Event</th>
                                            <th scope="col">Amount</th>
                                            <th scope="col">Payment</th>
                                            <th scope="col">Receipt</th>
                                            <th scope="col">Actions</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($events as $key => $event)
                                            <tr>
                                                <td>
                                                    <input type="checkbox" class="bulk-check" data-val="{{$event->id}}">
                                                </td>
                                                <td>{{convertUtf8(strlen($event->transaction_id)) > 30 ? convertUtf8(substr($event->transaction_id, 0, 30)) . '...' : convertUtf8($event->transaction_id)}}</td>
                                                <td>{{strlen($event->event->title) > 30 ? mb_substr($event->event->title,0,30,'utf-8') . '...' : $event->event->title}}</td>
                                                <td>{{$event->currency_symbol . ' ' . convertUtf8($event->amount)}}</td>
                                                <td>
                                                    @if(json_decode($event->transaction_details) !== "offline")
                                                        @if ($event->status == 'Pending')
                                                            <span class="badge badge-warning">Pending</span>
                                                        @elseif ($event->status == 'Success')
                                                            <span class="badge badge-success">Success</span>
                                                        @endif
                                                    @else
                                                        <form id="statusForm{{$event->id}}" class="d-inline-block"
                                                              action="{{route('admin.event.payment.log.update')}}"
                                                              method="post">
                                                            @csrf
                                                            <input type="hidden" name="id" value="{{$event->id}}">
                                                            <select class="form-control form-control-sm
                                                            @if ($event->status == 'Success')
                                                                bg-success
                                                            @elseif ($event->status == 'Pending')
                                                                bg-warning
                                                            @elseif ($event->status == 'Rejected')
                                                                bg-danger
                                                            @endif
                                                                " name="status"
                                                                    onchange="document.getElementById('statusForm{{$event->id}}').submit();">
                                                                <option
                                                                    value="pending" {{$event->status == 'Pending' ? 'selected' : ''}}>
                                                                    Pending
                                                                </option>
                                                                <option
                                                                    value="success" {{$event->status == 'Success' ? 'selected' : ''}}>
                                                                    Success
                                                                </option>
                                                                <option
                                                                    value="rejected" {{$event->status == 'Rejected' ? 'selected' : ''}}>
                                                                    Rejected
                                                                </option>
                                                            </select>
                                                        </form>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (!empty($event->receipt))
                                                        <a class="btn btn-sm btn-info" href="#" data-toggle="modal"
                                                           data-target="#receiptModal{{$event->id}}">Show</a>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (!empty($event->name !== "anonymous"))
                                                        <a class="btn btn-sm btn-info" href="#" data-toggle="modal"
                                                           data-target="#detailsModal{{$event->id}}">Detail</a>
                                                    @else
                                                        -
                                                    @endif
                                                    <form class="deleteform d-inline-block" action="{{route('admin.event.payment.delete')}}" method="post">
                                                        @csrf
                                                        <input type="hidden" name="payment_id" value="{{$event->id}}">
                                                        <button type="submit" class="deletebtn btn btn-danger btn-sm">
                                                        Delete
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                            <div class="modal fade" id="receiptModal{{$event->id}}" tabindex="-1"
                                                 role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="exampleModalLabel">Receipt
                                                                Image</h5>
                                                            <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <img
                                                                src="{{asset('assets/front/img/events/receipt/' . $event->receipt)}}"
                                                                alt="Receipt" width="100%">
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                    data-dismiss="modal">Close
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade" id="detailsModal{{$event->id}}" tabindex="-1"
                                                 role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="exampleModalLabel">Details</h5>
                                                            <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <label>Event:</label>
                                                            <p class="d-inline-block mb-0">{{$event->event->title}}</p>
                                                            <hr>
                                                            <label>Ticket ID:</label>
                                                            <p class="d-inline-block mb-0">{{$event->transaction_id}}</p>
                                                            <hr>
                                                            <label>Amount:</label>
                                                            <p class="d-inline-block mb-0">{{$event->currency_symbol . ' ' . $event->amount}}</p>
                                                            <hr>
                                                            <label>Username:</label>
                                                            <p class="d-inline-block mb-0">{{$event->user ? $event->user->username : '-'}}</p>
                                                            <hr>
                                                            <label>Name:</label>
                                                            <p class="d-inline-block mb-0">{{$event->name}}</p>
                                                            <hr>
                                                            <label>Email:</label>
                                                            <p class="d-inline-block mb-0">{{$event->email}}</p>
                                                            <hr>
                                                            <label>Phone:</label>
                                                            <p class="d-inline-block mb-0">{{$event->phone}}</p>
                                                            <hr>
                                                            <label>Quantity:</label>
                                                            <p class="d-inline-block mb-0">{{$event->quantity}}</p>
                                                            <hr>
                                                            <label>Payment Method:</label>
                                                            <p class="d-inline-block mb-0">{{$event->payment_method}}</p>
                                                            <hr>
                                                            @if ($event->status == 'Success')
                                                            <label>Ticket:</label>
                                                            <p class="d-inline-block mb-0">
                                                                <a href="{{asset('assets/front/invoices/' . urlencode($event->invoice))}}" download="Ticket-{{$event->event->slug}}.pdf" class="btn btn-primary btn-sm">{{__('Download Ticket')}}</a>
                                                            </p>
                                                            <hr>
                                                            @endif
                                                            <label>Payment Status:</label>
                                                            @if ($event->status == 'Pending')
                                                                <span class="badge badge-warning">Pending</span>
                                                            @elseif ($event->status == 'Success')
                                                                <span class="badge badge-success">Success</span>
                                                            @elseif ($event->status == 'Rejected')
                                                                <span class="badge badge-danger">Rejected</span>
                                                            @endif
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                    data-dismiss="modal">Close
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="d-inline-block mx-auto">
                            {{$events->appends(['language' => request()->input('language')])->links()}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function () {
            // make input fields RTL
            $("select[name='lang_id']").on('change', function () {
                $(".request-loader").addClass("show");
                let url = "{{url('/')}}/admin/rtlcheck/" + $(this).val();
                $.get(url, function (data) {
                    $(".request-loader").removeClass("show");
                    if (data == 1) {
                        $("form input").each(function () {
                            if (!$(this).hasClass('ltr')) {
                                $(this).addClass('rtl');
                            }
                        });
                        $("form select").each(function () {
                            if (!$(this).hasClass('ltr')) {
                                $(this).addClass('rtl');
                            }
                        });
                        $("form textarea").each(function () {
                            if (!$(this).hasClass('ltr')) {
                                $(this).addClass('rtl');
                            }
                        });
                        $("form .summernote").each(function () {
                            $(this).siblings('.note-editor').find('.note-editable').addClass('rtl text-right');
                        });

                    } else {
                        $("form input, form select, form textarea").removeClass('rtl');
                        $("form.modal-form .summernote").siblings('.note-editor').find('.note-editable').removeClass('rtl text-right');
                    }
                })
            });

            // translatable portfolios will be available if the selected language is not 'Default'
            $("#language").on('change', function () {
                let language = $(this).val();
                if (language == 0) {
                    $("#translatable").attr('disabled', true);
                } else {
                    $("#translatable").removeAttr('disabled');
                }
            });
        });
    </script>
@endsection
