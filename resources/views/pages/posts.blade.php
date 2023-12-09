@extends('layouts.app')

@section('sidebar')
    @include('partials.sidebar')
@endsection

@section('content')
<div class="col">
    <div class="d-flex justify-content-center">
        <div class="btn-group mt-3" role="group" aria-label="Post filters">
            <a href="{{ route('forYou') }}" class="btn btn-lg {{ request()->routeIs('forYou') ? 'btn-primary' : 'btn-secondary' }}" style="width: 150px;">For You</a>
            <a href="{{ route('home') }}" class="btn btn-lg {{ request()->routeIs('home') ? 'btn-primary' : 'btn-secondary' }}" style="width: 150px;">Feed</a>
        </div>
    </div>
    <div class="container-lg d-flex justify-content-center align-items-center w-50">
        <ul class="list-unstyled mb-4 w-100">
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
    @can('create', App\Models\Post::class)
    <a href="{{ url('/post/create') }}" class="btn btn-primary btn-lg position-fixed bottom-0 end-0 m-3">
        <i class="bi bi-plus-lg"></i> Add Post
    </a>
    @endcan
</div>
@endsection
