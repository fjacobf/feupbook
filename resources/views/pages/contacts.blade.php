@extends('layouts.app')

@section('content')

<div class="wrapper">
    <!-- Left side with a large logo or image -->
    <div class="left-side">
        <!-- The logo can be a background image in the CSS if preferred -->
        <img src="{{ asset('path-to-your-logo.png') }}" alt="FEUPBook Logo" class="logo">
    </div>

    <!-- Right side with contact information -->
    <div class="right-side">
        <h1>Contact Us</h1>
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
        <h2>Submit a Form</h2>
        <p>
            Use our online contact form to submit your questions or feedback. We'll get back to you as soon as possible.
        </p>
        <a href="{{ route('register') }}"> <button class="create-account-btn">Create account</button> </a>
    </div>
</div>

<footer>
    <nav>
        <a href="{{ url('/about') }}">About</a>
        <a href="{{ url('/help') }}">Help Center</a>
        <a href="{{ url('/faq') }}">FAQ</a>
        <a href="{{ url('/contacts') }}" class="active">Contacts</a>
    </nav>
    <div class="copyright">
        &copy; {{ date('Y') }} FEUPBook Corp.
    </div>
</footer>
@endsection
