@extends('layouts.app')

@section('sidebar')
    <div class="col-md-4">
        @include('partials.sidebar')
    </div>
@endsection

@section('content')
<div class="container" style="margin-left: 280px;">
        <div class="row">
            <div class="mt-4">
                <h1>Your Group Chats</h1>

                @if ($userGroups->isEmpty())
                    <p>You are not a member of any group chats.</p>
                @else
                    <ul class="list-group mt-3">
                        @foreach ($userGroups as $groupChat)
                            <li class="list-group-item">
                                <a href="{{ url('/group-chats', $groupChat->group_id) }}" class="text-decoration-none">
                                    <h5 class="mb-1">{{ $groupChat->name }}</h5>
                                </a>
                                <p class="mb-1">{{ $groupChat->description }}</p>
                                <small>Owner: {{ $groupChat->owner->name }}</small>
                                {{-- check if user has status waiting in group-chats table --}}
                                @if ($groupChat->memberStatus(auth()->user()) == 'waiting')
                                    <form action="{{ route('group-chats.acceptInvite.api', $groupChat->group_id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-success">Accept Invite</button>
                                    </form>
                                    <form action="{{ route('group-chats.rejectInvite.api', $groupChat->group_id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-danger">Reject Invite</button>
                                    </form>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
        <hr style="max-width: 700px;"/>
        <div class="mt-4">
            <h1>Create a new group chat</h1>
            <p>Search users:</p>
            <div class="input-group">
                <input type="text" name="query" id="query" class="form-control" style="max-width: 300px;" placeholder="Enter username">
                <div class="input-group-append">
                    <button type="button" class="btn btn-primary" id="search_button">Search</button>
                </div>
            </div>
            <form action="{{ route('group-chats.create.api') }}" method="POST">
                @csrf
            
                <div class="mb-3">
                    <label for="name" class="form-label">Group Name:</label>
                    <input type="text" name="name" id="name" class="form-control" required>
                </div>
            
                <div class="mb-3">
                    <label for="description" class="form-label">Group Description:</label>
                    <textarea name="description" id="description" class="form-control" rows="3" required></textarea>
                </div>
            
                <div class="mb-3">
                    <label for="query" class="form-label">Search:</label>
                    <select name="usernames[]" id="userDropdown" class="form-select" multiple>
                        <!-- Options should be populated with the users of the app -->
                    </select>
                </div>
            
                <button type="submit" class="btn btn-primary">Create Group Chat</button>
            </form>
            
        </div>
        <script>
            document.getElementById('search_button').addEventListener('click', function (event) {
                event.preventDefault();
                var query = document.getElementById('query').value;
                fetch('{{ route('search_json.api') }}?query=' + query)
                    .then(response => response.json()) // Parse the response as JSON
                    .then(data => {
                        // Assuming data is an array of users, you can map it to create options for the dropdown
                        var options = data.map(function(user) {
                            return '<option value="' + user.username + '">' + user.username + '</option>';
                        });
                        document.getElementById('userDropdown').innerHTML = options.join('');
                    })
                    .catch(error => console.error('Error:', error));
            });
        </script>
    </div>
@endsection
