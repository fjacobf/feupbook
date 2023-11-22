<div class="card bg-light mb-3" style="width: 100%;">
    <div class="card-header">
        <h4 class="card-title">{{ $post->user ? $post->user->username : 'Unknown User' }}</h4>
        <p class="card-text">{{ $post->date }}</p>
    </div>
    <div class="card-body">
        <p class="card-text">{{ $post->content }}</p><br><br>
        <p class="card-text">{{ $post->image }}</p>
        <h4 class="card-text">Comments:</h4>
        @forelse($post->comments as $comment)
            <div>
              <h5 class="card-text">{{$comment->user->username}}</h5>
              <p class="card-text">{{$comment->content}}</p>
            </div>
        @empty
            <div class="alert alert-info" role="alert">
                There are no comments on this post.
            </div>
        @endforelse
    </div>
    <div>
    </div>
</div>
