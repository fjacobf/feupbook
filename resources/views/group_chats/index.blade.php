@extends('layouts.app')

@section('sidebar')
    @include('partials.sidebar')
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
    </div>
@endsection
