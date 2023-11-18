<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Styles -->
        {{-- <link href="{{ url('css/milligram.min.css') }}" rel="stylesheet"> --}}
        {{-- <link href="{{ url('css/bootstrap.css') }}" rel="stylesheet"> --}}
        <link href="{{ url('css/bootstrap.min_flatly.css') }}" rel="stylesheet">
        {{-- <link href="{{ url('css/app.css') }}" rel="stylesheet"> --}}
        <script type="text/javascript">
            // Fix for Firefox autofocus CSS bug
            // See: http://stackoverflow.com/questions/18943276/html-5-autofocus-messes-up-css-loading/18945951#18945951
        </script>
        <script type="text/javascript" src={{ url('js/app.js') }} defer>
        </script>
    </head>
    <body>
        <main>
            <header>
                <h1><a href="{{ url('/cards') }}">Thingy!</a></h1>
                @if (Auth::check())
                    <a class="button" href="{{ url('/logout') }}"> Logout </a> <span>{{ Auth::user()->name }}</span>
                @endif
            </header>
            <section id="content">
                @yield('content')
            </section>
        </main>
        <footer class="bg-light text-center text-lg-start mt-auto">
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div class="container-fluid">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item"><a class="nav-link" href="{{ url('/about') }}">About</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ url('/help') }}">Help Center</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ url('/terms') }}">Terms of Service</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ url('/privacy') }}">Privacy Policy</a></li>
                    </ul>
                    <div class="copyright">
                        &copy; {{ date('Y') }} FEUPBook Corp.
                    </div>
                </div>
            </nav>
        </footer>
    </body>
</html>