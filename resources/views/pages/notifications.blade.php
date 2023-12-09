@extends('layouts.app')

@section('sidebar')
    @include('partials.sidebar')
@endsection

@section('content')
<div class="flex-grow-1" style="margin-left: 280px;">
    <div class="d-flex justify-content-center flex-column">
        @forelse($notifications as $notification)
                <p>{{$notification->message}}</p>
                <p>{{$notification->date}}</p>
            @empty
                <div class="alert alert-info mt-4" role="alert">
                    <h4 class="alert-heading">No posts to display!</h4>
                    <p>The user has no notifications</p>
                </div>
            @endforelse
    </div>
</div>

@endsection
