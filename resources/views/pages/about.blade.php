@extends('layouts.auth')

@section('content')

<div class="container">
    <div class="row">
        <div class="col-md-6">
            <img src="{{ asset('path-to-your-logo.png') }}" alt="FEUPBook Logo" class="img-fluid logo">
        </div>

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
