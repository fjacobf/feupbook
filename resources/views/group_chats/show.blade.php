<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $groupChat->name }}</title>
</head>
<body>
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
</body>
</html>
