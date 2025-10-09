<!-- Details Modal -->
<div class="modal fade" id="detailsModal{{$purchase->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-4">
                            <strong style="text-transform: capitalize;">Username:</strong>
                        </div>
                        <div class="col-lg-8">{{convertUtf8($purchase->user->username)}}</div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-lg-4">
                            <strong style="text-transform: capitalize;">First Name:</strong>
                        </div>
                        <div class="col-lg-8">{{convertUtf8($purchase->first_name)}}</div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-lg-4">
                            <strong style="text-transform: capitalize;">Last Name:</strong>
                        </div>
                        <div class="col-lg-8">{{convertUtf8($purchase->last_name)}}</div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-lg-4">
                            <strong style="text-transform: capitalize;">Email:</strong>
                        </div>
                        <div class="col-lg-8">{{convertUtf8($purchase->email)}}</div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-lg-4">
                            <strong style="text-transform: capitalize;">Course:</strong>
                        </div>
                        <div class="col-lg-8">{{convertUtf8($purchase->course->title)}}</div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-lg-4">
                            <strong style="text-transform: capitalize;">Course Price:</strong>
                        </div>
                        <div class="col-lg-8">{{convertUtf8($purchase->current_price)}} {{$purchase->currency_code}}</div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-lg-4">
                            <strong style="text-transform: capitalize;">Payment Method:</strong>
                        </div>
                        <div class="col-lg-8">{{convertUtf8($purchase->payment_method)}}</div>
                    </div>
                    <hr>

                    <div class="row">
                        <div class="col-lg-4">
                            <strong>Status:</strong>
                        </div>
                        <div class="col-lg-8">
                            @if (strtolower($purchase->payment_status) == 'completed')
                            <span class="badge badge-success">Completed</span>
                            @else
                            <span class="badge badge-warning">Incomplete</span>
                            @endif
                        </div>
                    </div>
                    <hr>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
