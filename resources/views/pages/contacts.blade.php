@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row">
        <!-- Left side with a large logo or image -->
        <div class="col-md-6">
            <!-- The logo can be a background image in the CSS if preferred -->
            <img src="{{ asset('path-to-your-logo.png') }}" alt="FEUPBook Logo" class="img-fluid logo">
        </div>

        <!-- Right side with contact information -->
        <div class="col-md-6">
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

            <h2>Submit a Form</h2>
            <p>
                Use our online contact form to submit your questions or feedback. We'll get back to you as soon as possible.
            </p>

            <a href="{{ route('register') }}" class="btn btn-primary">Create account</a>
        </div>
    </div>
</div>

@endsection
@section('footer')
@include('partials.footer')
@endsection
