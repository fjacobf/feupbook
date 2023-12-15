@extends('layouts.auth')

@section('content')

<div class="container d-flex flex-column justify-content-center m-auto w-75" style="max-width: 900px">
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