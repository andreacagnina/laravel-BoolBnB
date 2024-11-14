<header class="position-absolute w-100 z-1 top-0">
    <nav class="navbar navbar-expand-md navbar-light">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
                <div>
                    <img class="logo" src="{{ asset('img/logo.png') }}" alt="Logo">
                </div>
                {{-- config('app.name', 'Laravel') --}}
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="{{ __('Toggle navigation') }}">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <!-- Left Side Of Navbar -->
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link fw-bold" href="{{ url('/') }}">{{ __('Home') }}</a>
                    </li>
                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="navbar-nav ml-auto">
                    <!-- Authentication Links -->
                    @guest
                        <li class="nav-item">
                            <a class="nav-link fw-bold" href="{{ route('login') }}">{{ __('Login') }}</a>
                        </li>
                        @if (Route::has('register'))
                            <li class="nav-item">
                                <a class="nav-link fw-bold" href="{{ route('register') }}">{{ __('Register') }}</a>
                            </li>
                        @endif
                    @else
                        <li class="nav-item dropdown me-2">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle fw-bold" href="#" role="button"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ Auth::user()->name ?? Auth::user()->email }}
                            </a>

                            <div class="dropdown-menu dropdown-menu-right" style="color: #192033" aria-labelledby="navbarDropdown">

                                <a class="dropdown-item" href="{{ route('admin.properties.index') }}"><i
                                        class="fa-solid fa-house me-2"></i>{{ __('My Properties') }}</a>
                                <a class="dropdown-item" href="{{ route('admin.messages.index') }}"><i
                                        class="fa-solid fa-envelope-open-text position-relative me-2">
                                        @if ($unreadCount > 0)
                                            <span
                                                class="badge bg-danger position-absolute top-0 start-100 translate-middle rounded-pill">{{ $unreadCount }}</span>
                                        @endif
                                    </i>{{ __('Inbox') }}
                                </a>
                                <a class="dropdown-item" href="{{ route('admin.sponsors.index') }}"><i
                                        class="fa-solid fa-arrow-up-wide-short me-2"></i>{{ __('Advertisements') }}</a>
                                <a class="dropdown-item"
                                    href="{{ route('admin.views.index') }}"><i class="fa-solid fa-chart-pie me-2"></i>{{ __('Statistics') }}</a>

                                <a class="dropdown-item" href="{{ route('logout') }}"
                                    onclick="event.preventDefault();
                                                document.getElementById('logout-form').submit();">
                                    <i class="fa-solid fa-right-from-bracket me-2"></i> {{ __('Logout') }}
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>
</header>
