@extends('layouts.app')

@section('sidebar')
    @include('partials.sidebar')
@endsection

@section('content')
<div class="d-flex flex-column align-items-center w-100 overflow-auto">
    <div class="d-flex justify-content-center">
        <h3 class="bg-primary text-white rounded mt-3 p-2">Following</h3>
    </div>
    @if ($following->isEmpty())
        <p>This use is not following anyone.</p>
        @else
        <div class="card-columns">
            @foreach($following as $user_following)
            <div class="card mb-4" style="max-width: 600px;">
                <div class="card-body">
                    <h5 class="card-title">{{ $user_following->name }}</h5>
                    <p class="card-text text-secondary"><span>@</span>{{ $user_following->username }}</span>
                    <p class="card-text">{{ $user_following->bio }}</p>
                    <div class=d-flex>
                        <a href="{{ route('user.profile', ['id' => $user_following->user_id]) }}" class="btn btn-primary" style="margin-right: 10px">View Profile</a>
                        <form action="{{ route('user.unfollow', ['id' => $user_following->user_id]) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-danger">Unfollow</button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
</div>
@endif
@endsection