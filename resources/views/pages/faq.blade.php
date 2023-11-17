@extends('layouts.app')

@section('content')

<div class="wrapper">
    <!-- Left side with a large logo or image -->
    <div class="left-side">
        <!-- The logo can be a background image in the CSS if preferred -->
        <img src="{{ asset('path-to-your-logo.png') }}" alt="FEUPBook Logo" class="logo">
    </div>

    <!-- Right side with FAQ information -->
    <div class="right-side">
        <h1>Frequently Asked Questions (FAQ)</h1>
        <p>
            Welcome to the FEUPBook FAQ section. Find answers to common questions below.
            If you need further assistance, feel free to contact our support team.
        </p>
        <h2>General Questions</h2>
        <p>
            <strong>Q: How do I create an account on FEUPBook?</strong><br>
            A: You can create an account by clicking the "Create account" button on our homepage.
        </p>
        <p>
            <strong>Q: Can I connect with alumni on FEUPBook?</strong><br>
            A: Absolutely! FEUPBook allows you to connect with students, alumni, and faculty.
        </p>
        <h2>Account Management</h2>
        <p>
            <strong>Q: How can I reset my password?</strong><br>
            A: Visit the login page and click on the "Forgot Password" link to reset your password.
        </p>
        <p>
            <strong>Q: How do I update my profile information?</strong><br>
            A: You can update your profile information in the settings section after logging in.
        </p>
        <a href="{{ route('register') }}"> <button class="create-account-btn">Create account</button> </a>
    </div>
</div>

<footer>
    <nav>
        <a href="{{ url('/about') }}">About</a>
        <a href="{{ url('/help') }}">Help Center</a>
        <a href="{{ url('/faq') }}" class="active">FAQ</a>
        <a href="{{ url('/contacts') }}">Contacts</a>
    </nav>
    <div class="copyright">
        &copy; {{ date('Y') }} FEUPBook Corp.
    </div>
</footer>
@endsection
