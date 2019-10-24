<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div class="wrapper" id="app">
        <nav id="nav" style="">
            <div class="navbar">
                <a class="navbar-brand" href="#">Cardmonitor</a>
            </div>
            <ul class="col">
                <a href="{{ url('/home') }}"><li>Start</li></a>
                <a href="{{ url('/article') }}"><li>Artikel</li></a>
                <a href="{{ url('/order') }}"><li>Bestellungen</li></a>
                <a href="{{ url('/item') }}"><li>Kosten</li></a>
            </ul>
            <div class="px-3 text-white text-center"><p>Alle Daten sind von <a href="https://www.cardmarket.eu">Cardmarket</a></p></div>
            <div class="bg-secondary text-white p-2 d-flex justify-content-around">
                <div><i class="fas fa-bars pointer"></i></div>
                <div><i class="fas fa-bars pointer"></i></div>
                <div><i class="fas fa-bars pointer"></i></div>
                <div><i class="fas fa-bars pointer"></i></div>
            </div>
        </nav>

        <div id="content-container">

            <nav class="navbar navbar-expand navbar-light bg-light sticky-top shadow-sm">
                <div class="collapse navbar-collapse" id="navbarTogglerDemo01">
                    <span class="navbar-text" id="menu-toggle">
                        <i class="fas fa-bars pointer"></i>
                    </span>
                    <form class="form-inline col my-2 my-lg-0">
                        <input class="form-control mr-sm-2 col" type="search" placeholder="Search" aria-label="Search">
                        <!-- <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button> -->
                    </form>
                    <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
                        <li class="nav-item active">
                            <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Link</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Bild <span class="d-xs-none d-sm-none d-md-inline d-lg-inline d-xl-inline">Username</span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
                                <a class="dropdown-item" href="#">Action</a>
                                <a class="dropdown-item" href="#">Another action</a>
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                   onclick="event.preventDefault();
                                                 document.getElementById('logout-form').submit();">
                                    {{ __('Logout') }}
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                            </div>
                      </li>
                    </ul>
                </div>
            </nav>

            <div id="content" class="container-fluid mt-3" style="height: 100vh;">
                @yield('content')
            </div>

        </div>

        <flash-message :initial-message="{{ session()->has('status') ? json_encode(session('status')) : 'null' }}"></flash-message>
    </div>
</body>
</html>
