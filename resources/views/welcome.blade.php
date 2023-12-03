<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="{{ url('css/bootstrap.min_flatly.css') }}" rel="stylesheet">
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <title>Welcome to FEUPBook</title>
</head>
<body class="d-flex flex-column bg-light" style="height: 100vh;">

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

<div class="container py-5 my-auto" style="padding-top: 5vh; padding-bottom: 5vh;">
    <div class="row align-items-center" style="flex-grow: 1;">
        <!-- Left side with a large logo or image -->
        <div class="col-lg-6 mb-4 mb-lg-0">
            <img src="{{ asset('path-to-your-logo.png') }}" alt="FEUPBook Logo" class="img-fluid mx-auto d-block">
        </div>

        <!-- Right side with login/signup information -->
        <div class="col-lg-6">
            <div class="w-100" style="max-width: 540px;"> 
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
            </div>
        </div>
    </div>
</div>
<footer class="footer bg-primary text-center py-2" style="position: absolute; bottom: 0; width: 100%;">
<div class="container">
        <ul class="nav justify-content-center mb-1">
            <li class="nav-item">
                <a class="nav-link text-white" href="{{ url('/about') }}">About</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="{{ url('/help') }}">Help Center</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="{{ url('/faq') }}">FAQ</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="{{ url('/contacts') }}">Contacts</a>
            </li>
        </ul>
        <div class="text-light">&copy; {{ date('Y') }} FEUPBook Corp.</div>
</div>
</footer>

</body>
</html>
