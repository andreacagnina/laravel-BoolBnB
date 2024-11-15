<section id="aside" class="d-flex rounded-end">
    <div class="mt-2">
        <div class="item">
            <a class="" href="{{ route('admin.properties.index') }}"><i
                class="fa-solid fa-house "></i><span class="text ms-3">{{ __('My Properties') }}</span></a>
            </div>

            <div class="item">
    <a class="" href="{{ route('admin.messages.index') }}">
        <i
        class="fa-solid fa-envelope-open-text position-relative ">
        @if ($unreadCount > 0)
        <span
        class="badge bg-danger position-absolute top-0 start-100 translate-middle rounded-pill"><span class="">{{ $unreadCount }}</span>
        @endif
    </i><span class="text ms-3">{{ __('Inbox') }}</span>
</a>
</div>
<div class="item">
    <a class="" href="{{ route('admin.sponsors.index') }}"><i
        class="fa-solid fa-arrow-up-wide-short "></i><span class="text ms-3">{{ __('Advertisements') }}</span></a>
    </div>
    <div class="item">
        <a class=""
        href="{{ route('admin.views.index') }}"><i class="fa-solid fa-chart-pie "></i><span class="text ms-3">{{ __('Statistics') }}</span></a>
    </div>
    </div>
    <div class="item mb-2">
        <a class="" href="{{ route('logout') }}"
                                    onclick="event.preventDefault();
                                                document.getElementById('logout-form').submit();">
                                    <i class="fa-solid fa-right-from-bracket"></i></i><span class="text ms-3">{{ __('Logout') }}</span></a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
</div>

</section>

