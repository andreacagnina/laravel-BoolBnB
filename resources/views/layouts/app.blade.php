<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>BoolBnB</title>
    @vite('resources/js/app.js')
</head>

<body>
    <div id="app">
        @include('partials.header')
        <main>
            @yield('content')
        </main>
    </div>
</body>

</html>
