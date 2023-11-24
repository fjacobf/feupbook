@extends('layouts.auth')

@section('content')

<div class="container">
    <div class="row">
        <div class="col-md-6">
            <img src="{{ asset('path-to-your-logo.png') }}" alt="FEUPBook Logo" class="img-fluid logo">
        </div>

        <div class="col-md-6">
            <h1 class="mt-4">Help Center</h1>
            <p>
                Welcome to the FEUPBook Help Center. If you have any questions or issues, you're in the right place.
                Check out the information below or contact us for further assistance.
            </p>

            <h2>Frequently Asked Questions</h2>
            <p>
                Explore our FAQ section to find answers to common questions about using FEUPBook.
                If you can't find what you're looking for, feel free to reach out to us.
            </p>

            <h2>Contact Us</h2>
            <p>
                Need personalized assistance? Contact our support team, and we'll be happy to help you.
                Reach out via email at <a href="mailto:support@feupbook.com">support@feupbook.com</a> or use the contact form on our Contacts page.
            </p>

            @if(!Auth::check())
            <a href="{{ route('register') }}" class="btn btn-primary">Create account</a>
            <a href="{{ route('login') }}" class="btn btn-primary">Login</a>
            @else
            <a href="{{ route('home') }}" class="btn btn-primary">Home</a>
            @endif
        </div>
    </div>
</div>

@endsection
@section('footer')
@include('partials.footer')
@endsection
