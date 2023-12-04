<div class="post mt-4">
        <div class="card" style="width: 50em;">
            <div class="card-header d-flex justify-content-between fs-5">
                <small><a href="{{ route('user.profile', ['id' => $post->user->user_id]) }}" class="link-primary:hover">{{$post->user->name}}</a></small>
                <small class="text-muted"><span class="text-muted">@</span>{{ $post->user->username }}</small>
                <small class="text-black">{{ time_since($post->created_at) }}</small>
            </div>
            <a href="{{ route('showPost', ['id' => $post->post_id]) }}" class="text-decoration-none">
                <div class="card-body">
                    <h5 class="card-text text-black">{{ $post->content }}</h5>
                </div>
            </a> 

            <div class="card-footer d-flex justify-content-around">
                <div class="d-flex align-items-center">
                    <button class="btn bi bi-heart custom-btn-like"></button>
                    <span>{{ $post->likesCount() }}</span>
                </div>

                <div class="d-flex align-items-center">
                    <a href="{{ route('showPost', ['id' => $post->post_id]) }}" class="btn bi bi-chat custom-btn-comment"></a>
                    <span>{{ $post->commentsCount() }}</span>
                </div>
                
                <button class="btn bi bi-bookmark custom-btn-bookmark"></button>

                @canany(['update', 'delete'], $post)
                    <div class="ms-auto">
                            <a href="{{ route('editPost', ['id' => $post->post_id]) }}" class="btn btn-primary me-2">Edit Post</a>
                            <form action="{{ route('deletePost', ['id' => $post->post_id]) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this post?')">Delete Post</button>
                            </form>
                    </div>
                @endcanany
            </div>
        </div>
</div>
