<div class="post mt-4">
        <div class="card" style="width: 50em;">
            <div class="card-header d-flex justify-content-between fs-5">
                <small><a href="{{ route('user.profile', ['id' => $post->user->user_id]) }}" class="link-primary:hover">{{$post->user->name}}</a></small>
                <small class="text-muted"><span class="text-muted">@</span>{{ $post->user->username }}</small>
                <small class="text-black">{{ time_since($post->created_at) }}</small>
            </div>
            <a href="{{ route('post.show', ['id' => $post->post_id]) }}" class="text-decoration-none">
                <div class="card-body">
                    <h5 class="card-text text-black">{{ $post->content }}</h5>
                </div>
            </a> 

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
                            <a href="{{ route('post.edit', ['id' => $post->post_id]) }}" class="btn btn-primary me-2">Edit Post</a>
                            <form action="{{ route('post.delete', ['id' => $post->post_id]) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this post?')">Delete Post</button>
                            </form>
                    </div>
                @endcanany
            </div>
        </div>
</div>


