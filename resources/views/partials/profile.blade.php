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
            </div>
        </div>
    </section>
</section>

@endsection