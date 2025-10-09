@extends('admin.layout')

@section('content')
  <div class="page-header">
    <h4 class="page-title">Client Feedbacks</h4>
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
        <a href="#">Client Feedbacks</a>
      </li>
    </ul>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-lg-6">
                    <div class="card-title d-inline-block">Feedbacks</div>
                </div>
                <div class="col-lg-6 mt-2 mt-lg-0">
                  <button class="btn btn-danger float-right btn-sm mr-2 d-none bulk-delete"
                    data-href="{{route('admin.feedback.bulk.delete')}}"><i class="flaticon-interface-5"></i> Delete</button>
                </div>
            </div>
        </div>

        <div class="card-body">
          <div class="row">
            <div class="col-lg-12">
              @if (count($feedbacks) == 0)
                <h3 class="text-center">NO FEEDBACK FOUND</h3>
              @else
                <div class="table-responsive">
                  <table class="table table-striped mt-3">
                    <thead>
                      <tr>
                        <th scope="col">
                          <input type="checkbox" class="bulk-check" data-val="all">
                        </th>
                        <th scope="col">Name</th>
                        <th scope="col">Email</th>
                        <th scope="col">Subject</th>
                        <th scope="col">Rating</th>
                        <th scope="col">Feedback</th>
                        <th scope="col">Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($feedbacks as $feedback)
                      <tr>
                        <td>
                          <input type="checkbox" class="bulk-check" data-val="{{$feedback->id}}">
                        </td>
                        <td>{{ $feedback->name }}</td>
                        <td>{{ $feedback->email }}</td>
                        @php
                          $sub = str_replace('-', ' ', $feedback->subject);
                        @endphp
                        <td class="text-capitalize">{{ $sub }}</td>
                        <td>{{ $feedback->rating }}</td>
                        <td>
                          <a class="btn btn-sm btn-info" href="#" data-toggle="modal" data-target="#feedbackModal{{ $feedback->id }}">Show</a>
                        </td>
                        <td>
                          <form class="deleteform d-inline-block" action="{{route('admin.delete_feedback')}}" method="post">
                            @csrf
                            <input type="hidden" name="feedback_id" value="{{$feedback->id}}">
                            <button type="submit" class="btn btn-danger btn-sm deletebtn">
                              <span class="btn-label">
                                <i class="fas fa-trash"></i>
                              </span>
                              Delete
                            </button>
                          </form>
                        </td>
                      </tr>

                      @includeIf('admin.feedback.show_feedback')
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
              {{ $feedbacks->links() }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
