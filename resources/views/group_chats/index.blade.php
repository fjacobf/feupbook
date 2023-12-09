@extends('layouts.app')

@section('sidebar')
    {{-- @include('partials.sidebar') --}}
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8 offset-md-3">
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
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
        <div class="input-group">
            <input type="text" name="query" id="query" class="form-control" style="max-width: 300px;" placeholder="Enter username">
            <div class="input-group-append">
                <button type="button" class="btn btn-primary" id="search_button">Search</button>
            </div>
        </div>
        <form action={{ route('group-chats.create.api') }} method="POST">
            @csrf
            <label for="name" class="mb-2">Group Name:</label>
            <div class="form-group">
                <input type="text" name="name" id="name" class="form-control" required>
            </div>

            <label for="description" class="mb-2">Group Description:</label>
            <div class="form-group">
                <textarea name="description" id="description" class="form-control" rows="3" required></textarea>
            </div>

            <label for="query" class="mb-2">Search:</label>
            <div class="form-group d-flex align-items-center">
                <select name="usernames[]" id="userDropdown" multiple>
                    <!-- Options should be populated with the users of the app -->
                </select>
            </div>

            <input type="submit" value="Create Group Chat">
        </form>

        
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
