@extends('layouts.app')

@section('title', $card->name)

@section('footer')
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item"><a class="nav-link" href="{{ url('/about') }}">About</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ url('/help') }}">Help Center</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ url('/terms') }}">Terms of Service</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ url('/privacy') }}">Privacy Policy</a></li>
        </ul>
        <div class="copyright">
            &copy; {{ date('Y') }} FEUPBook Corp.
        </div>
    </div>
</nav>

@endsection