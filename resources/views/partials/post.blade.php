<div class="card bg-light mb-3" style="max-width: 20rem;">
  <div class="card-header">
      <h4 class="card-title">{{$post->owner_id}}</h4>
      <p class="card-text">{{$post->date}}</p>
  </div>
  <div class="card-body">
    <p class="card-text">{{$post->content}}</p><br><br>
    <p class="card-text">{{$post->image}}</p>
  </div>
</div>