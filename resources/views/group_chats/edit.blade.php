@extends('layouts.app')

@section('sidebar')
    @include('partials.sidebar')
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8 offset-md-2">
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
                    {{-- <h2>Users in this group chat:</h2>
                    <ul>
                        @foreach ($groupChat->users as $user)
                            <li>{{ $user->name }}</li>
                        @endforeach
                    </ul> --}}
                </div>
            </div>
        </div>
    </div>
@endsection