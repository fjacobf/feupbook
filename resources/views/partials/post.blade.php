<div class="post mt-4" id="post-{{$post->post_id}}">
    <div class="card">
        <div class="card-header d-flex justify-content-between fs-5">
            <div>
                <img src="{{ asset('images/profile_pics/' . $post->user->avatar) }}" class="rounded-circle" style="width: 20px; height: 20px;">
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
            $truncatedContent = mb_strlen($postContent) > 400 ? mb_substr($postContent, 0, 400) . '...' : $postContent;
            ?>
            <div id="short-content-{{ $post->post_id }}" class="post-content-short">
                <h5 class="card-text text-black">{!! $truncatedContent !!}</h5>
                @if (mb_strlen($postContent) > 400)
                    <button class="btn btn-link text-info p-0" onclick="toggleContent({{ $post->post_id }}, 'more')">See More</button>
                @endif
            </div>

            <div id="full-content-{{ $post->post_id }}" class="post-content-full d-none">
                <h5 class="card-text text-black">{!! $postContent !!}</h5>
                <button class="btn btn-link text-info p-0" onclick="toggleContent({{ $post->post_id }}, 'less')">See Less</button>
            </div>
            @if ($post->image)
                <img src="{{ asset($post->image) }}" class="rounded mx-auto d-block w-50 mt-2" alt="">
            @endif
        </div>
        <div class="d-flex justify-content-end">
            <a href="{{ route('post.show', ['id' => $post->post_id]) }}" class="text-info me-2 mb-1">Go to post</a> 
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
            @can('delete', $post)
                <div class="d-flex justify-content-end mt-2">
                    @can('update', $post)
                        <a href="{{ route('post.edit', ['id' => $post->post_id]) }}" class="btn btn-primary bi-pencil-fill me-2"></a>
                    @endcan
            
                    <form action="{{ route('post.delete', ['id' => $post->post_id]) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger bi-trash-fill" onclick="return confirm('Are you sure you want to delete this post?')"></button>
                    </form>
                </div>
            @endcan
        </div>
    </div>
</div>


