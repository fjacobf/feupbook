@section('sidebar')
    @include('partials.sidebar')
@endsection

@section('profile')

<section id="profile" class="col overflow-auto">
    <section id="profile-details" class="d-flex flex-column align-items-center w-100">
        <div class="profile-details w-75" style="max-width: 800px;">
            <div class="profile-details-text border-black mb-4">
                <div class="d-flex justify-content-center">
                    <div class="d-flex flex-column">
                        <h1 class="display-4">{{ $user->name }}</h1>
                        <h3 class="text-secondary"><span>@</span>{{ $user->username }}</h3>
                    </div>
                    <div class="profile-picture mt-4">
                        <img src="{{ asset('images/profile_pics/' . $user->avatar) }}" class="rounded-circle profile-pic-border" style="width: 150px; height: 150px;">
                    </div>
                </div>
                <div class="d-flex" style="margin-bottom: 10px;">
                        @can('updateSelf', $user)
                        <a href="{{ route('user.showEditPage', ['id' => $user->user_id])}}" class="btn btn-primary" style="margin-right: 5px;">Edit Profile</a>
                        @endcan
                        
                        @can('viewAdminInterface', $user)
                        <a href="{{ route('admin.manageUser', ['id' => $user->user_id]) }}" class="btn btn-danger">Manage User's Account</a>
                        @endcan
                </div>
                @if ($user->private)
                <div class="d-flex justify-content-center alert alert-warning" role="alert">
                    <strong>Private Profile</strong>
                </div>
                @elseif ($user->user_type === 'deleted')
                <div class="d-flex justify-content-center alert alert-danger" role="alert">
                    <strong>This Account is Deleted!</strong>
                </div>
                @elseif ($user->user_type === 'suspended')
                <div class="d-flex justify-content-center alert alert-danger" role="alert">
                    <strong>This Account is Suspended!</strong>
                </div>
                @else
                <div class="d-flex justify-content-center alert alert-success" role="alert">
                    <strong>Public Profile</strong>
                </div>
                @endif
                @if ($user->user_type !== 'deleted')
                <div class="card mt-3">
                    <div class="card-body">
                        <h5 class="card-title">Biography</h5>
                        <p class="card-text">{{ $user->bio }}</p>
                    </div>
                </div>
                <div class="profile-details-counts mt-3 d-flex justify-content-between">
                    <div class="text-center">
                        <p class="mb-0"><strong>{{ $user->followerCounts() }}</strong></p>
                        @can('viewFollowPages', $user)
                        <a href="{{ route('user.followers', ['id' => $user->user_id ])}}" class="mb-0 text-info text-decoration-none" style="font-size: 0.8rem;">Followers</a>
                        @else
                        <p class="mb-0" style="font-size: 0.8rem;">Followers</p>
                        @endcan
                    </div>
                    <div class="text-center">
                        <p class="mb-0"><strong>{{ $user->followingCounts() }}</strong></p>
                        @can('viewFollowPages', $user)
                        <a href="{{ route('user.following', ['id' => $user->user_id ])}}" class="mb-0 text-info text-decoration-none" style="font-size: 0.8rem;">Following</a>
                        @else
                        <p class="mb-0" style="font-size: 0.8rem;">Following</p>
                        @endcan
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
                    {{-- Show a muted, non-clickable button indicating that the request is pending --}}
                    <button class="btn btn-light" type="button" disabled>Request pending</button>
                    @elseif ($user->followStatus() === 'rejected')
                    {{-- Show a red, non-clickable button indicating that the request was rejected --}}
                    <button class="btn btn-danger" type="button" disabled>Request rejected</button>
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
            @endif
        </section>
        <section id="profile-feed" class="mt-4">
            <hr/>
            <h2 class="mb-2 text-center">Posts from this user</h2>
            <div class="container-lg d-flex justify-content-center align-items-center w-75" style="max-width: 800px">
                @if ((Auth::check() && Auth::user()->user_id == $user->user_id) || !$user->private || (Auth::Check() && Auth::user()->user_type == 'admin') || (Auth::check() && $user->isFollowing()))
                @if ($user->user_type === 'deleted')
                    <div class="alert alert-danger mt-4 mb-4 w-75" style="max-width: 800px">
                        <h4 class="alert-danger">This account was deleted!</h4>
                        <p>Can't show posts from deleted accounts.</p>
                    </div>
                @elseif ($user->user_type === 'suspended')
                    <div class="alert alert-danger mt-4 mb-4 w-75" style="max-width: 800px">
                        <h4 class="alert-danger">This account is suspended!</h4>
                        <p>This user's posts will be available once they are unrestricted.</p>
                    </div>
                @elseif ($user->posts()->count() > 0)
                    <ul class="list-unstyled mb-4 w-100">
                            @foreach ($user->posts()->orderBy('created_at', 'desc')->get() as $post)
                                @include('partials.post', ['post' => $post])
                            @endforeach
                    </ul>
                @else
                    <div class="alert alert-info mt-4 mb-4 w-75" role="alert" style="max-width: 800px">
                        <h4 class="alert-heading">No posts yet!</h4>
                        <p>When this user posts something, it will appear here.</p>
                    </div>
                @endif
                @else
                    <div class="alert alert-info mt-4 mb-4 w-75" role="alert" style="max-width: 800px">
                        <h4 class="alert-heading">This user's profile is set to private!</h4>
                        <p>Follow this user to see their posts.</p>
                    </div>
                @endif
            </div>
        </div>
    </section>
</section>

@endsection