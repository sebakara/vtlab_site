@extends("front.$version.layout")

@section('pagename')
 - {{__('Event Calendar')}}
@endsection

@section('meta-keywords', "$be->calendar_meta_keywords")
@section('meta-description', "$be->calendar_meta_description")

@section('styles')
<link href='{{asset('assets/front/css/calendar.css')}}' rel='stylesheet' />
@endsection

@section('breadcrumb-title', convertUtf8($be->event_calendar_title))
@section('breadcrumb-subtitle', convertUtf8($be->event_calendar_subtitle))
@section('breadcrumb-link', convertUtf8($be->event_calendar_title))


@section('content')

  <!--   about company section start   -->
  <div class="about-company-section pt-105 pb-115">
     <div class="container">
        <div class="row">
           <div class="col-lg-12">
              <div id='calendar'></div>
           </div>
        </div>
     </div>
  </div>
  <!--   about company section end   -->
@endsection

@section('scripts')
<script>

    var events = {!! json_encode($formattedEvents) !!};
    var today = new Date();
    var dd = String(today.getDate()).padStart(2, '0');
    var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
    var yyyy = today.getFullYear();

    today = yyyy + '-' + mm + '-' + dd;

    document.addEventListener('DOMContentLoaded', function() {
      var calendarEl = document.getElementById('calendar');

      var calendar = new FullCalendar.Calendar(calendarEl, {
        plugins: [ 'dayGrid', 'timeGrid', 'list', 'interaction' ],
        header: {
          left: 'prev,next today',
          center: 'title',
          right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek',
        },
        buttonText: {
            today: "{{__('today')}}",
            month: "{{__('month')}}",
            week: "{{__('week')}}",
            day: "{{__('day')}}",
            list: "{{__('list')}}"
        },
        defaultDate: today,
        navLinks: true, // can click day/week names to navigate views
        editable: true,
        eventLimit: true, // allow "more" link when too many events
        events: events,
        locale: '{{$currentLang->code}}',
        dir: "{{$rtl == 1 ? 'rtl' : 'ltr'}}"
      });

      calendar.render();
    });

  </script>
@endsection
