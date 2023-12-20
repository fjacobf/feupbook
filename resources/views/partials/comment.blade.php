@if ($comment->previous == null)
    <div class="d-flex flex-column ms-3">
        <div class="d-flex justify-content-between">
            <div class="d-flex align-items-center">
                <div>
                    <img src="{{ asset('images/profile_pics/' . $comment->user->avatar) }}" class="rounded-circle" alt="User avatar" style="width: 20px; height: 20px;">
                    <strong style="margin:0; font-size:1.1rem;" class="card-text">{{ $comment->user->username }}&nbsp</strong>
                </div>
                <p class="card-text">{{ $comment->content }}</p>
            </div>
            <div class="buttons d-flex">
                @can('delete', $comment)
                    <form action="{{ route('comment.delete', ['id' => $comment->comment_id]) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn"
                            onclick="return confirm('Are you sure you want to delete this comment?')"><i style="color: red"
                                class="bi bi-trash-fill"></i>
                        </button>
                        <input type="hidden" name="comment_id" id="comment_id_{{ $comment->comment_id }}" value="{{ $comment->comment_id }}">
                    </form>
                @endcan
                @if ($comment->isLiked() == true)
                <form action="{{ route('comment.dislike', ['id' => $comment->comment_id]) }}" method="POST">
                    @csrf
                    <button class="btn">
                        <i style="color: red" class="bi bi-heart-fill"></i>
                    </button>
                </form>
                @else
                    <form action="{{ route('comment.like', ['id' => $comment->comment_id]) }}" method="POST">
                        @csrf
                        <button class="btn">
                            <i style="color: red" class="bi bi-heart"></i>
                        </button>
                    </form>
                @endif
            </div>
        </div>
        @can('create', [App\Models\Comment::class, $post])
            <div style="display:none;" id="replyDiv{{ $comment->comment_id }}">
                <form class="d-flex justify-content-center mt-2" action="{{ route('comment.store') }}" method="POST">
                    @csrf
                    <textarea style="resize:none" id="content" name="content" cols="30" rows="1" placeholder="Adicione um comentário..."></textarea>
                    <input type="hidden" name="post_id" value="{{ $post->post_id }}">
                    <input type="hidden" name="comment_id" value="{{ $comment->comment_id }}">
                    <button type="submit" class="btn btn-primary ms-2">Comment</button>
                </form>
            </div>
            <div style="gap:10px;" class="d-flex align-items-center ms-2">
                <p class="m-0 comment-date">6d</p>
                <p class="m-0 comment-likes">{{$comment->likeCounts()}} likes</p>
                <button  class="btn btn-primary btn-sm" onclick="reply({{ $comment->comment_id }})">Reply</button>
            </div>
        @endcan
    </div>
@endif

@foreach ($comment->replies()->orderBy('created_at', 'desc')->get() as $reply)
    <div style="margin: 0 0 0.5rem 3rem;" class="d-flex flex-column">
        <div class="d-flex justify-content-between">
            <div style="margin-left:10px; display:flex; align-items: center;">
                <div>
                    <img src="{{ asset('images/profile_pics/' . $reply->user->avatar) }}" class="rounded-circle" alt="User avatar" style="width: 20px; height: 20px;">
                    <strong style="margin:0; font-size:1.1rem;" class="card-text">{{ $reply->user->username }}&nbsp</strong>
                </div>
                <p class="card-text">{{ $reply->content }}</p>
            </div>
            <div class="buttons d-flex">
                @can('delete', $reply)
                    <form action="{{ route('comment.delete', ['id' => $reply->comment_id]) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn"
                        onclick="return confirm('Are you sure you want to delete this comment?')"><i style="color: red"
                        class="bi bi-trash-fill"></i></button>
                        <input type="hidden" name="comment_id" value="{{ $reply->comment_id }}">
                    </form>
                @endcan
                @if ($reply->isLiked() == true)
                    <form action="{{ route('comment.dislike', ['id' => $reply->comment_id]) }}" method="POST">
                        @csrf
                        <button class="btn">
                            <i style="color: red" class="bi bi-heart-fill"></i>
                        </button>
                    </form>
                @else
                    <form action="{{ route('comment.like', ['id' => $reply->comment_id]) }}" method="POST">
                        @csrf
                        <button class="btn">
                            <i style="color: red" class="bi bi-heart"></i>
                        </button>
                    </form>
                @endif
            </div>
        </div>
        <div style="gap:10px;" class="d-flex align-items-center ms-2">
            <p class="m-0 comment-date">6d</p>
            <p class="m-0 comment-likes"> {{$reply->likeCounts()}} likes</p>
        </div>
    </div>
@endforeach

<script type="text/javascript">
    function reply(commentId)
    {
        let rep = document.getElementById('replyDiv' + commentId);

        if (rep.style.display === "none") {
            rep.style.display = "block";
        } else {
            rep.style.display = "none";
        }
    }
</script>
