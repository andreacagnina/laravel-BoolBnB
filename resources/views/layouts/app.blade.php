<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>BoolBnB</title>

    <!-- Risorse CSS -->
    <link rel="stylesheet" href="https://api.tomtom.com/maps-sdk-for-web/cdn/6.x/6.13.0/maps/maps.css">
    @vite('resources/js/app.js')

    <style>
        /* Nascondere il contenuto inizialmente */
        #app {
            display: none;
        }
    </style>
</head>

<body>
    <div id="app" class="d-flex wrapper">
        {{-- Include header --}}
        @include('partials.header')
    
        {{-- Contenitore principale --}}
        <div class="container-fluid d-flex px-0">
            {{-- Sidebar visibile solo per utenti autenticati --}}
            @auth
                @include('partials.sidebar')
            @endauth
    
            {{-- Contenuto principale --}}
            <main class="px-md-2">
                @yield('content')
            </main>
        </div>
    </div>    

    <!-- Script esterni -->
    <script src="https://api.tomtom.com/maps-sdk-for-web/cdn/6.x/6.13.0/maps/maps-web.min.js"></script>
    <script src="https://js.braintreegateway.com/web/dropin/1.8.1/js/dropin.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Mostra il contenuto solo quando la pagina è completamente caricata
        document.addEventListener("DOMContentLoaded", function () {
            const app = document.getElementById("app");

            // Nascondere il contenuto fino al caricamento completo
            window.addEventListener("load", function () {
                app.style.display = "block"; // Mostra il contenuto
            });
        });
    </script>

    <!-- Scripts specifici per le viste -->
    @yield('scripts')
</body>

</html>
