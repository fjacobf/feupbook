@extends('layouts.app')

@section('title', 'Search Results')

@section('sidebar')
    @include('partials.sidebar')
@endsection

@section('content')
    <div class="container">
        <h1>Search Results for "{{ $query }}"</h1>

        <!-- Search Form -->
        <form id="searchForm">
            <div class="mb-3">
                <label for="query" class="form-label">Search Users</label>
                <input type="text" class="form-control" id="query" name="query" required value="{{ $query }}">
            </div>
            <button type="submit" class="btn btn-primary">Search</button>
        </form>

        <!-- Search Results -->
        <div id="searchResults">
            @if($users->isEmpty())
                <p>No users found.</p>
            @else
                <ul>
                    @foreach($users as $user)
                        <li>
                            <a href="{{ route('user.profile', ['id' => $user->user_id]) }}">
                                {{ $user->username }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    <!-- JavaScript to handle the form submission with AJAX -->
    <script>
        document.getElementById('searchForm').addEventListener('submit', function (event) {
            event.preventDefault();

            // Get the search query from the form
            var query = document.getElementById('query').value;

            // Make an AJAX request to the search route
            fetch('{{ route('search') }}?query=' + encodeURIComponent(query))
                .then(response => response.text())
                .then(data => {
                    // Update the search results div with the new content
                    document.getElementById('searchResults').innerHTML = data;
                });
        });
    </script>
@endsection
