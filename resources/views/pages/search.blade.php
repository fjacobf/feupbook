@extends('layouts.app')

@section('sidebar')
    @include('partials.sidebar')
@endsection

@section('content')
    <div class="container" style="margin-left: 280px;">
        <h1>Search Users</h1>

        <form action="" method="GET">
            @csrf
            <label for="query" class="mb-2">Search:</label>
            <div class="form-group d-flex align-items-center">
                <div class="input-group">
                    <input type="text" name="query" id="query" class="form-control" style="max-width: 300px;" placeholder="Enter username">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary">Search</button>
                    </div>
                </div>
            </div>
        </form>
        <hr style="max-width: 700px;"/>

        <div id="searchResults">
            @if($users->isEmpty())
                <p>No results found.</p>
            @else
                @include('partials.search-results')
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Intercept form submission
            document.getElementById('searchForm').addEventListener('submit', function (event) {
                event.preventDefault(); // Prevent the default form submission
                var query = document.getElementById('query').value;

                // Make an AJAX request using the Fetch API
                fetch('{{ route('search') }}?query=' + query)
                    .then(response => response.text()) // Parse the response as JSON
                    .then(data => {
                        // Update the content area with the search results
                        document.getElementById('searchResults').innerHTML = data;
                    })
                    .catch(error => console.error('Error:', error));
            });
        });
    </script>
@endsection

