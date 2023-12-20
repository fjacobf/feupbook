@extends('layouts.auth')

@section('content')

<div class="container d-flex flex-column justify-content-center m-auto w-75" style="max-width: 900px">
    <h1 class="mt-4">About FEUPbook</h1>
    <p>
        FEUPbook is a social network that connects students, alumni, and faculty of FEUP (Faculty of Engineering of the University of Porto).
        Share updates, connect with friends, and stay updated with the latest happenings within the FEUP community.
    </p>

    <h2>Our Mission</h2>
    <p>
        Our mission is to provide a platform for the FEUP community to collaborate, share knowledge, and build meaningful connections.
        Whether you're a current student, an alumnus, or part of the faculty, FEUPbook is your space to stay connected.
    </p>

    <h2>Join Us Today</h2>
    <p>
        Ready to be a part of the FEUPbook community? Sign up today and start connecting with your fellow peers.
        Join the conversation, share your experiences, and be a part of the vibrant FEUP network.
    </p>

    @if(!Auth::check())
        <div class="d-flex justify-content-center">
            <a href="{{ route('login') }}" class="btn btn-primary w-25 me-3">Login</a>
            <a href="{{ route('register') }}" class="btn btn-outline-secondary w-25"><strong>Create account</strong></a>
        </div>
    @else
    <a href="{{ route('home') }}" class="btn btn-primary w-25">Home</a>
    @endif
</div>

@endsection
@section('footer')
@include('partials.footer')
@endsection
