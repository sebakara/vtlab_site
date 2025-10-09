<!-- Details Modal -->
<div class="modal fade" id="detailsModal{{$quote->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
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
                <div class="col-lg-8">{{convertUtf8($quote->name)}}</div>
            </div>
            <hr>
            <div class="row">
                <div class="col-lg-4">
                    <strong style="text-transform: capitalize;">Email:</strong>
                </div>
                <div class="col-lg-8">{{convertUtf8($quote->email)}}</div>
            </div>
            <hr>

          @php
            $fields = json_decode($quote->fields, true);
          @endphp

          @foreach ($fields as $key => $field)
          <div class="row">
            <div class="col-lg-4">
              <strong style="text-transform: capitalize;">{{str_replace("_"," ",$key)}}:</strong>
            </div>
            <div class="col-lg-8">
                @if (is_array($field) && array_key_exists('value', $field) && is_array($field['value']))
                    @php
                        $str = implode(", ", $field['value']);
                    @endphp
                    {{$str}}
                @else
                    @if(is_array($field))
                        @if (array_key_exists('type', $field) && $field['type'] == 5)
                            <a href="{{asset('assets/front/files/' . $field['value'])}}" class="btn btn-primary btn-sm" download="{{$key . ".zip"}}">Download</a>
                        @else
                            @if (array_key_exists('value', $field))
                                {{$field['value']}}
                            @endif
                        @endif
                    @endif
                @endif
            </div>
          </div>
          <hr>
          @endforeach

          <div class="row">
            <div class="col-lg-4">
              <strong>Status:</strong>
            </div>
            <div class="col-lg-8">
              @if ($quote->status == 0)
                <span class="badge badge-warning">Pending</span>
              @elseif ($quote->status == 1)
                <span class="badge badge-secondary">Processing</span>
              @elseif ($quote->status == 2)
                <span class="badge badge-success">Completed</span>
              @elseif ($quote->status == 3)
                <span class="badge badge-danger">Rejected</span>
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
