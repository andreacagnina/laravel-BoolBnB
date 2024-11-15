<header class="position-absolute w-100 z-1 top-0">
    <nav class="navbar navbar-expand-md navbar-light">
        <div class="container-fluid-sm container-md">
            <a class="navbar-brand d-flex align-items-center" href="{{ url('http://localhost:5174/') }}">
                <div>
                    <img class="logo" src="{{ asset('img/logo.png') }}" alt="Logo">
                </div>
                {{-- config('app.name', 'Laravel') --}}
            </a>

            
 

                <!-- Right Side Of Navbar -->
                <ul class="navbar-nav ms-auto">
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
                            <ul class="navbar-nav me-auto">
                                <li class="nav-item d-flex align-items-center">
                                    <p class="m-0 me-2"> {{ Auth::user()->name ?? Auth::user()->email }}</p><i class="fa-solid fa-user"></i>
                                </li>
                            </ul>
                    @endguest
                </ul>
        </div>
    </nav>
</header>
