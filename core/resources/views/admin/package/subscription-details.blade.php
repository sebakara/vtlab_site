<!-- Details Modal -->
<div class="modal fade" id="detailsModal{{$sub->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
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
                  <div class="col-lg-8">{{$sub->name}}</div>
              </div>
              <hr>
              <div class="row">
                  <div class="col-lg-4">
                      <strong style="text-transform: capitalize;">Email:</strong>
                  </div>
                  <div class="col-lg-8">{{$sub->email}}</div>
              </div>
              <hr>

            @php
              $fields = json_decode($sub->fields, true);
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

            @if (request()->input('type') != 'request')
                @if ($sub->current_package)
                    <div class="row">
                        <div class="col-lg-4">
                            <strong>Current Package:</strong>
                        </div>
                        <div class="col-lg-8">
                            {{ $sub->current_package->title }}
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-lg-4">
                            <strong>Current Package Price:</strong>
                        </div>
                        <div class="col-lg-8">
                            {{$bex->base_currency_symbol_position == 'left' ? $bex->base_currency_symbol : ''}}
                            {{ $sub->current_package->price }}
                            {{$bex->base_currency_symbol_position == 'right' ? $bex->base_currency_symbol : ''}}
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-lg-4">
                            <strong>Current Package Payment Method:</strong>
                        </div>
                        <div class="col-lg-8">
                            {{$sub->current_payment_method}}
                        </div>
                    </div>
                    <hr>
                @endif

                @if ($sub->next_package)

                    <div class="row">
                        <div class="col-lg-4">
                            <strong>Next / Upcoming Package:</strong>
                        </div>
                        <div class="col-lg-8">
                            {{ $sub->next_package->title }}
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-lg-4">
                            <strong>Next Package Price:</strong>
                        </div>
                        <div class="col-lg-8">
                            {{$bex->base_currency_symbol_position == 'left' ? $bex->base_currency_symbol : ''}}
                            {{$sub->next_package->price}}
                            {{$bex->base_currency_symbol_position == 'right' ? $bex->base_currency_symbol : ''}}
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-lg-4">
                            <strong>Next Package Payment Method:</strong>
                        </div>
                        <div class="col-lg-8">
                            {{$sub->next_payment_method}}
                        </div>
                    </div>
                    <hr>
                @endif

                <div class="row">
                    <div class="col-lg-4">
                      <strong>Status:</strong>
                    </div>
                    <div class="col-lg-8">
                        @if ($sub->status == 1)
                            <span class="badge badge-success">Active</span>
                        @elseif ($sub->status == 0)
                            <span class="badge badge-danger">Expired</span>
                        @endif
                    </div>
                  </div>
                  <hr>
            @else

                @if ($sub->pending_package)
                    <div class="row">
                        <div class="col-lg-4">
                            <strong>Pending Package:</strong>
                        </div>
                        <div class="col-lg-8">
                            {{ $sub->pending_package->title }}
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-lg-4">
                            <strong>Pending Package Price:</strong>
                        </div>
                        <div class="col-lg-8">
                            {{$bex->base_currency_symbol_position == 'left' ? $bex->base_currency_symbol : ''}}
                            {{$sub->pending_package->price}}
                            {{$bex->base_currency_symbol_position == 'right' ? $bex->base_currency_symbol : ''}}
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-lg-4">
                            <strong>Pending Package Payment Method:</strong>
                        </div>
                        <div class="col-lg-8">
                            {{$sub->pending_payment_method}}
                        </div>
                    </div>
                    <hr>
                @endif
            @endif



          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
