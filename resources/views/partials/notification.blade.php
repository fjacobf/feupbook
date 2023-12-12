@if($notifications->isEmpty())
    <p>No notifications found.</p>
@else
    <div class="card-columns">
        @foreach($notifications as $notification)
            <div class="card mb-4" style="max-width: 600px;">
                <div class="card-body">
                    <h5 class="card-title">{{ $notification->message }}</h5>
                    <p class="card-text text-secondary">{{ $notification->date}}</span>
                </div>
            </div>
        @endforeach
    </div>
@endif