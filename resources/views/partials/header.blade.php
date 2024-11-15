<header class="position-absolute w-100 z-1 top-0">
    <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ url('http://localhost:5174/') }}">
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
 

                <!-- Right Side Of Navbar -->
                <ul class="navbar-nav ms-auto">
                    <!-- Authentication Links -->
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                        </li>
                        @if (Route::has('register'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                            </li>
                        @endif
                    @else
                            <ul class="navbar-nav me-auto">
                                <li class="nav-item">
                                    <p class="m-0 me-2 d-inline"> {{ Auth::user()->name ?? Auth::user()->email }}</p><i class="fa-solid fa-user"></i>
                                </li>
                            </ul>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>
</header>
