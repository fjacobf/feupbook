@if ($comment->previous == null)
    <div>
        <div style="display: flex; justify-content: space-between">
            <h5 class="card-text">{{ $comment->user->username }}</h5>
            <p>{{ $comment->date }}</p>
        </div>
        <div style="display:flex; justify-content: space-between">
            <p class="card-text">{{ $comment->content }}</p>
            @can('delete', $comment)
                <form action="{{ route('deleteComment', ['id' => $comment->comment_id]) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this comment?')">Delete Comment</button>
                    <input type="hidden" name="comment_id" id="comment_id_{{ $comment->comment_id }}" value="{{ $comment->comment_id }}">
                </form>
            @endcan
        </div>
        @can('create', [App\Models\Comment::class, $post])
            <div style="display:none;" id="replyDiv{{ $comment->comment_id }}">
                <form style="display:flex; justify-content: center" action="{{ route('storeComment') }}" method="POST">
                    @csrf
                    <textarea style="resize:none" id="content" name="content" cols="30" rows="1" placeholder="Adicione um comentário..."></textarea>
                    <input type="hidden" name="post_id" value="{{ $post->post_id }}">
                    <input type="hidden" name="comment_id" value="{{ $comment->comment_id }}">
                    <button type="submit" class="btn btn-primary">Comment</button>
                </form>
            </div>
            <button onclick="reply({{ $comment->comment_id }})">Reply</button>
            <hr>
        @endcan
    </div>
@endif

@foreach ($comment->replies as $reply)
    <div style="margin-left: 3rem">
        <div style="display: flex; justify-content: space-between">
            <h5 class="card-text">{{ $reply->user->username }}</h5>
            <p>{{ $reply->date }}</p>
        </div>
        <div style="display: flex; justify-content: space-between">
            <p class="card-text">{{ $reply->content }}</p>
            @can('delete', $reply)
                <form action="{{ route('deleteComment', ['id' => $reply->comment_id]) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this comment?')">Delete Reply</button>
                    <input type="hidden" name="comment_id" value="{{ $reply->comment_id }}">
                </form>
            @endcan
        </div>
        <hr>
    </div>
@endforeach

<script type="text/javascript">
    function reply(commentId)
    {
        let rep = document.getElementById('replyDiv' + commentId);
        console.log(rep);
        rep.style.display = "block";
    }
</script>
