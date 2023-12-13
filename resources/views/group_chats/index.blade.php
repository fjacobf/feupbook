<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Group Chats</title>
</head>
<body>
    <h1>Group Chats</h1>

    <ul>
        @foreach ($groupChats as $groupChat)
            <li>
                <a href="{{ url('/group-chats', $groupChat->group_id) }}">
                    {{ $groupChat->name }}
                </a>
                <p>{{ $groupChat->description }}</p>
                <p>Owner: {{ $groupChat->owner->name }}</p>
            </li>
        @endforeach
    </ul>
</body>
</html>
