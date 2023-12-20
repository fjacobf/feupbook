@extends('layouts.auth')

@section('content')

<div class="container d-flex flex-column justify-content-center m-auto w-75" style="max-width: 900px">
    <h1 class="mt-4">Help Center</h1>
    <p>
        Welcome to the FEUPbook Help Center. If you have any questions or issues, you're in the right place.
        Check out the information below or contact us for further assistance.
    </p>

    <h2>Frequently Asked Questions</h2>
    <p>
        Explore our FAQ section to find answers to common questions about using FEUPbook.
        If you can't find what you're looking for, feel free to reach out to us.
    </p>

    <h2>Contact Us</h2>
    <p>
        Need personalized assistance? Contact our support team, and we'll be happy to help you.
        Reach out via email at <a href="mailto:support@feupbook.com">support@feupbook.com</a> or use the contact form on our Contacts page.
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
