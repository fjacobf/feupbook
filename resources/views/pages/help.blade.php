@extends('layouts.app')

@section('content')

<div class="wrapper">
    <!-- Left side with a large logo or image -->
    <div class="left-side">
        <!-- The logo can be a background image in the CSS if preferred -->
        <img src="{{ asset('path-to-your-logo.png') }}" alt="FEUPBook Logo" class="logo">
    </div>

    <!-- Right side with help information -->
    <div class="right-side">
        <h1>Help Center</h1>
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
            Reach out via email at support@feupbook.com or use the contact form on our Contacts page.
        </p>
        <a href="{{ route('register') }}"> <button class="create-account-btn">Create account</button> </a>
    </div>
</div>

<footer>
    <nav>
        <a href="{{ url('/about') }}">About</a>
        <a href="{{ url('/help') }}" class="active">Help Center</a>
        <a href="{{ url('/faq') }}">FAQ</a>
        <a href="{{ url('/contacts') }}">Contacts</a>
    </nav>
    <div class="copyright">
        &copy; {{ date('Y') }} FEUPBook Corp.
    </div>
</footer>
@endsection