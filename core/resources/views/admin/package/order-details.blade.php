<!-- Details Modal -->
<div class="modal fade" id="detailsModal{{$order->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
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
                    <strong style="text-transform: capitalize;">Name:</strong>
                </div>
                <div class="col-lg-8">{{$order->name}}</div>
            </div>
            <hr>
            <div class="row">
                <div class="col-lg-4">
                    <strong style="text-transform: capitalize;">Email:</strong>
                </div>
                <div class="col-lg-8">{{$order->email}}</div>
            </div>
            <hr>

          @php
            $fields = json_decode($order->fields, true);
            // dd($fields)
          @endphp

          @foreach ($fields as $key => $field)
          <div class="row">
            <div class="col-lg-4">
              <strong style="text-transform: capitalize;">{{str_replace("_"," ",$key)}}:</strong>
            </div>
            <div class="col-lg-8">
                @if (is_array($field['value']))
                    @php
                        $str = implode(", ", $field['value']);
                    @endphp
                    {{$str}}
                @else
                    @if ($field['type'] == 5)
                        <a href="{{asset('assets/front/files/' . $field['value'])}}" class="btn btn-primary btn-sm" download="{{$key . ".zip"}}">Download</a>
                    @else
                        {{$field['value']}}
                    @endif
                @endif
            </div>
          </div>
          <hr>
          @endforeach
          <div class="row">
            <div class="col-lg-4">
              <strong>Package Title:</strong>
            </div>
            <div class="col-lg-8">
              {{convertUtf8($order->package_title)}}
            </div>
          </div>
          <hr>
          <div class="row">
            <div class="col-lg-4">
              <strong>Package Price:</strong>
            </div>
            <div class="col-lg-8">
                {{$bex->base_currency_symbol_position == 'left' ? $bex->base_currency_symbol : ''}}{{convertUtf8($order->package_price)}}{{$bex->base_currency_symbol_position == 'right' ? $bex->base_currency_symbol : ''}}
            </div>
          </div>
          <hr>
          <div class="row">
            <div class="col-lg-4">
              <strong>Payment Method:</strong>
            </div>
            <div class="col-lg-8">
              {{$order->method}}
            </div>
          </div>
          <hr>
          <div class="row">
            <div class="col-lg-4">
              <strong>Payment Status:</strong>
            </div>
            <div class="col-lg-8">
                @if ($order->payment_status == 1)
                    <span class="badge badge-success">Completed</span>
                @elseif ($order->payment_status == 0)
                    <span class="badge badge-danger">Incomplete</span>
                @endif
            </div>
          </div>
          <hr>
          <div class="row">
            <div class="col-lg-4">
              <strong>Order Date:</strong>
            </div>
            <div class="col-lg-8">
              {{$order->created_at}}
            </div>
          </div>
          <hr>
          <div class="row">
            <div class="col-lg-4">
              <strong>Status:</strong>
            </div>
            <div class="col-lg-8">
              @if ($order->status == 0)
                <span class="badge badge-warning">Pending</span>
              @elseif ($order->status == 1)
                <span class="badge badge-secondary">Processing</span>
              @elseif ($order->status == 2)
                <span class="badge badge-success">Completed</span>
              @elseif ($order->status == 3)
                <span class="badge badge-danger">Rejected</span>
              @endif
            </div>
          </div>
          <hr>
          <div class="row">
            <div class="col-lg-4">
              <strong>Package Description:</strong>
            </div>
            <div class="col-lg-8">
              {!! $order->package_description !!}
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
