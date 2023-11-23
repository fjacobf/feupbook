@if($users->isEmpty())
    <p>No users found.</p>
@else
    <div class="card-columns">
        @foreach($users as $user)
            <div class="card mb-4" style="max-width: 600px;">
                <div class="card-body">
                    <h5 class="card-title">{{ $user->name }}</h5>
                    <p class="card-text text-secondary"><span>@</span>{{ $user->username }}</span>
                    <p class="card-text">{{ $user->bio }}</p>
                    <a href="{{ route('user.profile', ['id' => $user->user_id]) }}" class="btn btn-primary">View Profile</a>
                </div>
            </div>
        @endforeach
    </div>
@endif