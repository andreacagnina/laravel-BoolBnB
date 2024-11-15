<section id="aside" class="d-flex">
    <div>
        <div class="item">
            <a class="" href="{{ route('admin.properties.index') }}"><i
                class="fa-solid fa-house me-3"></i><span class="text">{{ __('My Properties') }}</span></a>
            </div>

            <div class="item">
    <a class="" href="{{ route('admin.messages.index') }}">
        <i
        class="fa-solid fa-envelope-open-text position-relative me-3">
        @if ($unreadCount > 0)
        <span
        class="badge bg-danger position-absolute top-0 start-100 translate-middle rounded-pill"><span class="text">{{ $unreadCount }}</span>
        @endif
    </i><span class="text">{{ __('Inbox') }}</span>
</a>
</div>
<div class="item">
    <a class="" href="{{ route('admin.sponsors.index') }}"><i
        class="fa-solid fa-arrow-up-wide-short me-3"></i><span class="text">{{ __('Advertisements') }}</span></a>
    </div>
    <div class="item">
        <a class=""
        href="{{ route('admin.views.index') }}"><i class="fa-solid fa-chart-pie me-3"></i><span class="text">{{ __('Statistics') }}</span></a>
    </div>
    </div>
    <div class="item">
        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fa-solid fa-right-from-bracket me-3"></i><span class="text">Logout</span></a>
</div>
</section>

