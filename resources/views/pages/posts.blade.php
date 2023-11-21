@extends('layouts.app')

@section('title', 'Posts')

@section('sidebar')
    @include('partials.sidebar')
@endsection

@section('content')
<div class="flex-grow-1" style="margin-left: 280px;">
    <div class="d-flex justify-content-center">
        <div class="btn-group mt-3" role="group" aria-label="Post filters">
            <a href="{{ route('forYou') }}" class="btn {{ request()->routeIs('forYou') ? 'btn-primary' : 'btn-secondary' }}" style="width: 150px;">For You</a>
            <a href="{{ route('list') }}" class="btn {{ request()->routeIs('list') ? 'btn-primary' : 'btn-secondary' }}" style="width: 150px;">All Posts</a>
        </div>
    </div>
    <div class="overflow-auto h-100">
        <section id="posts" class="container my-4 d-flex justify-content-center">
            <div>
                @each('partials.post', $posts, 'post')
            </div>
        </section>
    </div>
</div>
@endsection
