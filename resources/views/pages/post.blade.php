@extends('layouts.app')

@section('sidebar')
    @include('partials.sidebar')
@endsection

@section('content')

<div class="d-flex flex-column align-items-center ms-auto me-auto w-100 overflow-auto">
    <div class="container-lg d-flex flex-column justify-content-center align-items-center w-75">
        <ul id="post-list" class="list-unstyled mb-4 w-100" style="max-width: 800px">
            <div class="post mt-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between fs-5">
                        <div>
                            <img src="{{ asset('images/profile_pics/' . $post->user->avatar) }}" class="rounded-circle" style="width: 25px; height: 25px;">
                            <small><a href="{{ route('user.profile', ['id' => $post->user->user_id]) }}" class="link-primary:hover">{{$post->user->name}}</a></small>
                        </div>
                        <small class="text-muted d-none d-sm-block"><span class="text-muted">@</span>{{ $post->user->username }}</small>
                        <small class="text-black">{{ time_since($post->created_at) }}</small>
                    </div>

                    <div class="card-body">
                        <?php
                        $postContent = preg_replace_callback('/@(\w+)/', function($matches) {
                            $user = \App\Models\User::where('username', $matches[1])->first();
                            return $user ? '<a class="link-primary:hover text-decoration-none" href="' . route('user.profile', ['id' => $user->user_id]) . '">' . $matches[0] . '</a>' : $matches[0];
                        }, $post->content);
                        ?>
                        <h5 class="card-text text-black">{!! $postContent !!}</h5>
                        @if ($post->image)
                            <img src="{{ asset($post->image) }}" class="rounded mx-auto d-block w-50" alt="">
                        @endif
                    </div>

                    <div class="card-footer d-flex flex-column justify-content-around">
                        <div class="d-flex align-items-center justify-content-around custom-btn-container">
                            <div class="likes">
                                @if ($post->isLiked() == true)
                                    <button id="btn-{{ $post->post_id }}" class="btn bi bi-heart-fill custom-btn-like" onclick="handleLikeDislike({{$post->post_id}},'dislike')"></button>
                                @else
                                    <button id="btn-{{ $post->post_id }}" class="btn bi bi-heart custom-btn-like" onclick="handleLikeDislike({{$post->post_id}},'like')"></button>
                                @endif

                                <span id="like-count-{{ $post->post_id }}">{{ $post->likesCount() }}</span>
                            </div>

                            <div class="comments">
                                <a href="{{ route('post.show', ['id' => $post->post_id]) }}" class="btn bi bi-chat custom-btn-comment"></a>
                                <span>{{ $post->commentsCount() }}</span>
                            </div>

                            <div class="bookmarks">
                                @if ($post->isBookmarked() == true)
                                    <button id="btn-bookmark-{{ $post->post_id }}" class="btn bi bi-bookmark-fill custom-btn-bookmark" onclick="handleBookmark({{$post->post_id}},'unbookmark')"></button>
                                @else
                                    <button id="btn-bookmark-{{ $post->post_id }}" class="btn bi bi-bookmark custom-btn-bookmark" onclick="handleBookmark({{$post->post_id}},'bookmark')"></button>
                                @endif
                                <span id="bookmark-count-{{ $post->post_id }}">{{ $post->bookmarksCount() }}</span>
                            </div>
                        </div>

                        <div class="ms-auto">
                            @can('report', $post)
                                <a href="{{ route('post.showReportForm', ['id' => $post->post_id]) }}" class="btn btn-warning bi-flag-fill"></a>
                            @endcan

                            @can('update', $post)
                                <a href="{{ route('post.edit', ['id' => $post->post_id]) }}" class="btn btn-primary bi-pencil-fill me-2"></a>
                            @endcan

                            @can('delete', $post)
                                <form action="{{ route('post.delete', ['id' => $post->post_id]) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger bi-trash-fill" onclick="return confirm('Are you sure you want to delete this post?')"></button>
                                </form>
                            @endcan
                        </div>
                    </div>

                    <div class="card-body">
                        <form class="d-flex justify-content-center" action="{{ route('comment.store') }}" method="POST">
                            @csrf
                            <textarea style="resize:none" id="content" name="content" cols="30" rows="1"
                                placeholder="Adicione um comentário..."></textarea> 
                            <input type="hidden" name="post_id" id="post_id" value="{{ $post->post_id }}">
                            <input type="hidden" name="comment_id" id="comment_id" value="{{ NULL }}">
                            <button type="submit" class="btn btn-primary ms-2">Comment</button>
                        </form>

                        @forelse($post->comments()->orderBy('created_at', 'desc')->get() as $comment)
                        @include('partials.comment', ['comment' => $comment])
                        @empty
                            <div class="d-flex justify-content-center">
                                <p class="text-secondary mt-3 mb-0">There are no comments on this post.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </ul>
    </div>
</div>
@endsection
