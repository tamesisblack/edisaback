<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'BINGO') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <style>
        .background_bingo{
            background-color: #0E0D22;
            /* position: fixed;
            z-index: 2;
            width: 100%; */
        }
        .btn_navegacion{
            background: rgb(223,160,10);
            margin: 5px;
            font-weight: bold;
            border-radius: 2px;
            width: 110px;
            height: 40px;
            font-size: 15px;
            color: black;
            padding: 5px;
            text-align: center;
            text-transform: uppercase;
            -webkit-box-shadow: -2px 2px 5px 0px rgba(94,94,94,1);
            -moz-box-shadow: -2px 2px 5px 0px rgba(94,94,94,1);
            box-shadow: -2px 2px 5px 0px rgba(94,94,94,1);
            text-decoration: none;
        }
        .barra{
            padding: 0% !important;
        }
        #navbarDropdown{
            color: white;
        }
    </style>
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light shadow-sm background_bingo">
            <div class="container barra">
                <a class="navbar-brand" href="{{ url('/') }}">
                    <img src="img/logo1.png" style="width: 120px; height: 80px;">
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="true" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon" style="background-color: rgba(255, 255, 255, 0.616); border-radius: 5px;"></span>
                </button>

                <div class="collapse navbar-collapse show" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <button class="btn btn_navegacion"><a style="text-decoration: none; color: black;" href="{{ url('/eventos') }}">Eventos</a></button>
                        
                        <button class="btn btn_navegacion"><a style="text-decoration: none; color: black;" href="{{ url('/clientes') }}">Clientes</a></button>

                        {{-- <div class="dropdown">
                            <button class="btn btn_navegacion dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                              Idioma
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                              <a class="dropdown-item" href="#">Español</a>
                              <a class="dropdown-item" href="#">Inglés</a>
                            </div>
                          </div> --}}

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif
                            
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Cerrar sesión') }}
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
        
        <main class="">
            @yield('content')
        </main>
    </div>

    <!-- <script src="https://www.gstatic.com/dialogflow-console/fast/messenger/bootstrap.js?v=1"></script>
    <df-messenger
    chat-icon="https:&#x2F;&#x2F;storage.googleapis.com&#x2F;cloudprod-apiai&#x2F;7460b211-dc59-446f-93a6-a97f133849b1_x.png"
    intent="WELCOME"
    chat-title="Bingofun"
    agent-id="c1372c79-b589-45d6-9465-aec0ef740ad3"
    language-code="es"
    ></df-messenger> -->

    <!-- This site is converting visitors into subscribers and customers with https://respond.io -->
    <script id="respondio__widget" src="https://cdn.respond.io/webchat/widget/widget.js?cId=f875497cad17c8079273f7ffa8d5a48c0a07ac8558b790f40f19a89d0709635d"></script>
    <!-- https://respond.io -->

    
</body>
</html>
