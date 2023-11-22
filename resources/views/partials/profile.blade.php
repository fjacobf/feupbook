@section('profile')

<section id="profile">
    <section id="profile-details">
        <div class="profile-details">
            <!-- <img> User profile pic here </img> -->
            <div class="profile-details-text">
                <h1> {{ $user->name }} </h1>
                @if ($user->private) <h3 class="text-muted">Private Profile</h3>
                @else <h3 class="text-muted">Public Profile</h3>
                @endif
                <h3 class="text-secondary"><span class="text-secondary">@</span>{{ $user->username }}</h3>
                <div class="bio-rectangle p-2 border border-dark">
                    <p class="mb-0"> {{ $user->bio }} </p>
                </div>
            </div>
            <div class="profile-details-counts mt-3">
                <p><strong>{{ $user->followerCounts() }} Followers</strong></p>
                <p><strong>{{ $user->followingCounts() }} Following</strong></p>
                <p><strong>{{ $user->postCounts() }} Posts</strong></p>
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
    <section id="profile-feed">
        <h2>Posts from this user</h2>

        {{-- print out isFollowing --}}
        <p>isFollowing: {{ $user->isFollowing() ? 'true' : 'false' }}</p>

        @if ((Auth::check() && Auth::user()->user_id == $user->user_id) || !$user->private || (Auth::Check() && Auth::user()->user_type == 'admin')
        || (Auth::check() && $user->isFollowing()))
            @if ($user->posts()->count() > 0)
            <ul>
                @foreach ($user->posts()->orderBy('created_at', 'desc')->get() as $post)
                <div class="post col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <small class="text-black">{{ $user->name }}</small>
                            <small class="text-muted"><span class="text-muted">@</span>{{ $user->username }}</small>
                            <small class="text-black">{{ $post->created_at }}</small>
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
            <div class="alert alert-info" role="alert">
                <h4 class="alert-heading">No posts yet!</h4>
                <p>When this user posts something, it will appear here.</p>
            </div>
            @endif
        @else
        <div class="alert alert-info" role="alert">
            <h4 class="alert-heading">This user's profile is set to private!</h4>
            <p>Follow this user to see their posts.</p>
        </div>
        @endif
    </section>
</section>

@endsection