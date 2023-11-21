@section('sidebar')
    @include('partials.sidebar')
@endsection

@section('profile')

<section id="profile" class="flex-grow-1" style="margin-left: 280px;">
    <section id="profile-details" class="d-flex justify-content-center">
        <div class="profile-details">
            <!-- <img> User profile pic here </img> -->
            <div class="profile-details-text border-black mb-4">
                <h1 class="display-4">{{ $user->name }}</h1>
                <h3 class="text-secondary"><span>@</span>{{ $user->username }}</h3>
                @if ($user->private)
                    <div class="alert alert-warning" role="alert">
                        <strong>Private Profile</strong>
                    </div>
                @else
                    <div class="alert alert-success" role="alert">
                        <strong>Public Profile</strong>
                    </div>
                @endif
                <div class="card mt-3">
                    <div class="card-body">
                        <h5 class="card-title">Biography</h5>
                        <p class="card-text">{{ $user->bio }}</p>
                    </div>
                </div>
            </div>
            <div class="profile-details-counts mt-3 d-flex justify-content-between">
                <div class="text-center">
                    <p class="mb-0"><strong>{{ $user->followerCounts() }}</strong></p>
                    <p class="mb-0" style="font-size: 0.8rem;">Followers</p>
                </div>
                <div class="text-center">
                    <p class="mb-0"><strong>{{ $user->followingCounts() }}</strong></p>
                    <p class="mb-0" style="font-size: 0.8rem;">Following</p>
                </div>
                <div class="text-center">
                    <p class="mb-0"><strong>{{ $user->postCounts() }}</strong></p>
                    <p class="mb-0" style="font-size: 0.8rem;">Posts</p>
                </div>
                @if (Auth::check() && Auth::user()->user_id != $user->user_id)
                    @if ($user->followStatus() === 'accepted')
                        {{-- Show Unfollow button --}}
                        <form action="{{ route('user.unfollow', ['id' => $user->user_id]) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-danger">Unfollow</button>
                        </form>
                    @elseif ($user->followStatus() === 'waiting')
                        {{-- Show a message indicating that the request is pending --}}
                        <span class="text-muted">Follow request pending</span>
                    @elseif ($user->followStatus() === 'rejected')
                        {{-- Show a message indicating that the request was rejected --}}
                        <span class="text-danger">Follow request rejected</span>
                    @else
                        {{-- Show Follow button --}}
                        <form action="{{ route('user.follow', ['id' => $user->user_id]) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary">Follow</button>
                        </form>
                    @endif
                @endif
            </div>
        </div>
    </section>
    <section id="profile-feed" class="mt-4 text-center">
    <hr/>
    <h2 class="mb-4">Posts from this user</h2>
    @if ((Auth::check() && Auth::user()->user_id == $user->user_id) || !$user->private || (Auth::Check() && Auth::user()->user_type == 'admin') || (Auth::check() && $user->isFollowing()))
        @if ($user->posts()->count() > 0)
        <div class="container-lg">
            <ul class="list-unstyled d-inline-block">
                @foreach ($user->posts()->orderBy('date', 'desc')->get() as $post)
                <div class="post mb-4 d-inline-block align-top">
                    <div class="card" style="width: 50rem;">
                        <div class="card-header d-flex justify-content-between">
                            <small class="text-black">{{ $user->name }}</small>
                            <small class="text-muted"><span class="text-muted">@</span>{{ $user->username }}</small>
                            <small class="text-black">{{ $post->date }}</small>
                        </div>
                        <div class="card-body">
                            <p class="card-text">{{ $post->content }}</p>
                            <p class="card-text">{{ $post->image }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </ul>
            @else
            <div class="alert alert-info mt-4 mb-4" role="alert">
                <h4 class="alert-heading">No posts yet!</h4>
                <p>When this user posts something, it will appear here.</p>
            </div>
            @endif
            @else
            <div class="alert alert-info mt-4 mb-4" role="alert">
                <h4 class="alert-heading">This user's profile is set to private!</h4>
                <p>Follow this user to see their posts.</p>
            </div>
            @endif
        </div>
    </section>
</section>

@endsection