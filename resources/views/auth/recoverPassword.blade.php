@extends('layouts.auth')

@section('content')
    <div class="container d-flex flex-column justify-content-center align-items-center m-auto">
        <h1 class="text-center">Change password</h1>
        <p class="text-center">Use the form below to change your password.</p>
        <form action="{{ route('auth.resetPassword', ['token' => $token])}}" method="POST" style="max-width: 500px;">
            @csrf
            @method('POST')
            <div class="mb-3">
                <label for="new_password" class="form-label">New Password</label>
                <input type="password" class="form-control w-50" id="new_password" name="new_password" aria-describedby="newPasswordHelp">
                <div id="newPasswordHelp" class="form-text">Enter your new password.</div>
            </div>
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <input type="password" class="form-control w-50" id="confirm_password" name="new_password_confirmation" aria-describedby="confirmPasswordHelp">
                <div id="confirmPasswordHelp" class="form-text">Confirm your new password.</div>
            </div>
            <input type="hidden" name="token" value="{{ $token }}">
            <button type="submit" class="btn btn-primary">Update Password</button>
        </form>
    </div>
@endsection

@section('footer')
    @include('partials.footer')
@endsection