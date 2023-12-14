@extends('layouts.app')

@section('sidebar')
<div class="col-auto col-md-3 col-xl-2 px-sm-2 px-0 sidebar">
  <div class="d-flex flex-column align-sm-start position-fixed min-vh-100 p-4 bg-light">
      <a href="/home" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-dark text-decoration-none">
        <span class="fs-3 fw-bold d-none d-sm-inline">Feupbook</span>
      </a>
      <hr>
      <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item w-100">
          <a href="{{ route('home') }}" class="nav-link align-middle {{ request()->is('home') ? 'active' : 'link-dark' }} py-2" aria-current="page" style="font-size: 1.25rem;">
            <i class="bi bi-house-fill"></i>
            <p class="d-none d-sm-inline m-0 ms-3">Home</p>
          </a>
        </li>
        <li>
          <a href="{{url('/search')}}" class="nav-link {{ request()->is('search') ? 'active' : 'link-dark' }} py-2" style="font-size: 1.25rem;">          
          <i class="bi bi-search"></i>
          <p class="d-none d-sm-inline m-0 ms-3">Search</p>
          </a>
        </li>
        <li>
          <a href="#" class="nav-link link-dark py-2" style="font-size: 1.25rem;">
            <i class="bi bi-chat-dots-fill me-3"></i>
            <p class="d-none d-sm-inline m-0">Messages</p>
          </a>
        </li>
        <li>
          <a href="{{route('post.bookmarks')}}" class="nav-link {{ request()->is('post/bookmarks') ? 'active' : 'link-dark' }} py-2" style="font-size: 1.25rem;">
            <i id="bookmark-sidebar" class="bi bi-bookmark-fill me-3"></i>
            <p class="d-none d-sm-inline m-0">Bookmarks</p>
          </a>
        </li>
        <li>
          @auth
          <a href="{{ route('user.profile', ['id' => auth()->user()->user_id]) }}" class="nav-link {{ request()->routeIs('user.profile') && (request()->route()->parameter('id') == auth()->user()->user_id) ? 'active' : 'link-dark' }} py-2" style="font-size: 1.25rem;">
            <i class="bi bi-person-circle me-3"></i>
            <p class="d-none d-sm-inline m-0">Profile</p>
          </a>
          @endauth
          @guest
          <a href="{{ route('login') }}" class="nav-link link-dark py-2" style="font-size: 1.25rem;">
            <i class="bi bi-person-circle me-3"></i>
            <p class="d-none d-sm-inline m-0">Profile</p>
          </a>
          @endguest
        </li>
        <li>
          <a href="#" class="nav-link link-dark py-2" style="font-size: 1.25rem;">
            <i class="bi bi-gear-fill me-3"></i>
            <p class="d-none d-sm-inline m-0">Settings</p>
          </a>
        </li>
        <!-- Button to toggle additional items -->
        <li class="nav-item">
          <a href="#" class="nav-link link-dark py-2" style="font-size: 1.25rem;" data-bs-toggle="collapse" data-bs-target="#additionalItems" aria-expanded="false" aria-controls="additionalItems">
              <i class="bi bi-question-circle me-3"></i>
              <p class="d-none d-sm-inline m-0">Help</p>
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
          <strong class="d-none d-sm-inline">{{ $currentUser ? $currentUser->username : 'Guest' }}</strong>
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
</div>


@endsection