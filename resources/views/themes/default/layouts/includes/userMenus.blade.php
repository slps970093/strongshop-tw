<a class="list-group-item @if(request()->route()->named('user.index'))active @endif)" href="{{route('user.index')}}">@lang('User Home')</a>
<a class="list-group-item @if(request()->route()->named('user.my.account'))active @endif)" href="{{route('user.my.account')}}">@lang('My Account')</a>
<a class="list-group-item @if(request()->route()->named('user.my.collects'))active @endif)" href="{{route('user.my.collects')}}">@lang('My Wish List')</a>
<a class="list-group-item @if(request()->route()->named(['user.my.orders', 'user.my.order.detail']))active @endif)" href="{{route('user.my.orders')}}">@lang('My Orders')</a>
<a class="list-group-item @if(request()->route()->named(['user.my.feedback', 'user.my.feedback.create', 'user.my.feedback.reylies']))active @endif)" href="{{route('user.my.feedback')}}">@lang('My Feedback')</a>