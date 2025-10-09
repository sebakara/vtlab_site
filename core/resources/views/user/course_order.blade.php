@extends('user.layout')

@section('pagename')
- {{__("Enrolled Courses")}}
@endsection

@section('content')
{{-- hero area start --}}
<div
  class="breadcrumb-area services service-bg"
  style="background-image: url('{{asset('assets/front/img/' . $bs->breadcrumb)}}'); background-size:cover;"
>
  <div class="container">
    <div class="breadcrumb-txt">
      <div class="row">
        <div class="col-xl-7 col-lg-8 col-sm-10">
          <h1>{{__('My Courses')}}</h1>
          <ul class="breadcumb">
            <li><a href="{{route('user-dashboard')}}">{{__('Dashboard')}}</a></li>
            <li>{{__('My Courses')}}</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <div class="breadcrumb-area-overlay"></div>
</div>
{{-- hero area end --}}

{{-- my courses area start --}}
<section class="user-dashbord">
  <div class="container">
    <div class="row">
      @include('user.inc.site_bar')
      <div class="col-lg-9">
        <div class="row">
          <div class="col-lg-12">
            <div class="user-profile-details">
              <div class="account-info">
                <div class="title">
                  <h4>{{__('All Courses')}}</h4>
                </div>
                <div class="main-info">
                  <div class="main-table">
                    <div class="table-responsiv">
                      <table
                        id="ordersTable"
                        class="dataTables_wrapper dt-responsive table-striped dt-bootstrap4"
                        style="width:100%"
                      >
                        <thead>
                          <tr>
                            <th>{{__('Course')}}</th>
                            <th>{{__('Duration')}}</th>
                            <th>{{__('Price')}}</th>
                            <th>{{__('Action')}}</th>
                          </tr>
                        </thead>
                        <tbody>
                          @if (count($course_orders) == 0)
                            <tr class="text-center">
                              <td colspan="4">
                                {{__('No Course Order Found!')}}
                              </td>
                            </tr>
                          @else
                            @foreach ($course_orders as $course_order)
                              <tr>
                                <td>
                                    @if (!empty($course_order->course))
                                        {{ strlen($course_order->course->title) > 30 ? mb_substr($course_order->course->title,0,30,'utf-8') . '...' : $course_order->course->title }}
                                    @endif
                                </td>
                                <td>
                                    @if (!empty($course_order->course))
                                        {{$course_order->course->duration}}
                                    @endif
                                </td>
                                <td>
                                  @if ($course_order->current_price == null)
                                    {{__('Free')}}
                                  @else
                                    {{$bex->base_currency_symbol_position == 'left' ? $bex->base_currency_symbol : ''}} {{$course_order->current_price}} {{$bex->base_currency_symbol_position == 'right' ? $bex->base_currency_symbol : ''}}
                                  @endif
                                </td>
                                <td>
                                  <a
                                    href="{{route('user.course.lessons', $course_order->id)}}"
                                    class="btn base-bg text-white"
                                  >{{__('Videos')}}</a>
                                </td>
                              </tr>
                            @endforeach
                          @endif
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
{{-- my courses area end --}}
@endsection

@section('scripts')
<script>
  $(document).ready(function() {
    $('#ordersTable').DataTable({
      responsive: true,
      ordering: false
    });
  });
</script>
@endsection
