<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="{{ url('css/bootstrap.min_flatly.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <title>Welcome to FEUPBook</title>
</head>
<body class="d-flex flex-column vh-100">

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

    <div class="d-flex justify-content-center align-items-center vh-100 w-100">
        <div class="w-75"> 
            <h1 class="display-4 text-center mb-4">Happening now</h1>
            <h2 class="display-6 mb-4 text-center">Join FEUPBook today.</h2>
            <div class="d-flex justify-content-center">
                <div class="d-flex flex-column align-items-center my-2 w-75"> 
                    <a href="/auth/google/redirect" class="btn w-100 text-white mt-2" style="max-width: 400px; background-color: #dd4b39;">
                        <i class="fa-brands fa-google"></i> 
                        <span class="ms-2 fs-5">Sign in with Google</span>
                    </a>
                    <span class="px-2">or</span>
                    <a href="{{ route('register') }}" class="btn btn-success w-100" style="max-width: 400px;"><span class="fs-5">Create account</span></a>
                </div>
            </div>
            <p class="text-muted mt-4 text-center"><small>By signing up, you agree to the Terms of Service and Privacy Policy, including Cookie Use.</small></p>
            <div class="mt-2 text-center">
                Already have an account? <a href="{{ route('login') }}" class="fw-bold">Sign in</a>
            </div>
        </div>
    </div>

    <footer class="footer bg-primary text-center py-2 mt-auto">
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
