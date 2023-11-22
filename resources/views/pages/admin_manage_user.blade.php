@extends('layouts.app')

@section('sidebar')
    @include('partials.sidebar')
@endsection

@section('content')
    <div class="container">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h2>Edit User: {{ $user->name }}</h2>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('admin.updateUser', ['id' => $user->user_id]) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="username" class="form-label">Username:</label>
                            <input type="text" class="form-control" name="username" value="{{ $user->username }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email:</label>
                            <input type="email" class="form-control" name="email" value="{{ $user->email }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="name" class="form-label">Name:</label>
                            <input type="text" class="form-control" name="name" value="{{ $user->name }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="bio" class="form-label">Bio:</label>
                            <textarea class="form-control" name="bio">{{ $user->bio }}</textarea>
                        </div>
                        <div class="form-check mb-3">
                            <input type="checkbox" class="form-check-input" value="1" name="private" {{ $user->private ? 'checked' : '' }}>
                            <label class="form-check-label" for="private">Private</label>
                        </div>
                        <div class="mb-3">
                            <label for="user_type" class="form-label">User Type:</label>
                            <select class="form-select" name="user_type">
                                @foreach($userTypes as $type)
                                    <option value="{{ $type }}" {{ $user->user_type === $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">Update User</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

