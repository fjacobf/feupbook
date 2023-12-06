@extends('layouts.app')

@section('sidebar')
    @include('partials.sidebar')
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8 offset-md-4">
                <div class="mt-4">
                    <h1>{{ $groupChat->name }}</h1>
                    <p>Description: {{ $groupChat->description }}</p>
                    <p>Owner: {{ $groupChat->owner->name }}</p>

                    <h2>Messages</h2>
                    {{-- <ul>
                        @foreach ($groupChat->messages as $message)
                            <li>
                                <p>Sender: {{ $message->emitter->name }}</p>
                                <p>Date: {{ $message->date }}</p>
                                <p>{{ $message->content }}</p>
                            </li>
                        @endforeach
                    </ul> --}}

                    <div id="chat"></div>

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

    <div id="chat"></div>

    <script>            
        document.addEventListener('DOMContentLoaded', function() {
    // call $groupChat->messages every 2 seconds
    setInterval(function() {
        var xhr = new XMLHttpRequest();
        
        xhr.onreadystatechange = function() {
            console.log('Ready state:', xhr.readyState);
            
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    // handle success
                    console.log('Response:', xhr.responseText);
                    let messages = JSON.parse(xhr.responseText);
                    let html = '';
                    messages.forEach(function(message) {
                        html += '<p>Sender: ' + (message.emitter ? message.emitter.name : 'Unknown') + '</p>';
                        html += '<p>Date: ' + message.date + '</p>';
                        html += '<p>' + message.content + '</p>';
                    });
                    document.querySelector('#chat').innerHTML = html;
                } else {
                    // handle error
                    console.error('Error:', xhr.status, xhr.statusText);
                }
            }
        };
        
        xhr.open('GET', '/group-chats/{{ $groupChat->group_id }}/messages', true);
        xhr.send();
    }, 2000);
});



    </script>
@endsection
