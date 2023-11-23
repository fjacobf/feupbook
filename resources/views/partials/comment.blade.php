
@if ($comment->previous == null)
    <div>
        <div style="display: flex; justify-content: space-between">
            <h5 class="card-text">{{ $comment->user->username }}</h5>
            <p>{{ $comment->date }}</p>
        </div>
        <div style="display:flex; justify-content: space-between">
            <p class="card-text">{{ $comment->content }}</p>
            @if (Auth::id() == $comment->author_id)
                <form action="{{ route('deleteComment') }}" method="GET">
                    @csrf
                    <button type="submit" class="btn btn-danger">Delete</button>
                    <input type="hidden" name="comment_id" id="post_id" value="{{ $comment->comment_id }}">
                </form>
            @endif
        </div>
        <div style="display:none;" id="replyDiv">
            <form style="display:flex; justify-content: center" action="{{ route('storeComment') }}" method="POST">
                @csrf
                <textarea style="resize:none" id="content" name="content" cols="30" rows="1"
                    placeholder="Adicione um comentário..."></textarea> <!--textarea not auto expanding-->
                <input type="hidden" name="post_id" id="post_id" value="{{ $post->post_id }}">
                <input type="hidden" name="comment_id" id="comment_id" value="{{ $comment->comment_id }}">
                <button type="submit" class="btn btn-primary">Post</button>
            </form>
        </div>
        <button onclick="reply()">Reply</button>
        <hr>
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
            @if (Auth::id() == $reply->author_id)
                <form action="{{ route('deleteComment') }}" method="GET">
                    @csrf
                    <button type="submit" class="btn btn-danger">Delete</button>
                    <input type="hidden" name="comment_id" id="post_id" value="{{ $reply->comment_id }}">
                </form>
            @endif
        </div>
        <hr>
    </div>
@endforeach



<script type="text/javascript">
    function reply()
    {
        let rep = document.getElementById('replyDiv');
        console.log(rep);
        // rep.afer(caller);
        rep.style.display = "block";
    }
</script>
