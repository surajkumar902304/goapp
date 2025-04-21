<!doctype html>
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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div id="app">
        <div class="topbar border-bottom small text-bg-dark py-1">
            <div class="container">
                <div class="row">
                    <div class="col-md-6 fw-bold">
                        <a href="mailto:info@truewebpro.co.uk" class="link-light text-decoration-none" title="Email">
                            <i class="iconify" data-icon="mdi:email-fast-outline"></i>
                            info@truewebpro.co.uk
                        </a>
                        <span>|</span>
                        <a href="tel:+447492 835206" class="link-light text-decoration-none" title="Call">
                            <i class="iconify" data-icon="line-md:phone-call-loop"></i>
                            +44 7492 835 206
                        </a>
                    </div>
                    <div class="col-md-6 text-md-end fw-bold">
                        @guest
                            @if (Route::has('login'))
                                <a href="{{route('login')}}" class="link-light text-decoration-none">Login</a>
                            @endif
                        <span>|</span>
                                @if (Route::has('register'))
                                    <a href="{{route('register')}}" class="link-light text-decoration-none">Register</a>
                                @endif
                        <span>|</span>
                        @else
                            <a class="link-light text-decoration-none" href="{{ route('logout') }}"
                               onclick="event.preventDefault();
                               document.getElementById('logout-form').submit();">
                                {{ __('Logout') }}
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        @endguest
                        <a href="#about" class="link-light text-decoration-none">About</a>
                    </div>
                </div>
            </div>

        </div>
        <nav class="navbar navbar-expand-md navbar-light bg-white border-bottom shadow-sm sticky-top">
            <div class="container">
                <a class="navbar-brand fs-4 fw-bold d-flex" href="{{ url('/') }}">
                    <img src="{{asset('images/truewebapp_logo1.png')}}" alt="TrueWebApp" width="120" height="60">
{{--                    <i class="iconify fs-2" data-icon="carbon:application-mobile"></i>--}}
{{--                    TrueWebApp--}}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mx-auto nav-justified w-75 fw-bold">
                        <li class="nav-item">
                            <a class="nav-link" href="#products">Products</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#features">Features</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#industries">Industries</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#pricing">Pricing</a>
                        </li>
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                       <a href="#contact" class="btn btn-primary rounded-0">Contact Us</a>
                    </ul>
                </div>
            </div>
        </nav>
        <main>
            @yield('content')
        </main>
        <section class="copyright text-bg-dark py-1">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-7 text-center">
                        © Copyright 2025 by TrueWebApp. Developed by <a href="https://www.truewebpro.co.uk/">Truewebpro</a>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" integrity="sha512-1cK78a1o+ht2JcaW6g8OXYwqpev9+6GqOkz9xmBN9iUUhIndKtxwILGWYOSibOKjLsEdjyjZvYDq/cZwNeak0w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js" integrity="sha512-A7AYk1fGKX6S2SsHywmPkrnzTZHrgiVT7GcQkLGDe2ev0aWb8zejytzS8wjo7PGEXKqJOrjQ4oORtnimIRZBtw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        AOS.init();
    </script>
    <script src="https://code.iconify.design/2/2.2.1/iconify.min.js"></script>
    <style>
        body, html{
            max-width: 100vw;
            overflow-x: hidden;
            overflow-y: scroll;
        }
    </style>
</body>
</html>
