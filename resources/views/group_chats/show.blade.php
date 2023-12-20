@extends('layouts.app')

@section('sidebar')
    @include('partials.sidebar')
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="mt-4">
                <h1>{{ $groupChat->name }}</h1>
                <p>Description: {{ $groupChat->description }}</p>
                <p>Owner: {{ $groupChat->owner->name }}</p>
                @if (auth()->user()->user_id === $groupChat->owner->user_id)
                    <a href="{{ route('group-chats.edit', $groupChat->group_id) }}" class="btn btn-primary">Edit</a>
                @endif

                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#groupChatUsersModal">
                Show Group Chat Users
                </button>
                
                <div class="modal fade" id="groupChatUsersModal" tabindex="-1" role="dialog" aria-labelledby="groupChatUsersModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="groupChatUsersModalLabel">Group Chat Users</h5>
                            </div>
                            <div class="modal-body">
                                <ul id="groupChatUsers" class="list-group">
                                    <!-- Users will be dynamically added here -->
                                </ul>
                            </div>
                            <div class="modal-footer">
                                <button type="button" id="closeModalButton" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
                

                <h2>Messages</h2>

                <div id="chat" class="mt-3 mb-3 chat-box p-3 rounded">
                    <!-- Messages will be dynamically added here -->
                </div>
                

                <form id="messageForm" method="POST" action="{{ route('group-chats.sendMessage', $groupChat->group_id) }}">
                    @csrf
                    <div class="form-group">
                        <label for="content">Message:</label>
                        <input class="form-control" id="content" name="content" required></input>
                    </div>
                    <button type="submit" class="btn btn-primary">Send</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- <script>            
    document.addEventListener('DOMContentLoaded', function() {
        var chatBox = document.querySelector('#chat');

        // Function to scroll the chat box to the bottom
        function scrollToBottom() {
            chatBox.scrollTop = chatBox.scrollHeight;
        }

        // Initial scroll to bottom
        scrollToBottom();

        let message_length = 0;

        setInterval(function() {
            
        }, 200);
    });
</script> --}}

<script type="module">
    var chatBox = document.querySelector('#chat');

    function scrollToBottom() {
        chatBox.scrollTop = chatBox.scrollHeight;
    }

    document.addEventListener('DOMContentLoaded', function() {
        var form = document.querySelector('#messageForm');
        var chatBox = document.querySelector('#chat');

        form.addEventListener('submit', function(event) {
            event.preventDefault();
    
            var formData = new FormData(form);
            var request = new XMLHttpRequest();
            request.open('POST', "{{ route('group-chats.sendMessage', $groupChat->group_id) }}");
            request.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            request.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    
            request.onload = function() {
                if (request.status >= 200 && request.status < 400) {
                    // Success!
                    console.log(request.responseText);

                    // Scroll to bottom after sending a new message
                    scrollToBottom();
                } else {
                    // We reached our target server, but it returned an error
                    console.error('Server error');
                }
            };
    
            request.onerror = function() {
                // There was a connection error of some sort
                console.error('Connection error');
            };
    
            request.send(formData);
        });

        var xhr = new XMLHttpRequest();
        xhr.open('GET', '/group-chats/{{ $groupChat->group_id }}/messages', true);
        xhr.send();
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    let messages = JSON.parse(xhr.responseText);
                    let html = '';
                    messages.forEach(function(message) {
                        html += '<div class="message ' + (message.emitter.user_id === {{ auth()->user()->user_id }} ? 'text-right bg-primary text-white' : 'text-left bg-light') + '">';
                        html += '<p class="font-weight-bold mb-0">' + message.emitter.name + '</p>';
                        html += '<p class="mb-0">' + message.content + '</p>';
                        html += '<p class="small">' + message.date + '</p>';
                        html += '</div>';
                    });
                    chatBox.innerHTML = html;
                    scrollToBottom();
                }
            }
        };
    });

    document.querySelector('[data-target="#groupChatUsersModal"]').addEventListener('click', function () {
        fetch('{{ route('group-chats.getMembers.api', $groupChat->group_id) }}', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            }
        })
        .then(response => response.json())
        .then(data => {
            console.log(data);
            const groupChatUsersList = document.querySelector('#groupChatUsers');
            if (!groupChatUsersList) {
                console.error('Element with id "groupChatUsersList" not found');
                return;
            }
            groupChatUsersList.innerHTML = '';
            data.forEach(user => {
                const listItem = document.createElement('li');
                listItem.className = 'list-group-item';
                listItem.textContent = user.name;
                groupChatUsersList.appendChild(listItem);
            });
            
            // Show the modal
            const modal = new bootstrap.Modal(document.getElementById('groupChatUsersModal'));
            modal.show();
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });

    // Close the modal when the "Close" button is clicked
    document.querySelector('#closeModalButton').addEventListener('click', function () {
        const modal = bootstrap.Modal.getInstance(document.getElementById('groupChatUsersModal'));
        modal.hide();
    });


    Echo.private('group-chat.' + {{ $groupChat->group_id }})
        .listen('NewMessage', (e) => {
            console.log(e);
            let html = '';
            html += '<div class="message ' + (e.emitter_id === {{ auth()->user()->user_id }} ? 'text-right bg-primary text-white' : 'text-left bg-light') + '">';
            html += '<p class="font-weight-bold mb-0">' + e.emitter_name + '</p>';
            html += '<p class="mb-0">' + e.content + '</p>';
            html += '<p class="small">' + e.date + '</p>';
            html += '</div>';
            chatBox.innerHTML += html;
            scrollToBottom();
        });
</script>
<style>
    .chat-box {
        height: 400px;
        overflow-y: scroll;
        border: 1px solid #ccc;
        padding: 10px;
        border-radius: 5px;
    }

    .message {
        margin-bottom: 15px;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
    }

    .message p {
        margin: 0;
    }

    .message .small {
        font-size: 0.7rem;
    }

    .text-right {
        text-align: right;
    }

    .text-left {
        text-align: left;
    }
</style>
@endsection
