@if ($comment->previous == null)
    <div>
        <div class="d-flex justify-content-between">
            <h5 class="card-text">{{ $comment->user->username }}</h5>
            <p>{{ $comment->date }}</p>
        </div>
        <div class="d-flex justify-content-between">
            <p class="card-text">{{ $comment->content }}</p>
            <div class="d-flex justify-content-end">
                <button class="btn btn-primary" onclick="reply({{ $comment->comment_id }})">Reply</button>
                @can('delete', $comment)
                    <form action="{{ route('comment.delete', ['id' => $comment->comment_id]) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger ms-2" onclick="return confirm('Are you sure you want to delete this comment?')">Delete Comment</button>
                        <input type="hidden" name="comment_id" id="comment_id_{{ $comment->comment_id }}" value="{{ $comment->comment_id }}">
                    </form>
                @endcan
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
                <form action="{{ route('comment.delete', ['id' => $reply->comment_id]) }}" method="POST">
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
