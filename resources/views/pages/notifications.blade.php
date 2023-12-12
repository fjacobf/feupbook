@extends('layouts.app')

@section('sidebar')
    @include('partials.sidebar')
@endsection

@section('content')
<div class="flex-grow-1 container" style="margin-left: 280px;">
    <div class="d-flex justify-content-center flex-column">
        <h3>Notifications</h3>
        <div id="notifications">
            @include('partials.notification')
        </div>
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
        }, 2000);
    }
    startLiveUpdate();
</script>
@endsection
