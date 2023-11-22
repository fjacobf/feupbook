@if ($comment->previous == null)
    <div>
        <div style="display: flex; justify-content: space-between">
            <h5 class="card-text">{{ $comment->user->username }}</h5>
            <p>{{ $comment->date }}</p>
        </div>
        <p class="card-text">{{ $comment->content }}</p>
        <button class="btn btn-secondary">Reply</button>
        @if (Auth::id() == $comment->author_id)
            <form action="{{ route('deleteComment') }}" method="GET">
                @csrf
                <button type="submit" class="btn btn-danger">Delete</button>
                <input type="hidden" name="comment_id" id="post_id" value="{{ $comment->comment_id }}">
            </form>
        @endif
        <hr>
    </div>
@endif
@foreach ($comment->replies as $reply)
    <div style="margin-left: 3rem">
        <div style="display: flex; justify-content: space-between">
            <h5 class="card-text">{{ $reply->user->username }}</h5>
            <p>{{ $reply->date }}</p>
        </div>
        <p class="card-text">{{ $reply->content }}</p>
        <button class="btn btn-secondary">Reply</button>
        @if (Auth::id() == $reply->author_id)
            <form action="{{ route('deleteComment') }}" method="GET">
                @csrf
                <button type="submit" class="btn btn-danger">Delete</button>
                <input type="hidden" name="comment_id" id="post_id" value="{{ $reply->comment_id }}">
            </form>
        @endif
        <hr>
    </div>
@endforeach
