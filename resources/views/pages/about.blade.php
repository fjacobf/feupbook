@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row">
        <!-- Left side with a large logo or image -->
        <div class="col-md-6">
            <!-- The logo can be a background image in the CSS if preferred -->
            <img src="{{ asset('path-to-your-logo.png') }}" alt="FEUPBook Logo" class="img-fluid logo">
        </div>

        <!-- Right side with about information -->
        <div class="col-md-6">
            <h1 class="mt-4">About FEUPBook</h1>
            <p>
                FEUPBook is a social network that connects students, alumni, and faculty of FEUP (Faculty of Engineering of the University of Porto).
                Share updates, connect with friends, and stay updated with the latest happenings within the FEUP community.
            </p>

            <h2>Our Mission</h2>
            <p>
                Our mission is to provide a platform for the FEUP community to collaborate, share knowledge, and build meaningful connections.
                Whether you're a current student, an alumnus, or part of the faculty, FEUPBook is your space to stay connected.
            </p>

            <h2>Join Us Today</h2>
            <p>
                Ready to be a part of the FEUPBook community? Sign up today and start connecting with your fellow peers.
                Join the conversation, share your experiences, and be a part of the vibrant FEUP network.
            </p>

            <a href="{{ route('register') }}" class="btn btn-primary">Create account</a>
        </div>
    </div>
</div>

@endsection
@section('footer')
@include('partials.footer')
@endsection
