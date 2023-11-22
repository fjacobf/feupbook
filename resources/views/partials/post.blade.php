<div class="post mb-4">
    <div class="card" style="width: 50em;">
        <div class="card-header d-flex justify-content-between fs-5">
            <small><a href="{{ route('user.profile', ['id' => $post->user->user_id]) }}" class="link-primary:hover">{{$post->user->name}}</a></small>
            <small class="text-muted"><span class="text-muted">@</span>{{ $post->user->username }}</small>
            <small class="text-black">{{ \Carbon\Carbon::parse($post->created_at)->format('H:i d-m-y') }}</small>
        </div>
        <div class="card-body">
            <p class="card-text">{{ $post->content }}</p>
            <p class="card-text">{{ $post->image }}</p>
        </div>
    </div>
</div>
