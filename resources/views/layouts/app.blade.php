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
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.0/font/bootstrap-icons.css">
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
        {{-- use mix to load app.js --}}

        <script src="{{asset('js/app.js')}}"></script>
        @vite(['resources/js/app.js'])
        <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    </head>
    <body>
        <div class="d-flex vh-100 overflow-hidden">
            @if ($errors->any())
                <div class="d-flex justify-content-center mt-2">
                    <div class="alert alert-danger alert-dismissible fade show" role="alert" style="max-width: 400px;">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            @endif

            @yield('sidebar')
            
            <div class="container-fluid d-flex flex-column w-100 p-0 overflow-auto">
                <div class="d-flex justify-content-start w-100">
                    <nav class="navbar navbar-expand-md navbar-light bg-light d-md-none w-100">
                        <div class="d-flex justify-content-between d-md-none d-block">
                            <button class="btn btn-lg p-1 ms-2 open-btn"><i class="bi bi-list"></i></button>
                        </div>
                    </nav>
                </div>
            
                @yield('content')
            </div>
        </div>
    </body>
</html>