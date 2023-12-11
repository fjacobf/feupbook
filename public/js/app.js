function handleLikeDislike(postId, action) {
   this.event.preventDefault();
   let url = '/post/' + postId + '/' + action;
   console.log(postId);
   console.log(action);
   let method = action === 'like' ? 'POST' : 'DELETE';

   fetch(url, {
       method: method,
       headers: {
           'Content-Type': 'application/json',
           'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
       },
       body: JSON.stringify({ post_id: postId })
   })
   .then(response => response.json())
   .then(data => {
       if(action === 'like') {
            document.getElementById('btn-' + postId).setAttribute('onclick', 'handleLikeDislike(' + postId + ', \'dislike\')');
            document.getElementById('btn-' + postId).classList.remove('bi-heart');
            document.getElementById('btn-' + postId).classList.add('bi-heart-fill');
            let likeCount = parseInt(document.getElementById('like-count-' + postId).textContent);
            likeCount++;
            document.getElementById('like-count-' + postId).textContent = likeCount;
       } else {
            document.getElementById('btn-' + postId).setAttribute('onclick', 'handleLikeDislike(' + postId + ', \'like\')');
            document.getElementById('btn-' + postId).classList.remove('bi-heart-fill');
            document.getElementById('btn-' + postId).classList.add('bi-heart');
            let likeCount = parseInt(document.getElementById('like-count-' + postId).textContent);
            likeCount--;
            document.getElementById('like-count-' + postId).textContent = likeCount;
       }
   })
}