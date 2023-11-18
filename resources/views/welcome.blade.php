<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="{{ url('css/bootstrap.min_flatly.css') }}" rel="stylesheet">
    <title>Welcome to FEUPBook</title>
</head>
<body class="d-flex flex-column min-vh-100">

<div class="container-fluid">
    <div class="row">
        <!-- Left side with a large logo or image -->
        <div class="col-md-6 left-side">
            <!-- The logo can be a background image in the CSS if preferred -->
            <img src="{{ asset('path-to-your-logo.png') }}" alt="FEUPBook Logo" class="img-fluid logo">
        </div>

        <!-- Right side with login/signup information -->
        <div class="col-md-6 right-side">
            @if (auth()->check())
                {{-- User is logged in --}}
                <h1>Welcome, {{ auth()->user()->name }}!</h1>
                <p>You are already logged in.</p>
                <a href="{{ route('logout') }}" class="btn btn-danger">Logout</a>
            @else
                {{-- User is not logged in --}}
                <h1>Happening now</h1>
                <h2>Join FEUPBook today.</h2>
                <button class="btn btn-primary google-btn">Sign up with Google</button>
                <div class="mt-3 mb-3">or</div>
                <button class="btn btn-success create-account-btn">Create account</button>
                <p class="mt-3 agreement">By signing up, you agree to the Terms of Service and Privacy Policy, including Cookie Use.</p>
                <div class="login-link">
                    Already have an account? <a href="{{ route('login') }}" class="sign-in-link">Sign in</a>
                </div>
            @endif
        </div>
    </div>
</div>

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
