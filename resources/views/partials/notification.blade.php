@if($notifications->isEmpty())
    <p>No notifications found.</p>
@else
    <div class="d-flex flex-column align-items-center w-100">
        @foreach($notifications as $notification)
            <div class="card mb-4 w-100" style="max-width: 800px;">
                @if ($notification->notification_type == 'request_follow')
                    <div class="card-body">
                        <h5 class="card-title">{{ $notification->message }}</h5>
                        <p class="card-text text-secondary">{{ $notification->date}}</span>
                        <button class="btn bi bi-check-circle"></button>
                        <button class="btn bi bi-x-circle"></button>
                    </div>
                @else
                    @if ($notification->notification_type == 'liked_post' or $notification->notification_type == 'comment_post')
                        <a href="{{ route('post.show', ['id' => $notification->post_id]) }}" class="text-decoration-none text-black">
                    @elseif($notification->notification_type == 'liked_comment' or $notification->notification_type == 'reply_comment')
                        <a href="{{ route('post.show', ['id' => $notification->comment->post_id]) }}" class="text-decoration-none text-black">
                    @elseif ($notification->notification_type == 'started_following' or $notification->notification_type == 'accepted_follow')
                        <a href="{{ route('user.profile', ['id' => $notification->user_id]) }}" class="text-decoration-none text-black">
                    @elseif ($notification->notification_type == 'joined_group' or $notification->notification_type == 'group_invite')
                        <a href="{{ route('group-chats.show', ['groupChat' => $notification->group_id]) }}" class="text-decoration-none text-black">
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