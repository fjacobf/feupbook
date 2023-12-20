@extends('layouts.app')

@section('sidebar')
<div class="d-flex flex-column align-sm-start min-vh-100 p-4 bg-light sidebar" id="side_nav">
    <div class="header-box d-flex justify-content-between">
      <a href="/home" class="text-dark text-decoration-none d-flex align-items-center">
        <span class="fs-3 fw-bold">Feupbook</span>
      </a>
      <button class="btn btn-lg d-md-none d-block close-btn"> <i class="bi bi-list"></i> </button>
    </div>
    <hr>
    <ul class="nav nav-pills flex-column mb-auto">
      <li class="nav-item w-100">
        <a href="{{ route('home') }}" class="nav-link align-middle {{ request()->is('home') ? 'active' : 'link-dark' }} py-2 px-2" aria-current="page" style="font-size: 1.25rem;">
          <i class="bi bi-house-fill me-2"></i>
          <p class="d-inline m-0">Home</p>
        </a>
      </li>
      <li class="nav-item w-100">
        <a href="{{url('/search')}}" class="nav-link {{ request()->is('search') ? 'active' : 'link-dark' }} py-2 px-2" style="font-size: 1.25rem;">          
        <i class="bi bi-search me-2"></i>
        <p class="d-inline m-0">Search</p>
        </a>
      </li>
      <li class="nav-item w-100">
        <a href="{{url('/group-chats')}}" class="nav-link {{ request()->is('group-chats') ? 'active' : 'link-dark' }} py-2 px-2" style="font-size: 1.25rem;">
          <i class="bi bi-chat-dots-fill me-2"></i>
          <p class="d-inline me-auto">Messages</p>
        </a>
      </li>
      <li class="nav-item w-100">
        <a href="{{route('post.bookmarks')}}" class="nav-link {{ request()->is('post/bookmarks') ? 'active' : 'link-dark' }} py-2 px-2" style="font-size: 1.25rem;">
          <i id="bookmark-sidebar" class="bi bi-bookmark-fill me-2"></i>
          <p class="d-inline m-0">Bookmarks</p>
        </a>
      </li>
      <li>
        @auth
        <a href="{{ route('user.profile', ['id' => auth()->user()->user_id]) }}" class="nav-link {{ request()->routeIs('user.profile') && (request()->route()->parameter('id') == auth()->user()->user_id) ? 'active' : 'link-dark' }} py-2 px-2" style="font-size: 1.25rem;">
          <i class="bi bi-person-circle me-2"></i>
          <p class="d-inline m-0">Profile</p>
        </a>
        @endauth
        @guest
        <a href="{{ route('login') }}" class="nav-link link-dark py-2 px-2" style="font-size: 1.25rem;">
          <i class="bi bi-person-circle me-2"></i>
          <p class="d-inline m-0">Profile</p>
        </a>
        @endguest
      </li>
      <li>
        @auth
        <a href="{{ route('notifications.list', ['id' => auth()->user()->user_id]) }}" class="nav-link {{ request()->routeIs('notifications.list') && (request()->route()->parameter('id') == auth()->user()->user_id) ? 'active' : 'link-dark' }} py-2 px-2" style="font-size: 1.25rem;">
          <i class="bi bi-bell-fill me-2"></i>
          <p class="d-inline m-0">Notifications</p>
        </a>
        @endauth
        @guest
        <a href="{{ route('login') }}" class="nav-link link-dark py-2 px-2" style="font-size: 1.25rem;">
          <i class="bi bi-bell-fill me-2"></i>
          <p class="d-inline m-0">Notifications</p>
        </a>
        @endguest
    
      <!-- Button to toggle additional items -->
      <li class="nav-item">
        <a href="#" class="nav-link link-dark py-2 px-2" style="font-size: 1.25rem;" data-bs-toggle="collapse" data-bs-target="#additionalItems" aria-expanded="false" aria-controls="additionalItems">
            <i class="bi bi-question-circle me-2"></i>
            <p class="d-inline m-0">Help</p>
        </a>
      </li>
    </ul>
    <!-- Additional items (collapsed by default) -->
    <div class="collapse" id="additionalItems">
      <ul class="nav nav-pills flex-column mb-auto">
          <li>
              <a href="{{route('contacts')}}" class="nav-link link-dark py-2" style="font-size: 1.25rem;">
                  Contacts
              </a>
          </li>
          <li>
              <a href="{{route('help')}}" class="nav-link link-dark py-2" style="font-size: 1.25rem;">
                  Help
              </a>
          </li>
          <li>
              <a href="{{route('faq')}}" class="nav-link link-dark py-2" style="font-size: 1.25rem;">
                  FAQ
              </a>
          </li>
          <li>
              <a href="{{route('about')}}" class="nav-link link-dark py-2" style="font-size: 1.25rem;">
                  About
              </a>
          </li>
      </ul>
    </div>

    
    <hr>
    <div class="dropdown">
      <a href="/home" class="d-flex align-items-center link-dark text-decoration-none dropdown-toggle py-3" id="dropdownUser2" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 1.25rem;">
        @if (Auth::check())
          <img src="{{ asset('images/profile_pics/' . auth()->user()->avatar)}}" alt="" width="48" height="48" class="rounded-circle profile-pic me-3">
        @else
          <img src="{{ asset('images/profile_pics/default_avatar.png') }}" alt="" width="48" height="48" class="rounded-circle profile-pic me-3">
        @endif
        <strong>{{ $currentUser ? (strlen($currentUser->username) > 11 ? substr($currentUser->username, 0, 11) . '...' : $currentUser->username) : 'Guest' }}</strong>
      </a>
      @if (Auth::check())
        <ul class="dropdown-menu text-small shadow" aria-labelledby="dropdownUser2">
          <li><a class="dropdown-item" href="#">Add another account</a></li>
          <li><a class="dropdown-item" href="{{url('/logout')}}">Sign out</a></li>
        </ul>
      @else
        <ul class="dropdown-menu text-small shadow" aria-labelledby="dropdownUser2">
          <li><a class="dropdown-item" href="{{url('/login')}}">Sign in</a></li>
          <li><a class="dropdown-item" href="{{url('/register')}}">New account</a></li>
        </ul>
      @endif
    </div>
</div>
@endsection