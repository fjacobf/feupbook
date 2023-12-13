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

                    <h2>Messages</h2>

                    <div id="chat" class="mt-3 mb-3 chat-box">
                        <!-- Messages will be dynamically added here -->
                    </div>

                    <form id="messageForm" method="POST" action="{{ route('group-chats.sendMessage', $groupChat->group_id) }}">
                        @csrf
                        <div class="form-group">
                            <label for="content">Message:</label>
                            <textarea class="form-control" id="content" name="content" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Send</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>            
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
                var xhr = new XMLHttpRequest();
                
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === XMLHttpRequest.DONE) {
                        if (xhr.status === 200) {
                            let messages = JSON.parse(xhr.responseText);
                            let html = '';
                            if(messages.length > message_length) {
                                messages.forEach(function(message) {
                                    html += '<div class="message ' + (message.emitter.id === {{ auth()->id() }} ? 'text-right' : 'text-left') + '">';
                                    html += '<p><strong>' + message.emitter.name + '</strong></p>';
                                    html += '<p>' + message.content + '</p>';
                                    html += '<p><small>' + message.date + '</small></p>';
                                    html += '</div>';
                                });
                                chatBox.innerHTML = html;
                                message_length = messages.length;
                                scrollToBottom();
                            }
                        }
                    }
                };
                
                xhr.open('GET', '/group-chats/{{ $groupChat->group_id }}/messages', true);
                xhr.send();
            }, 200);
        });

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

        .message text-right {
            background-color: #dff0d8;
        }

        .message text-left {
            background-color: #d9edf7;
        }
    </style>
@endsection
