@extends('layouts.app')

@section('sidebar')
    @include('partials.sidebar')
@endsection

@section('content')
<div class="col">
    <section class="d-flex justify-content-center">
        <div class="post mt-4">
            <div class="card" style="width: 50em;">
                <div class="card-header d-flex justify-content-between fs-5">
                    <small><a href="{{ route('user.profile', ['id' => $post->user->user_id]) }}" class="link-primary:hover">{{$post->user->name}}</a></small>
                    <small class="text-muted"><span class="text-muted">@</span>{{ $post->user->username }}</small>
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
                </div>

                <div class="card-footer d-flex justify-content-around">
                    <div class="d-flex align-items-center custom-btn-container">
                        @if ($post->isLiked() == true)
                            <button id="btn-{{ $post->post_id }}" class="btn bi bi-heart-fill custom-btn-like" onclick="handleLikeDislike({{$post->post_id}},'dislike')"></button>
                        @else
                            <button id="btn-{{ $post->post_id }}" class="btn bi bi-heart custom-btn-like" onclick="handleLikeDislike({{$post->post_id}},'like')"></button>
                        @endif

                        <span id="like-count-{{ $post->post_id }}">{{ $post->likesCount() }}</span>
                    </div>

                    <div class="d-flex align-items-center custom-btn-container">
                        <a href="{{ route('post.show', ['id' => $post->post_id]) }}" class="btn bi bi-chat custom-btn-comment"></a>
                        <span>{{ $post->commentsCount() }}</span>
                    </div>

                    <div class="d-flex align-items-center custom-btn-container">
                        @if ($post->isBookmarked() == true)
                            <form action="{{ route('post.unbookmark', ['id' => $post->post_id]) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button style="color: blue;" class="btn bi bi-bookmark-fill custom-btn-bookmark"></button>
                            </form>
                        @else
                            <form action="{{ route('post.bookmark', ['id' => $post->post_id]) }}" method="POST">
                                @csrf
                                <button class="btn bi bi-bookmark custom-btn-bookmark"></button>
                            </form>
                        @endif
                        <span>{{ $post->bookmarksCount() }}</span>
                    </div>

                    @canany(['update', 'delete'], $post)
                        <div class="ms-auto">
                                <a href="{{ route('post.edit', ['id' => $post->post_id]) }}" class="btn btn-primary bi-pencil-fill me-2"></a>
                                <form action="{{ route('post.delete', ['id' => $post->post_id]) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger bi-trash-fill" onclick="return confirm('Are you sure you want to delete this post?')"></button>
                                </form>
                        </div>
                    @endcanany
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
    </section>
</div>
@endsection
