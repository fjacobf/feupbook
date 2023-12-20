@extends('layouts.app')

@section('sidebar')
    @include('partials.sidebar')
@endsection

@section('content')
<div class="d-flex flex-column align-items-center mx-auto w-100 overflow-auto">
    <div class="mt-4">
        <h1>Edit Group Chat</h1>

        <form method="POST" action="{{ route('group-chats.update', $groupChat->group_id) }}">
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
        {{-- div to say if group-chat was updated or not --}}
        <div id="updateMessage"></div>
        <h3 class="mt-2">Users accepted in this group chat:</h3>
        <ul>
            @foreach ($acceptedMembers as $member)
                <li>
                    <a href="{{ route('user.profile', ['id' => $member->user_id]) }}">
                        {{ $member->name }}
                    </a>
                    @if ($member->user_id !== $groupChat->owner_id)
                        <form method="POST" action="{{ route('group-chats.removeMember', ['groupChat' => $groupChat->group_id]) }}" style="display: inline;">
                            @csrf
                            <input type="hidden" name="username" value="{{ $member->username }}">
                            <button type="submit" class="btn btn-danger">Remove</button>
                        </form>
                    @endif
                </li>
            @endforeach
        </ul>

        <h3>Waiting for these users to accept the invite:</h3>
        <ul id="waitingList">
            @foreach ($waitingMembers as $pendingMember)
                <li>
                    <a href="{{ route('user.profile', ['id' => $pendingMember->user_id]) }}">
                        {{ $pendingMember->name }}
                    </a>
                </li>
            @endforeach
        </ul>

        <h3>Search and add a user to the group chat</h3>
        <input type="text" id="searchUserInput" placeholder="Search for users">
        <ul id="searchResults"></ul>

    </div>
</div>

<script>
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function(event) {
            event.preventDefault();

            const listItem = this.closest('li');

            fetch(this.action, {
                method: 'POST',
                body: new FormData(this),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': this.querySelector('input[name="_token"]').value
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                // Handle the response data here
                console.log(data);
                // Update the message div
                const updateMessage = document.querySelector('#updateMessage');
                updateMessage.textContent = data;
                // Remove the list item from the DOM
                // listItem.remove();
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    });

    document.querySelector('#searchUserInput').addEventListener('input', function(event) {
        const query = this.value;
        fetch(`{{ route('search_json') }}?query=${query}&groupChat={{ $groupChat->group_id }}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            }
        })
        .then(response => response.json())
        .then(data => {
            const searchResults = document.querySelector('#searchResults');
            searchResults.innerHTML = '';
            data.forEach(user => {
                // Check if the user is already in the group chat or waiting to accept the invite
                const isUserInGroupChat = document.querySelector(`#groupChatList a[href="/profile/${user.username}"]`);
                const isUserInWaitingList = document.querySelector(`#waitingList a[href="/profile/${user.username}"]`);
                if (!isUserInGroupChat && !isUserInWaitingList) {
                    const listItem = document.createElement('li');
                    const userLink = document.createElement('a');
                    userLink.href = `/profile/${user.username}`;
                    userLink.textContent = user.username;
                    listItem.appendChild(userLink);
                    const addButton = document.createElement('button');
                    addButton.textContent = 'Add';
                    addButton.addEventListener('click', function() {
                        fetch(`{{ route('group-chats.addMember', ['groupChat' => $groupChat->group_id]) }}`, {
                            method: 'POST',
                            body: JSON.stringify({ username: user.username }),
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            // Handle the response data here
                            console.log(data);
                            // Remove the list item from the search results
                            listItem.remove();
                            // Add the user to the waiting list
                            const waitingList = document.querySelector('#waitingList');
                            const waitingListItem = document.createElement('li');
                            const waitingUserLink = document.createElement('a');
                            waitingUserLink.href = `/profile/${user.username}`;
                            waitingUserLink.textContent = user.username;
                            waitingListItem.appendChild(waitingUserLink);
                            waitingList.appendChild(waitingListItem);
                        })
                        .catch(error => {
                            console.error('Error:', error);
                        });
                    });
                    listItem.appendChild(addButton);
                    searchResults.appendChild(listItem);
                }
            });
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });
</script>
@endsection
