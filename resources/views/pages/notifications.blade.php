@extends('layouts.app')

@section('sidebar')
    @include('partials.sidebar')
@endsection

@section('content')
<div class="d-flex flex-column align-items-center ms-auto me-auto w-100 overflow-auto">
        <h3 class="bg-primary text-white rounded mt-3 p-2">Notifications</h3>
        <div id="notifications" class="w-75">
            @include('partials.notification')
        </div>
</div>

<script>
    function startLiveUpdate(){
        var n = document.getElementById('notifications');
        setInterval(() => {
            // Make an AJAX request using the Fetch API
            fetch('{{ route('notifications.api') }}?query=' + {{auth()->user()->user_id}})
                .then(response => response.text()) // Parse the response as JSON
                .then(data => {
                    // Update the content area with the search results
                    n.innerHTML = data;
                })
                .catch(error => console.error('Error:', error));
        }, 10000);
    }
    startLiveUpdate();
</script>
@endsection
