@extends('layouts.app')

@section('sidebar')
    @include('partials.sidebar')
@endsection

@section('content')
    <div class="container" style="margin-left: 280px;">
        <h2>Edit Profile</h2>
        <form action="{{ route('user.updateProfile', ['id' => $user->user_id]) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input value="{{ $user->name }}" type="text" class="form-control" id="name" name="name" aria-describedby="nameHelp">
                <div id="nameHelp" class="form-text">Your name will be displayed on your profile.</div>
            </div>
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input value="{{ $user->username }}" type="text" class="form-control" id="username" name="username" aria-describedby="usernameHelp">
                <div id="usernameHelp" class="form-text">Your username will be used to identify you on the site.</div>
            </div>
            <div class="mb-3">
                <label for="bio" class="form-label">Bio</label>
                <textarea class="form-control" id="bio" name="bio" rows="3">{{ $user->bio }}</textarea>
                <div id="bioHelp" class="form-text">Use your biography to tell people more about yourself.</div>
            </div>
            <div class="mb-3">
                <label for="private" class="form-label">Profile privacy</label>
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" value="1" id="private" name="private" {{ $user->private ? 'checked' : '' }}>
                    <label class="form-check-label" for="private">Private</label>
                </div>
                <div id="privateHelp" class="form-text">If your profile is private, only your followers will be able to see your posts.</div>
            </div>
            <button type="submit" class="btn btn-primary">Update Profile</button>
        </form>
    </div>
@endsection