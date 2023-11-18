@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Left side with a large logo or image -->
        <div class="col-md-6 left-side">
            <!-- The logo can be a background image in the CSS if preferred -->
            <img src="{{ asset('path-to-your-logo.png') }}" alt="FEUPBook Logo" class="img-fluid logo">
        </div>

        <!-- Right side with login/signup information -->
        <div class="col-md-6 right-side">
            <form method="POST" action="{{ route('login') }}" class="mt-5">
                {{ csrf_field() }}

                <div class="mb-3">
                    <label for="email" class="form-label">E-mail</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control" required autofocus>
                    @if ($errors->has('email'))
                        <span class="error">
                            {{ $errors->first('email') }}
                        </span>
                    @endif
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input id="password" type="password" name="password" class="form-control" required>
                    @if ($errors->has('password'))
                        <span class="error">
                            {{ $errors->first('password') }}
                        </span>
                    @endif
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label" for="remember">Remember Me</label>
                </div>

                <button type="submit" class="btn btn-primary">Login</button>
                <a class="btn btn-outline-secondary" href="{{ route('register') }}">Register</a>

                @if (session('success'))
                    <p class="success mt-3">
                        {{ session('success') }}
                    </p>
                @endif
            </form>

            <div class="login-link mt-3">
                Don't have an account? <a href="{{ route('register') }}" class="sign-in-link">Sign up</a>
            </div>
        </div>
    </div>
</div>

@endsection
