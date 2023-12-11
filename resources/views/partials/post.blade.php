<div class="post mt-4">
        <div class="card" style="width: 50em;">
            <div class="card-header d-flex justify-content-between fs-5">
                <small><a href="{{ route('user.profile', ['id' => $post->user->user_id]) }}" class="link-primary:hover text-decoration-none">{{$post->user->name}}</a></small>
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
            <div class="d-flex justify-content-end">
                <a href="{{ route('post.show', ['id' => $post->post_id]) }}" class="text-decoration-none text-info me-2 mb-1">Go to post</a> 
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
                        <button id="btn-bookmark-{{ $post->post_id }}" class="btn bi bi-bookmark-fill custom-btn-bookmark" onclick="handleBookmark({{$post->post_id}},'unbookmark')"></button>
                    @else
                        <button id="btn-bookmark-{{ $post->post_id }}" class="btn bi bi-bookmark custom-btn-bookmark" onclick="handleBookmark({{$post->post_id}},'bookmark')"></button>
                    @endif
                    <span id="bookmark-count-{{ $post->post_id }}">{{ $post->bookmarksCount() }}</span>
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


