@extends('layouts.app')

@section('sidebar')
    @include('partials.sidebar')
@endsection

@section('content')
    <div class="container">
        <h1>Search Users</h1>

        <form action="{{ route('search') }}" method="GET">
            @csrf
            <div class="form-group">
                <label for="query">Search:</label>
                <input type="text" name="query" id="query" class="form-control" placeholder="Enter username">
            </div>
            <button type="submit" class="btn btn-primary">Search</button>
        </form>

        @if($users->isEmpty())
            <p>No results found.</p>
        @else
            @include('partials.search-results')
        @endif
    </div>
@endsection

