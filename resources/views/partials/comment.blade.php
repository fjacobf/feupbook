@if ($comment->previous == null)
    <div class="d-flex flex-column ms-3">
        <div class="d-flex justify-content-between">
            <div class="d-flex align-items-center">
                <div class="card-text">
                    <img src="{{ asset('images/profile_pics/' . $comment->user->avatar) }}" class="rounded-circle" alt="User avatar" style="width: 25px; height: 25px;">
                    <a class="text-black link-underline link-underline-opacity-0" href="{{ route('user.profile', ['id' => $comment->user->user_id]) }}">
                        <strong style="margin:0; font-size:1.1rem;" class="card-text">{{ $comment->user->username }}&nbsp</strong>
                    </a>
                    <span>{{ $comment->content }}</span>
                </div>
            </div>
            <div class="buttons d-flex">
                @can('delete', $comment)
                    <form class="d-flex align-items-center" action="{{ route('comment.delete', ['id' => $comment->comment_id]) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn"
                            onclick="return confirm('Are you sure you want to delete this comment?')"><i style="color: red"
                                class="bi bi-trash-fill"></i>
                        </button>
                        <input type="hidden" name="comment_id" id="comment_id_{{ $comment->comment_id }}" value="{{ $comment->commxent_id }}">
                    </form>
                @endcan
                @if ($comment->isLiked() == true)
                <button id="btn-comment{{ $comment->comment_id }}" class="btn bi bi-heart-fill" onclick="handleLikeDislikeComment({{$comment->comment_id}},'dislike')"></button>
                @else
                    <button id="btn-comment{{ $comment->comment_id }}" class="btn bi bi-heart " onclick="handleLikeDislikeComment({{$comment->comment_id}},'like')"></button>
                @endif
            </div>
        </div>
        @can('create', [App\Models\Comment::class, $post])
            <div style="display:none;" id="replyDiv{{ $comment->comment_id }}">
                <form class="d-flex justify-content-center mt-2" action="{{ route('comment.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="post_id" value="{{ $post->post_id }}">
                    <input type="hidden" name="comment_id" value="{{ $comment->comment_id }}">
                    <textarea class="comment-textarea border-0 w-100" style="resize:none" id="content" name="content" cols="30" rows="1" placeholder="Add a reply..."></textarea>
                    <button type="submit" class="post-button" style="display:none">Reply</button>
                </form>
                <hr>
            </div>
            <div style="gap:10px; font-size: 0.8rem;" class="d-flex align-items-center mb-2">
                <p class="m-0 comment-date">{{time_since($comment->created_at)}}</p>
                <p id="comment-like-count-{{$comment->comment_id}}" class="m-0 comment-likes">{{$comment->likeCounts()}} likes</p>
                <a style="cursor:pointer;"  class="link-underline-secondary" onclick="reply({{ $comment->comment_id }})">Reply</a>
            </div>
        @endcan
    </div>
@endif

@foreach ($comment->replies()->orderBy('created_at', 'desc')->get() as $reply)
    <div style="margin: 0 0 0.5rem 3rem;" class="d-flex flex-column">
        <div class="d-flex justify-content-between">
            <div style="margin-left:10px; display:flex; align-items: center;">
                <a class="text-black link-underline link-underline-opacity-0" href="{{ route('user.profile', ['id' => $reply->user->user_id]) }}">
                    <img src="{{ asset('images/profile_pics/' . $reply->user->avatar) }}" class="rounded-circle" alt="User avatar" style="width: 20px; height: 20px;">
                    <strong style="margin:0; font-size:1.1rem;" class="card-text">{{ $reply->user->username }}&nbsp</strong>
                </a>
                <p class="card-text">{{ $reply->content }}</p>
            </div>
            <div class="buttons d-flex">
                @can('delete', $reply)
                    <form class="d-flex align-items-center" action="{{ route('comment.delete', ['id' => $reply->comment_id]) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn"
                        onclick="return confirm('Are you sure you want to delete this comment?')"><i style="color: red"
                        class="bi bi-trash-fill"></i></button>
                        <input type="hidden" name="comment_id" value="{{ $reply->comment_id }}">
                    </form>
                @endcan
                @if ($reply->isLiked() == true)
                <button id="btn-comment{{ $reply->comment_id }}" class="btn bi bi-heart-fill" onclick="handleLikeDislikeComment({{$reply->comment_id}},'dislike')"></button>
                @else
                    <button id="btn-comment{{ $reply->comment_id }}" class="btn bi bi-heart" onclick="handleLikeDislikeComment({{$reply->comment_id}},'like')"></button>
                @endif
            </div>
        </div>
        <div style="gap:10px; font-size: 0.8rem;" class="d-flex align-items-center mb-2">
            <p class="m-0 comment-date">{{time_since($reply->created_at)}}</p>
            <p id="comment-like-count-{{$reply->comment_id}}" class="m-0 comment-likes"> {{$reply->likeCounts()}} likes</p>
        </div>
    </div>
@endforeach

<script type="text/javascript">

    

</script>
