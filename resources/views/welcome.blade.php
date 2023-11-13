<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <title>Welcome to FEUPBook</title>
</head>
<body>

<div class="wrapper">
    <!-- Left side with a large logo or image -->
    <div class="left-side">
        <!-- The logo can be a background image in the CSS if preferred -->
        <img src="{{ asset('path-to-your-logo.png') }}" alt="FEUPBook Logo" class="logo">
    </div>

    <!-- Right side with login/signup information -->
    <div class="right-side">
        <h1>Happening now</h1>
        <h2>Join FEUPBook today.</h2>
        <button class="google-btn">Sign up with Google</button>
        <div class="divider">or</div>
        <button class="create-account-btn">Create account</button>
        <p class="agreement">By signing up, you agree to the Terms of Service and Privacy Policy, including Cookie Use.</p>
        <div class="login-link">
            Already have an account? <a href="{{ route('login') }}" class="sign-in-link">Sign in</a>
        </div>
    </div>
</div>

<footer>
    <nav>
        <a href="{{ url('/about') }}">About</a>
        <a href="{{ url('/help') }}">Help Center</a>
        <a href="{{ url('/terms') }}">Terms of Service</a>
        <a href="{{ url('/privacy') }}">Privacy Policy</a>
    </nav>
    <div class="copyright">
        &copy; {{ date('Y') }} FEUPBook Corp.
    </div>
</footer>

</body>
</html>
