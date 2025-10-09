<div class="col-lg-3">
  <div class="user-sidebar">
    <ul class="links">
      <li>
        <a
          class="@if(request()->path() == 'user/dashboard') active @endif"
          href="{{route('user-dashboard')}}"
        >{{__('Dashboard')}}</a>
      </li>

            @if ($bex->recurring_billing == 1)
            <li><a class="@if(request()->path() == 'user/packages') active @endif" href="{{route('user-packages')}}">{{__('Packages')}}</a></li>
            @endif

            @if ($bex->is_shop == 1 && $bex->catalog_mode == 0)
                <li><a class="
                    @if(request()->path() == 'user/orders') active
                    @elseif(request()->is('user/order/*')) active
                    @endif"
                    href="{{route('user-orders')}}">{{__('Product Orders')}} </a></li>
            @endif

            @if ($bex->recurring_billing == 0)
                <li><a class="
                    @if(request()->path() == 'user/package/orders') active
                    @elseif(request()->is('user/package/order/*')) active
                    @endif"
                    href="{{route('user-package-orders')}}">{{__('Package Orders')}} </a></li>
            @endif

            @if ($bex->is_course == 1)
                <li>
                    <a
                    class="@if(request()->path() == 'user/course_orders') active @endif"
                    href="{{route('user.course_orders')}}"
                    >{{__('Courses')}}</a>
                </li>
            @endif

            @if ($bex->is_event == 1)
                <li>
                    <a
                    class="@if(request()->path() == 'user/events') active
                    @elseif(request()->is('user/event/*')) active
                    @endif"
                    href="{{route('user-events')}}"
                    >{{__('Event Bookings')}}</a>
                </li>
            @endif

            @if ($bex->is_donation == 1)
                <li>
                    <a
                    class="@if(request()->path() == 'user/donations') active @endif"
                    href="{{route('user-donations')}}"
                    >{{__('Donations')}}</a>
                </li>
            @endif

      @if ($bex->is_ticket == 1)
        <li>
          <a
            class="@if(request()->path() == 'user/tickets') active
            @elseif(request()->is('user/ticket/*')) active
            @endif"
            href="{{route('user-tickets')}}"
          >{{__('Support Tickets')}}</a>
        </li>
      @endif

      <li>
        <a
          class=" @if(request()->path() == 'user/profile') active @endif"
          href="{{route('user-profile')}}"
        >{{__('Edit Profile')}}</a>
      </li>

      @if ($bex->is_shop == 1 && $bex->catalog_mode == 0)
        <li>
          <a
            class=" @if(request()->path() == 'user/shipping/details') active @endif"
            href="{{route('shpping-details')}}"
          >{{__('Shipping Details')}}</a>
        </li>
        <li>
          <a
            class=" @if(request()->path() == 'user/billing/details') active @endif"
            href="{{route('billing-details')}}"
          >{{__('Billing Details')}}</a>
        </li>
      @endif
        <li>
            <a
            class=" @if(request()->path() == 'user/reset') active @endif"
            href="{{route('user-reset')}}"
            >{{__('Change Password')}}</a>
        </li>

      <li><a href="{{route('user-logout')}}">{{__('Logout')}}</a></li>
    </ul>
  </div>
</div>
