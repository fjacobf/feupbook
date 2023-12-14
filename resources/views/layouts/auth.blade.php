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
        <link href="{{ url('css/bootstrap.min_flatly.css') }}" rel="stylesheet">
        <script type="text/javascript">
            // Fix for Firefox autofocus CSS bug
            // See: http://stackoverflow.com/questions/18943276/html-5-autofocus-messes-up-css-loading/18945951#18945951
        </script>
        <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    </head>
    <body class="d-flex flex-column vh-100">
        <header>
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div class="container-fluid">
                    <a class="navbar-brand fs-3" href="{{url('/home')}}">Feupbook</a>
                </div>
            </nav>
        </header>

        @yield('content')
        
        <footer class="footer bg-primary text-center py-2 mt-auto">
            @yield('footer')
        </footer>
    </body>
</html>