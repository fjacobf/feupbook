@extends('layouts.app')

@section('title', 'Posts')

@section('sidebar')
    @include('partials.sidebar')
@endsection

@section('content')
<div class="flex-grow-1" style="margin-left: 280px;">
    <div class="d-flex justify-content-center">
        <div class="btn-group mt-3" role="group" aria-label="Post filters">
            <a href="{{ route('forYou') }}" class="btn btn-lg {{ request()->routeIs('forYou') ? 'btn-primary' : 'btn-secondary' }}" style="width: 150px;">For You</a>
            <a href="{{ route('home') }}" class="btn btn-lg {{ request()->routeIs('home') ? 'btn-primary' : 'btn-secondary' }}" style="width: 150px;">All Posts</a>
        </div>
    </div>
    <div class="overflow-auto h-100">
        <section id="posts" class="container my-4 d-flex justify-content-center">
            <div>
                @forelse($posts as $post)
                    @include('partials.post', ['post' => $post])
                @empty
                    <div class="alert alert-info" role="alert">
                        There are no posts to display.
                    </div>
                @endforelse
            </div>
        </section>
    </div>
    <a href="{{ url('/post/create') }}" class="btn btn-primary btn-lg position-fixed bottom-0 end-0 m-3">
        <i class="bi bi-plus-lg"></i> Add Post
    </a>
</div>

@endsection