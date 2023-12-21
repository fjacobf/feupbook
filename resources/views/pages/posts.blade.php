@extends('layouts.app')

@section('sidebar')
    @include('partials.sidebar')
@endsection

@section('content')
<div class="d-flex flex-column align-items-center ms-auto me-auto w-100 overflow-auto">
    <div class="d-flex justify-content-center">
        <div class="btn-group mt-3" role="group" aria-label="Post filters">
            <a href="{{ route('forYou') }}" class="btn btn-lg {{ request()->routeIs('forYou') ? 'btn-primary' : 'btn-secondary' }}" style="width: 150px;">For You</a>
            <a href="{{ route('home') }}" class="btn btn-lg {{ request()->routeIs('home') ? 'btn-primary' : 'btn-secondary' }}" style="width: 150px;">Feed</a>
        </div>
    </div>
    <div class="container-lg d-flex flex-column justify-content-center align-items-center w-100">
        <ul id="post-list" data-page-context="{{ $pageContext }}" data-current-page="{{ $posts->currentPage() }}" data-next-page-url="{{ $posts->nextPageUrl() }}" class="list-unstyled mb-4 w-100" style="max-width: 800px">
            @forelse($posts as $post)
                @include('partials.post', ['post' => $post])
            @empty
                <div class="alert alert-info mt-4" role="alert">
                    <h4 class="alert-heading">No posts to display!</h4>
                    <p>When the user starts following other users, their posts will be displayed here.</p>
                </div>
            @endforelse
        </ul>
        @if ($posts->hasMorePages())
            <button id="load-more" class="btn btn-info mb-3" onclick="loadMorePosts()">See More Posts</button>
        @endif
    </div>
    @can('create', App\Models\Post::class)
    <a href="{{ url('/post/create') }}" class="btn btn-primary btn-lg position-fixed bottom-0 end-0 mb-3 me-3">
        <i class="bi bi-plus-lg"></i> 
        <p class="d-none d-sm-inline">Add Post</p>
    </a>
    @endcan
</div>
@endsection
