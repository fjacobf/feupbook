@if($notifications->isEmpty())
    <p>No notifications found.</p>
@else
    <div class="card-columns">
        @foreach($notifications as $notification)
            <div class="card mb-4" style="max-width: 600px;">
                @if ($notification->notification_type == 'request_follow')
                    @php
                        $follow_request = $notification->notif_user->followRequestsRcv->where('req_id', $notification->user_id)->first();
                    @endphp
                    @if ($follow_request->status == "waiting")
                        <div class="card-body">
                            <a href="{{ route('user.profile', ['id' => $notification->user_id]) }}" class="text-decoration-none text-black">
                                <h5 class="card-title">{{ $notification->message }}</h5>
                            </a>
                            <p class="card-text text-secondary">{{ $notification->date}}</span>
                            <form action="{{ route('follow-request.Accept.api', $notification->user_id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success">Accept Follow</button>
                            </form>
                            <form action="{{ route('follow-request.Reject.api', $notification->user_id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-danger">Reject Follow</button>
                            </form>
                        </div>
                    @elseif ($follow_request->status == "accepted")
                        <a href="{{ route('user.profile', ['id' => $notification->user_id]) }}" class="text-decoration-none text-black">
                            <div class="card-body">
                                <h5 class="card-title">You accepted {{$notification->request_user->username}} follow request</h5>
                                <p class="card-text text-secondary">{{ $notification->date}}</span>
                            </div> 
                        </a>
                    @else {{--$follow_request->status == "rejected"--}}
                        <a href="{{ route('user.profile', ['id' => $notification->user_id]) }}" class="text-decoration-none text-black">
                            <div class="card-body">
                                <h5 class="card-title">You rejected {{$notification->request_user->username}} follow request</h5>
                                <p class="card-text text-secondary">{{ $notification->date}}</span>
                            </div>
                        </a>
                    @endif
                @elseif ($notification->notification_type == 'group_invite')
                    @php
                        $group = $notification->group;
                    @endphp
                    @if ($group->memberStatus($notification->notif_user) == "waiting")
                        <div class="card-body">
                            <a href="{{ route('group-chats.show', ['groupChat' => $notification->group_id]) }}" class="text-decoration-none text-black">
                                <h5 class="card-title">{{ $notification->message }}</h5>
                            </a>
                            <p class="card-text text-secondary">{{ $notification->date}}</span>
                            <form action="{{ route('group-chats.acceptInvite.api', $group->group_id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success">Accept Invite</button>
                            </form>
                            <form action="{{ route('group-chats.rejectInvite.api', $group->group_id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-danger">Reject Invite</button>
                            </form>
                        </div>
                    @elseif ($group->memberStatus($notification->notif_user) == "accepted")
                        <div class="card-body">
                            <h5 class="card-title">You accepted {{$group->name}} join request</h5>
                            <p class="card-text text-secondary">{{ $notification->date}}</span>
                        </div>
                    @else
                        <div class="card-body">
                            <h5 class="card-title">You rejected {{$group->name}} join request</h5>
                            <p class="card-text text-secondary">{{ $notification->date}}</span>
                        </div>
                    @endif
                @else
                    @if ($notification->notification_type == 'liked_post' or $notification->notification_type == 'comment_post')
                        <a href="{{ route('post.show', ['id' => $notification->post_id]) }}" class="text-decoration-none text-black">
                    @elseif($notification->notification_type == 'liked_comment' or $notification->notification_type == 'reply_comment')
                        <a href="{{ route('post.show', ['id' => $notification->comment->post_id]) }}" class="text-decoration-none text-black">
                    @elseif ($notification->notification_type == 'started_following' or $notification->notification_type == 'accepted_follow')
                        <a href="{{ route('user.profile', ['id' => $notification->user_id]) }}" class="text-decoration-none text-black">
                    @elseif ($notification->notification_type == 'joined_group')
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

