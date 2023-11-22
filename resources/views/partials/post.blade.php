<div class="card bg-light mb-3" style="width: 100%;">
    <div class="card-header">
        <h4 class="card-title">{{ $post->user ? $post->user->username : 'Unknown User' }}</h4>
        <!-- Format the date -->
        <p class="card-text">{{ \Carbon\Carbon::parse($post->created_at)->format('H:i d-m-y') }}</p>
    </div>
    <div class="card-body">
        <p class="card-text">{{ $post->content }}</p><br><br>
        <!-- code to display post images when implemented -->
        <!-- @if ($post->image)
<img src="{{ asset('storage/images/' . $post->image) }}" alt="Post Image">
@endif -->
        <h4 class="card-text">Comments:</h4>
        @forelse($post->comments as $comment)
          @include('partials.comment', ['comment' => $comment])
        @empty
            <p style="color: gray; font-size: 0.8rem">There are no comments on this post.</p>
        @endforelse

        <form style="display:flex; justify-content: center" action="{{ route('storeComment') }}" method="POST">
            @csrf
            <textarea style="resize:none" id="content" name="content" cols="30" rows="1"
                placeholder="Adicionei um comentário..."></textarea> <!--textarea not auto expanding-->
            <input type="hidden" name="post_id" id="post_id" value="{{ $post->post_id }}">
            <button type="submit" class="btn btn-primary">Post</button>
        </form>
    </div>
    <div>
    </div>
</div>
