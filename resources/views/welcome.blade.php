<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="{{ url('css/bootstrap.min_flatly.css') }}" rel="stylesheet">
    <title>Welcome to FEUPBook</title>
</head>
<body class="d-flex flex-column bg-light" style="height: 100vh;">

<div class="container py-5 my-auto" style="padding-top: 5vh; padding-bottom: 5vh;">
    <div class="row align-items-center" style="flex-grow: 1;">
        <!-- Left side with a large logo or image -->
        <div class="col-lg-6 mb-4 mb-lg-0">
            <img src="{{ asset('path-to-your-logo.png') }}" alt="FEUPBook Logo" class="img-fluid mx-auto d-block">
        </div>

        <!-- Right side with login/signup information -->
        <div class="col-lg-6">
            <div class="w-100" style="max-width: 540px;"> 
                @if (auth()->check())
                    <h1 class="display-4">Welcome, {{ auth()->user()->name }}!</h1>
                    <p class="lead">You are logged in.</p>
                    <a href="{{ route('logout') }}" class="btn btn-danger btn-lg w-100">Logout</a>
                @else
                <h1 class="display-4">Happening now</h1>
                <h2 class="display-6 mb-4">Join FEUPBook today.</h2>
                <a class="btn btn-primary btn-lg mb-2 w-100">Sign up with Google</a> 
                <div class="d-flex justify-content-center my-2"> 
                    <span class="px-2">or</span> 
                </div>
                <a href="{{ route('register') }}" class="btn btn-success btn-lg w-100">Create account</a>
                <p class="text-muted mt-4"><small>By signing up, you agree to the Terms of Service and Privacy Policy, including Cookie Use.</small></p>
                <div class="mt-2">
                    Already have an account? <a href="{{ route('login') }}" class="fw-bold">Sign in</a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<footer class="footer bg-primary text-center py-2">
    <div class="container">
        <ul class="nav justify-content-center mb-2">
            <li class="nav-item">
                <a class="nav-link text-white" href="{{ url('/about') }}">About</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="{{ url('/help') }}">Help Center</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="{{ url('/terms') }}">Terms of Service</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="{{ url('/privacy') }}">Privacy Policy</a>
            </li>
        </ul>
        <div class="text-light">&copy; {{ date('Y') }} FEUPBook Corp.</div>
    </div>
</footer>



</body>
</html>
