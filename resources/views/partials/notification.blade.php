@if($notifications->isEmpty())
    <p>No notifications found.</p>
@else
    <div class="notifications d-flex flex-column align-items-center w-100">
        @foreach($notifications as $notification)
            <div class="card mb-4 w-100" style="">
                @if ($notification->notification_type == 'request_follow')
                    @php
                        $follow_request = $notification->notif_user->followRequestsRcv->where('req_id', $notification->user_id)->first();
                    @endphp
                    @if ($follow_request->status == "waiting")
                        <div class="d-flex justify-content-between align-items-center card-body" >
                            <div class="d-flex align-items-center">
                                <img src="{{ asset('images/profile_pics/' . $notification->request_user->avatar) }}" class="rounded-circle" style="width: 30px; height: 30px; margin: 10px">
                                <a href="{{ route('user.profile', ['id' => $notification->user_id]) }}" class="text-decoration-none text-black">
                                    <p class="card-text">{{ $notification->message }} <span class="card-text text-secondary">{{ time_since($notification->date)}}</span></p>
                                </a>
                            </div>
                            
                            <div class="d-flex align-items-center buttons">
                                <form class="" action="{{ route('follow-request.Accept.api', $notification->user_id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-success ">Accept</button>
                                </form>
                                <form action="{{ route('follow-request.Reject.api', $notification->user_id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-danger">Reject</button>
                                </form>
                            </div>
                        </div>
                    @elseif ($follow_request->status == "accepted")
                        <a href="{{ route('user.profile', ['id' => $notification->user_id]) }}" class="text-decoration-none text-black">
                            <div class="d-flex align-items-center card-body">
                                <img src="{{ asset('images/profile_pics/' . $notification->request_user->avatar) }}" class="rounded-circle" style="width: 30px; height: 30px; margin: 10px">
                                <h5 class="card-text">You accepted {{$notification->request_user->username}} follow request <span class="card-text text-secondary">{{ time_since($notification->date)}}</span></h5>
                            </div> 
                        </a>
                    @else {{--$follow_request->status == "rejected"--}}
                        <a href="{{ route('user.profile', ['id' => $notification->user_id]) }}" class="text-decoration-none text-black">
                            <div class="d-flex align-items-center card-body">
                                <img src="{{ asset('images/profile_pics/' . $notification->request_user->avatar) }}" class="rounded-circle" style="width: 30px; height: 30px; margin: 10px">
                                <h5 class="card-text">You rejected {{$notification->request_user->username}} follow request <span class="card-text text-secondary">{{ time_since($notification->date)}}</span></h5>
                            </div>
                        </a>
                    @endif
                @elseif ($notification->notification_type == 'group_invite')
                    @php
                        $group = $notification->group;
                    @endphp
                    @if ($group->memberStatus($notification->notif_user) == "waiting")
                        <div class="d-flex align-items-center justify-content-between card-body">
                                <a href="{{ route('group-chats.show', ['groupChat' => $notification->group_id]) }}" class="text-decoration-none text-black ms-3">
                                    <h5 class="card-text">{{ $notification->message }} <span class="card-text text-secondary">{{ time_since($notification->date)}}</span></h5>
                                </a>
                            <div class="d-flex align-items-center buttons">
                                <form action="{{ route('group-chats.acceptInvite.api', $group->group_id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-success">Accept</button>
                                </form>
                                <form action="{{ route('group-chats.rejectInvite.api', $group->group_id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-danger">Reject</button>
                                </form>
                            </div>
                        </div>
                    @elseif ($group->memberStatus($notification->notif_user) == "accepted")
                    <a href="{{ route('group-chats.show', ['groupChat' => $notification->group_id]) }}" class="text-decoration-none text-black ms-3">
                        <div class="card-body">
                            <h5 class="card-text">You accepted {{$group->name}} join request <span class="card-text text-secondary">{{ time_since($notification->date)}}</span></h5>
                        </div>
                    </a>
                    @else
                    <div class="card-body">
                        <h5 class="card-text">You rejected {{$group->name}} join request <span class="card-text text-secondary">{{ time_since($notification->date)}}</span></h5>
                    </div>
                    @endif
                @else
                    @if ($notification->notification_type == 'liked_post' or $notification->notification_type == 'comment_post')
                        <a href="{{ route('post.show', ['id' => $notification->post_id]) }}" class="text-decoration-none text-black">
                            <div class="d-flex align-items-center card-body">
                                <img src="{{ asset('images/profile_pics/' . $notification->request_user->avatar) }}" class="rounded-circle" style="width: 30px; height: 30px; margin: 10px">
                    
                    @elseif($notification->notification_type == 'liked_comment' or $notification->notification_type == 'reply_comment')
                        <a href="{{ route('post.show', ['id' => $notification->comment->post_id]) }}" class="text-decoration-none text-black">
                            <div class="d-flex align-items-center card-body">
                                <img src="{{ asset('images/profile_pics/' . $notification->request_user->avatar) }}" class="rounded-circle" style="width: 30px; height: 30px; margin: 10px">
        
                    @elseif ($notification->notification_type == 'started_following' or $notification->notification_type == 'accepted_follow')
                        <a href="{{ route('user.profile', ['id' => $notification->user_id]) }}" class="text-decoration-none text-black">
                            <div class="d-flex align-items-center card-body">
                                <img src="{{ asset('images/profile_pics/' . $notification->request_user->avatar) }}" class="rounded-circle" style="width: 30px; height: 30px; margin: 10px">
            
                    @elseif ($notification->notification_type == 'joined_group')
                        <a href="{{ route('group-chats.show', ['groupChat' => $notification->group_id]) }}" class="text-decoration-none text-black">
                            <div class="d-flex align-items-center card-body">
                                <img src="{{ asset('images/profile_pics/' . $notification->request_user->avatar) }}" class="rounded-circle" style="width: 30px; height: 30px; margin: 10px">
                    
                    @else
                        <a href="#" class="text-decoration-none text-black">
                            <div class="card-body">
                
                    @endif
                        <h5 class="card-text">{{ $notification->message }} <span class="card-text text-secondary">{{ time_since($notification->date)}}</span></h5>
                    </div>
                        </a>
                @endif
            </div>
        @endforeach
    </div>
@endif

<style>
    .card-text{
        font-size: 1rem;
    }
    
    @media (max-width: 767px) {
      .card-text {
        font-size: 1rem;
        }
    }
</style>