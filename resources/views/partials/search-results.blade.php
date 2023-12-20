@if($users->isEmpty())
    <p>No users found.</p>
@else
    <div class="d-flex flex-column align-items-center">
        @foreach($users as $user)
            <div class="card mb-4 w-100" style="max-width: 800px;">
                <div class="card-body">
                    <div>
                        <img src="{{ asset('images/profile_pics/' . $user->avatar)}}" alt="{{ $user->name }}" class="rounded-circle" width="50" style="border: 1px solid #000000">
                        <h5 class="card-title">{{ $user->name }}</h5>
                    </div>
                    <p class="card-text text-secondary"><span>@</span>{{ $user->username }}</span>
                    <p class="card-text">{{ $user->bio }}</p>
                    <a href="{{ route('user.profile', ['id' => $user->user_id]) }}" class="btn btn-primary">View Profile</a>
                </div>
            </div>
        @endforeach
    </div>
@endif