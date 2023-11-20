@extends('layouts.auth')

@section('footer')
<div class="container">
        <ul class="nav justify-content-center mb-1">
            <li class="nav-item">
                <a class="nav-link text-white" href="{{ url('/about') }}">About</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="{{ url('/help') }}">Help Center</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="{{ url('/terms') }}">Terms of Service</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="{{ url('/privacy') }}">Privacy Policy</a>
            </li>
        </ul>
        <div class="text-light">&copy; {{ date('Y') }} FEUPBook Corp.</div>
</div>
@endsection