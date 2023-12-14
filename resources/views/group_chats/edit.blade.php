@extends('layouts.app')

@section('sidebar')
    @include('partials.sidebar')
@endsection

@section('content')
<div class="d-flex flex-column align-items-center mx-auto w-100 overflow-auto">
    <div class="mt-4">
        <h1>Edit Group Chat</h1>

        <form method="POST" action="{{ route('group-chats.update.api', $groupChat->group_id) }}">
            @csrf
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ $groupChat->name }}" required>
            </div>

            <div class="form-group">
                <label for="description">Description:</label>
                <textarea class="form-control" id="description" name="description" required>{{ $groupChat->description }}</textarea>
            </div>

            {{-- <div class="form-group">
                <label for="users">Add Users:</label>
                <select multiple class="form-control" id="users" name="users[]">
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div> --}}

            <button type="submit" class="btn btn-primary">Update</button>
        </form>
        <h3 class="mt-2">Users accepted in this group chat:</h3>
        <ul>
            @foreach ($acceptedMembers as $member)
                <li>
                    <a href="{{ route('user.profile', ['id' => $member->user_id]) }}">
                        {{ $member->name }}
                    </a>
                </li>
            @endforeach
        </ul>

        <h3>Waiting for these users to accept the invite:</h3>
        <ul>
            @foreach ($waitingMembers as $pendingMember)
                <li>
                    <a href="{{ route('user.profile', ['id' => $pendingMember->user_id]) }}">
                        {{ $pendingMember->name }}
                    </a>
                </li>
            @endforeach
        </ul>

    </div>
</div>
@endsection