@extends('layouts.auth')

@section('content')

<div class="container d-flex flex-column justify-content-center m-auto w-75" style="max-width: 900px">
    <h1 class="mt-4">Contact Us</h1>
    <p>
        Have questions or concerns? Feel free to get in touch with us. We're here to help!
    </p>

    <h2>Email Support</h2>
    <p>
        For general inquiries and support, you can reach us via email at <strong>support@feupbook.com</strong>.
    </p>

    <h2>Visit Our Office</h2>
    <p>
        If you prefer in-person assistance, you can visit our office at:
        <br>
        123 Main Street, Cityville, Country
    </p>

    @if(!Auth::check())
        <div class="d-flex justify-content-center">
            <a href="{{ route('login') }}" class="btn btn-primary w-25 me-3">Login</a>
            <a href="{{ route('register') }}" class="btn btn-outline-secondary w-25""><strong>Create account</strong></a>
        </div>
    @else
    <a href="{{ route('home') }}" class="btn btn-primary w-25">Home</a>
    @endif
</div>

@endsection
@section('footer')
@include('partials.footer')
@endsection
