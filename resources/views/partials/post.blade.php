<div class="post mt-4">
        <div class="card" style="width: 50em;">
            <div class="card-header d-flex justify-content-between fs-5">
                <small><a href="{{ route('user.profile', ['id' => $post->user->user_id]) }}" class="link-primary:hover">{{$post->user->name}}</a></small>
                <small class="text-muted"><span class="text-muted">@</span>{{ $post->user->username }}</small>
                <small class="text-black">{{ \Carbon\Carbon::parse($post->created_at)->format('H:i d-m-y') }}</small>
            </div>
            <a href="{{ route('showPost', ['id' => $post->post_id]) }}" class="text-decoration-none">
                <div class="card-body">
                    <h5 class="card-text text-black">{{ $post->content }}</h5>
                </div>
            </a>
                <div class="card-body">
                    <h4 class="card-text">Comments:</h4>
                    @forelse($post->comments as $comment)
                    @include('partials.comment', ['comment' => $comment])
                    @empty
                        <p style="color: gray; font-size: 0.8rem">There are no comments on this post.</p>
                    @endforelse

                    <form style="margin: 1rem; display:flex; justify-content: center" action="{{ route('storeComment') }}" method="POST">
                        @csrf
                        <textarea style="resize:none" id="content" name="content" cols="30" rows="1"
                            placeholder="Adicione um comentário..."></textarea> <!--textarea not auto expanding-->
                        <input type="hidden" name="post_id" id="post_id" value="{{ $post->post_id }}">
                        <input type="hidden" name="comment_id" id="comment_id" value="{{ NULL }}">
                        <button type="submit" class="btn btn-primary">Comment</button>
                    </form>
                </div>
                @can('update', $post)
                    <div class="card-footer d-flex justify-content-end">
                        <!-- Edit Button -->
                        <a href="{{ route('editPost', ['id' => $post->post_id]) }}" class="btn btn-primary me-2">Edit Post</a>
                @endcan

                @can('delete', $post)
                        <!-- Delete Button Form -->
                        <form action="{{ route('deletePost', ['id' => $post->post_id]) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this post?')">Delete Post</button>
                        </form>
                    </div>
                @endcan
        </div>
</div>
