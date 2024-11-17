<header class="position-absolute w-100 z-1 top-0">
    <nav class="navbar navbar-expand-md navbar-light">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="{{ url('http://localhost:5174/') }}">
                <div>
                    <img class="logo" src="{{ asset('img/logo.png') }}" alt="Logo">
                </div>
                {{-- config('app.name', 'Laravel') --}}
            </a>
            <ul class="navbar-nav ms-auto d-flex flex-row">
                <!-- Authentication Links -->
                @guest
                    <li class="nav-item">
                        <a class="nav-link fw-bold" href="{{ route('login') }}">{{ __('Login') }}</a>
                    </li>
                    @if (Route::has('register'))
                        <li class="nav-item">
                            <a class="nav-link fw-bold ms-2" href="{{ route('register') }}">{{ __('Register') }}</a>
                        </li>
                    @endif
                @endguest
            </ul>            
        </div>
    </nav>
</header>
