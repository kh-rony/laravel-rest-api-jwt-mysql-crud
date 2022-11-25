<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - Laravel REST API CRUD App using JWT MySQL</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Bootstrap Library -->
    <link href="{{url('/')}}/public/frontend/bootstrap/bootstrap.min.css" rel="stylesheet">
    <script src="{{url('/')}}/public/frontend/bootstrap/bootstrap.bundle.min.js"></script>
    <script src="{{url('/')}}/public/frontend/bootstrap/popper.min.js"></script>
    <script src="{{url('/')}}/public/frontend/bootstrap/bootstrap.min.js"></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>

<body>
    <div id="app">
        @include('common.navbar')

        <main role="main">

            <div>
                @yield('content')
            </div>

        </main>

        @include('common.footer')
    </div>
</body>

</html>