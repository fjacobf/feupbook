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
                    <ul>
                        @foreach ($groupChat->messages as $message)
                            <li>
                                <p>Sender: {{ $message->emitter->name }}</p>
                                <p>Date: {{ $message->date }}</p>
                                <p>{{ $message->content }}</p>
                            </li>
                        @endforeach
                    </ul>
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
@endsection
