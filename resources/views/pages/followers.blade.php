@extends('layouts.app')

@section('sidebar')
    @include('partials.sidebar')
@endsection

@section('content')
<div class="d-flex flex-column align-items-center w-100 overflow-auto">
    <div class="d-flex justify-content-center">
        <h3 class="bg-primary text-white rounded mt-3 p-2">Followers</h3>
    </div>
    @if ($followers->isEmpty())
        <p>This use is not being followed by anyone.</p>
        @else
        <div class="card-columns mt-2">
            @foreach($followers as $follower)
            <div class="card mb-4" style="max-width: 600px;">
                <div class="card-body w-100">
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