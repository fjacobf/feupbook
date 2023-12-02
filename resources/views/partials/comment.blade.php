@if ($comment->previous == null)
    <div class="d-flex flex-column ms-3">
        <div class="d-flex justify-content-between">
            <div class="d-flex align-items-center">
                <strong style="margin:0; font-size:1.1rem;" class="card-text">{{ $comment->user->username }}&nbsp</strong>
                <p class="card-text">{{ $comment->content }}</p>
            </div>
            <div class="buttons d-flex">
                @if (Auth::id() == $comment->author_id)
                    <form action="{{ route('deleteComment', ['id' => $comment->comment_id]) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn"
                            onclick="return confirm('Are you sure you want to delete this comment?')">
                            <i style="color: red" class="bi bi-trash-fill"></i>
                        </button>
                        <input type="hidden" name="comment_id" id="comment_id_{{ $comment->comment_id }}"
                            value="{{ $comment->comment_id }}">
                    </form>
                @endif
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
        <div style="margin: 1rem; display:none;" id="replyDiv{{ $comment->comment_id }}">
            <form style="display:flex; justify-content: center" action="{{ route('storeComment') }}" method="POST">
                @csrf
                <textarea style="resize:none" id="content" name="content" cols="30" rows="1"
                    placeholder="Adicione um comentário..."></textarea>
                <input type="hidden" name="post_id" value="{{ $post->post_id }}">
                <input type="hidden" name="comment_id" value="{{ $comment->comment_id }}">
                <button type="submit" class="btn btn-primary">Comment</button>
            </form>
        </div>
        <div style="gap:10px;" class="d-flex align-items-center ms-2">
            <p class="m-0 comment-date">6d</p>
            <p class="m-0 comment-likes">{{ $comment->likeCounts() }} likes</p>
            <button style="text-decoration: underline; width:4rem; padding:0;" class="btn"
                onclick="reply({{ $comment->comment_id }})">Reply</button>
        </div>
    </div>
@endif

{{-- REPLIES --}}

@foreach ($comment->replies as $reply)
    <div style="margin: 0 0 0.5rem 3rem;" class="d-flex flex-column">
        <div class="d-flex justify-content-between">
            <div style="margin-left:10px; display:flex; align-items: center;">
                <strong style="margin:0; font-size:1.1rem;"
                    class="card-text">{{ $reply->user->username }}&nbsp</strong>
                <p class="card-text">{{ $reply->content }}</p>
            </div>
            <div class="buttons d-flex">
                @if (Auth::id() == $reply->author_id)
                    <form action="{{ route('deleteComment', ['id' => $reply->comment_id]) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn"
                            onclick="return confirm('Are you sure you want to delete this comment?')"><i
                                style="color: red" class="bi bi-trash-fill"></i></button>
                        <input type="hidden" name="comment_id" value="{{ $reply->comment_id }}">
                    </form>
                @endif
                @if ($comment->isLiked() == true)
                    <button class="btn">
                        <i style="color: red" class="bi bi-heart-fill"></i>
                    </button>
                @else
                    <button class="btn">
                        <i style="color: red" class="bi bi-heart"></i>
                    </button>
                @endif
            </div>

        </div>
        <div style="gap:10px;" class="d-flex align-items-center ms-2">
            <p class="m-0 comment-date">6d</p>
            <p class="m-0 comment-likes">3 likes</p>
        </div>
    </div>
@endforeach

{{-- FUNCTION TO SHOW REPLY COMMENT FORM --}}
<script type="text/javascript">
    function reply(commentId) {
        let rep = document.getElementById('replyDiv' + commentId);
        if (rep.style.display === "none") {
            rep.style.display = "block";
        } else {
            rep.style.display = "none";
        }

    }
</script>
