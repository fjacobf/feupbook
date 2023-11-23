@extends('layouts.app')

@section('sidebar')
    @include('partials.sidebar')
@endsection

@section('content')
<div class="flex-grow-1" style="margin-left: 280px;">
    <div class="d-flex justify-content-center">
        <div class="btn-group mt-3" role="group" aria-label="Post filters">
            <a href="{{ route('forYou') }}" class="btn btn-lg {{ request()->routeIs('forYou') ? 'btn-primary' : 'btn-secondary' }}" style="width: 150px;">For You</a>
            <a href="{{ route('home') }}" class="btn btn-lg {{ request()->routeIs('home') ? 'btn-primary' : 'btn-secondary' }}" style="width: 150px;">Feed</a>
        </div>
    </div>
    <div class="container-lg d-flex justify-content-center align-items-center w-100">
        <ul class="list-unstyled mb-4">
            @forelse($posts as $post)
                @include('partials.post', ['post' => $post])
            @empty
                <div class="alert alert-info mt-4" role="alert">
                    <h4 class="alert-heading">No posts to display!</h4>
                    <p>When the user starts following other users, their posts will be displayed here.</p>
                </div>
            @endforelse
        </ul>
    </div>
    <a href="{{ url('/post/create') }}" class="btn btn-primary btn-lg position-fixed bottom-0 end-0 m-3">
        <i class="bi bi-plus-lg"></i> Add Post
    </a>
</div>

@endsection
