@extends('layouts.auth')

@section('content')
    <div class="container d-flex flex-column justify-content-center align-items-center m-auto">
        <h1 class="text-center">Reset Password</h1>
        <p class="text-center">If you forgot your password, you can reset it by filling the form below.</p>
        <p class="text-center">Use the e-mail associated with your account.</p>
        <form action="{{ route('sendRecoveryEmail') }}" method="POST" style="max-width: 500px;">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label">E-mail</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control" required autofocus>
                @if ($errors->has('email'))
                    <span class="error">
                        {{ $errors->first('email') }}
                    </span>
                @endif
            </div>

            <button type="submit" class="btn btn-primary">Send Password Reset Link</button>
        </form>
    </div>
@endsection

@section('footer')
    @include('partials.footer')
@endsection