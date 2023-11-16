@extends('layouts.app')

@section('content')
<div class="wrapper">
    <!-- Left side with a large logo or image -->
    <div class="left-side">
        <!-- The logo can be a background image in the CSS if preferred -->
        <img src="{{ asset('path-to-your-logo.png') }}" alt="FEUPBook Logo" class="logo">
    </div>

    <!-- Right side with login/signup information -->
    <div class="right-side">
        <form method="POST" action="{{ route('login') }}">
            {{ csrf_field() }}

            <label for="email">E-mail</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
            @if ($errors->has('email'))
                <span class="error">
                    {{ $errors->first('email') }}
                </span>
            @endif

            <label for="password" >Password</label>
            <input id="password" type="password" name="password" required>
            @if ($errors->has('password'))
                <span class="error">
                    {{ $errors->first('password') }}
                </span>
            @endif

            <label>
                <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Remember Me
            </label>

            <button type="submit">
                Login
            </button>
            <a class="button button-outline" href="{{ route('register') }}">Register</a>
            @if (session('success'))
                <p class="success">
                    {{ session('success') }}
                </p>
            @endif
        </form>
        <div class="login-link">
            Don't have an account? <a href="{{ route('register') }}" class="sign-in-link">Sign up</a>
        </div>
    </div>
</div>
@endsection