@if($notifications->isEmpty())
    <p>No notifications found.</p>
@else
    <div class="card-columns">
        @foreach($notifications as $notification)
            <div class="card mb-4" style="max-width: 600px;">
                @if ($notification->notification_type == 'request_follow')
                    <div class="card-body">
                        <h5 class="card-title">{{ $notification->message }}</h5>
                        <p class="card-text text-secondary">{{ $notification->date}}</span>
                        <button class="btn bi bi-check-circle"></button>
                        <button class="btn bi bi-x-circle"></button>
                    </div>
                @else
                    @if ($notification->notification_type == 'liked_post' or $notification->notification_type == 'comment_post')
                        <a href="{{ route('showPost', ['id' => $notification->post_id]) }}" class="text-decoration-none text-black">
                    @elseif($notification->notification_type == 'liked_comment' or $notification->notification_type == 'reply_comment')
                        <a href="{{ route('showPost', ['id' => $notification->comment->post_id]) }}" class="text-decoration-none text-black">
                    @elseif ($notification->notification_type == 'started_following')
                        <a href="{{ route('user.profile', ['id' => $notification->user_id]) }}" class="text-decoration-none text-black">
                    @else
                    <a href="#" class="text-decoration-none text-black">
                    @endif
                            <div class="card-body">
                                <h5 class="card-title">{{ $notification->message }}</h5>
                                <p class="card-text text-secondary">{{ $notification->date}}</span>
                            </div>
                        </a>
                @endif

                <div class="card-body">
                </div>
            </div>
        @endforeach
    </div>
@endif