@extends('layouts.auth')

@section('content')

<div class="container">
    <div class="row">
        <div class="col-md-6">
            <img src="{{ asset('path-to-your-logo.png') }}" alt="FEUPBook Logo" class="img-fluid logo">
        </div>

        <div class="col-md-6">
            <h1 class="mt-4">Frequently Asked Questions (FAQ)</h1>
            <p>
                Welcome to the FEUPBook FAQ section. Find answers to common questions below.
                If you need further assistance, feel free to contact our support team.
            </p>

            <h2>General Questions</h2>
            <ul class="list-unstyled">
                <li>
                    <strong>Q: How do I create an account on FEUPBook?</strong><br>
                    A: You can create an account by clicking the "Create account" button on our homepage.
                </li>
                <li>
                    <strong>Q: Can I connect with alumni on FEUPBook?</strong><br>
                    A: Absolutely! FEUPBook allows you to connect with students, alumni, and faculty.
                </li>
            </ul>

            <h2>Account Management</h2>
            <ul class="list-unstyled">
                <li>
                    <strong>Q: How can I reset my password?</strong><br>
                    A: Visit the login page and click on the "Forgot Password" link to reset your password.
                </li>
                <li>
                    <strong>Q: How do I update my profile information?</strong><br>
                    A: You can update your profile information in the settings section after logging in.
                </li>
            </ul>

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