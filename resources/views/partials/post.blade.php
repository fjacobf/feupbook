<div class="card bg-light mb-3" style="width: 100%;">
  <div class="card-header">
      <h4 class="card-title">{{ $post->user ? $post->user->username : 'Unknown User' }}</h4>
      <p class="card-text">{{$post->date}}</p>
  </div>
  <div class="card-body">
    <p class="card-text">{{$post->content}}</p><br><br>
    <p class="card-text">{{$post->image}}</p>
  </div>
</div>