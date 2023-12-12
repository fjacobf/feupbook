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
                    @if (auth()->user()->id === $groupChat->owner->id)
                        <a href="{{ route('group-chats.edit', $groupChat->group_id) }}" class="btn btn-primary">Edit</a>
                    @endif

                    <h2>Messages</h2>

                    <div id="chat" class="mt-3 mb-3" style="height: 400px; overflow-y: scroll; border: 1px solid #ccc; padding: 10px; border-radius: 5px;"></div>

                    <form method="POST" action="{{ route('group-chats.sendMessage', $groupChat->group_id) }}">
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
            setInterval(function() {
                var xhr = new XMLHttpRequest();
                
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === XMLHttpRequest.DONE) {
                        if (xhr.status === 200) {
                            let messages = JSON.parse(xhr.responseText);
                            let html = '';
                            messages.forEach(function(message) {
                                if (message.emitter.id === {{ auth()->id() }}) {
                                    html += '<div class="text-right">';
                                    html += '<p><strong>' + message.emitter.name + '</strong></p>';
                                    html += '<p>' + message.content + '</p>';
                                    html += '<p><small>' + message.date + '</small></p>';
                                    html += '</div>';
                                } else {
                                    html += '<div class="text-left">';
                                    html += '<p><strong>' + message.emitter.name + '</strong></p>';
                                    html += '<p>' + message.content + '</p>';
                                    html += '<p><small>' + message.date + '</small></p>';
                                    html += '</div>';
                                }
                            });
                            document.querySelector('#chat').innerHTML = html;
                        }
                    }
                };
                
                xhr.open('GET', '/group-chats/{{ $groupChat->group_id }}/messages', true);
                xhr.send();
            }, 200);
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var form = document.querySelector('form');
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
@endsection