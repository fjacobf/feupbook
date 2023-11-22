@extends('layouts.app')

@section('sidebar')

<div class="d-flex flex-column flex-shrink-0 p-4 bg-light" style="width: 280px; height: 100vh; position: fixed; overflow-y: auto;">
    <a href="/home" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-dark text-decoration-none">
      <span class="fs-3 fw-bold">Feupbook</span>
    </a>
    <hr>
    <ul class="nav nav-pills flex-column mb-auto">
      <li class="nav-item">
        <a href="{{url('/home')}}" class="nav-link active py-2" aria-current="page" style="font-size: 1.25rem;">
          <i class="bi bi-house-fill me-3"></i>
          Home
        </a>
      </li>
      <li>
        <a href="#" class="nav-link link-dark py-2" style="font-size: 1.25rem;">
          <i class="bi bi-search me-3"></i>
          Search
        </a>
      </li>
      <li>
        <a href="#" class="nav-link link-dark py-2" style="font-size: 1.25rem;">
          <i class="bi bi-chat-dots-fill me-3"></i>
          Messages
        </a>
      </li>
      <li>
        <a href="#" class="nav-link link-dark py-2" style="font-size: 1.25rem;">
          <i class="bi bi-bookmark-fill me-3"></i>
          Bookmarks
        </a>
      </li>
      <li>
        @auth
        <a href="{{ route('user.profile', ['id' => auth()->user()->user_id]) }}" class="nav-link link-dark py-2" style="font-size: 1.25rem;">
          <i class="bi bi-person-circle me-3"></i>
          Profile
        </a>
        @endauth
        @guest
        <a href="{{ route('login') }}" class="nav-link link-dark py-2" style="font-size: 1.25rem;">
          <i class="bi bi-person-circle me-3"></i>
          Profile
        </a>
        @endguest
      </li>
      <li>
        <a href="#" class="nav-link link-dark py-2" style="font-size: 1.25rem;">
          <i class="bi bi-gear-fill me-3"></i>
          Settings
        </a>
      </li>
    </ul>
    <hr>
    <div class="dropdown">
      <a href="/home" class="d-flex align-items-center link-dark text-decoration-none dropdown-toggle py-3" id="dropdownUser2" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 1.25rem;">
        <img src="https://github.com/mdo.png" alt="" width="48" height="48" class="rounded-circle me-3">
        <strong>{{ $currentUser ? $currentUser->username : 'Guest' }}</strong>
      </a>
      <ul class="dropdown-menu text-small shadow" aria-labelledby="dropdownUser2">
        <li><a class="dropdown-item" href="#">Add another account</a></li>
        <li><a class="dropdown-item" href="{{url('/logout')}}">Sign out</a></li>
      </ul>
    </div>
</div>

@endsection