@extends('layouts.app')

@section('sidebar')
    @include('partials.sidebar')
@endsection

@section('content')
    <div class="container" style="margin-left: 280px;">
        <h1>Followers</h1>
        @if ($followers->isEmpty())
            <p>This use is not being followed by anyone.</p>
            @else
            <div class="card-columns">
                @foreach($followers as $follower)
                <div class="card mb-4" style="max-width: 600px;">
                    <div class="card-body">
                        <h5 class="card-title">{{ $follower->name }}</h5>
                        <p class="card-text text-secondary"><span>@</span>{{ $follower->username }}</span>
                        <p class="card-text">{{ $follower->bio }}</p>
                        <div class=d-flex>
                            <a href="{{ route('user.profile', ['id' => $follower->user_id]) }}" class="btn btn-primary" style="margin-right: 10px">View Profile</a>
                            <form action="{{ route('user.removeFollower', ['id' => $follower->user_id]) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-danger">Remove follower</button>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    @endif
@endsection