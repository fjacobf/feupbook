@if($users->isEmpty())
    <p>No users found.</p>
@else
    <ul>
        @foreach($users as $user)
            <li>
                <a href="{{ route('user.profile', ['id' => $user->user_id]) }}">
                    {{ $user->username }}
                </a>
            </li>
        @endforeach
    </ul>
@endif
