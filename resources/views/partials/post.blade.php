<div class="card bg-light mb-3" style="width: 100%;">
    <div class="card-header">
        <h4 class="card-title">{{ $post->user ? $post->user->username : 'Unknown User' }}</h4>
      <!-- Format the date -->
        <p class="card-text">{{ \Carbon\Carbon::parse( $post->created_at)->format('H:i d-m-y')  }}</p>
    </div>
    <div class="card-body">
        <p class="card-text">{{ $post->content }}</p><br><br>
  <!-- code to display post images when implemented -->
        <!-- @if($post->image)
      <img src="{{ asset('storage/images/' .  $post->image ) }}" alt="Post Image">
    @endif -->
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
