function handleLikeDislike(postId, action) {
   let url = '/post/' + postId + '/' + action;
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

function handleBookmark(postId, action) {
   let url = '/post/' + postId + '/' + action;
   let method = action === 'bookmark' ? 'POST' : 'DELETE';

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
       if(action === 'bookmark') {
            document.getElementById('btn-bookmark-' + postId).setAttribute('onclick', 'handleBookmark(' + postId + ', \'unbookmark\')');
            document.getElementById('btn-bookmark-' + postId).classList.remove('bi-bookmark');
            document.getElementById('btn-bookmark-' + postId).classList.add('bi-bookmark-fill');
            let bookmarkCount = parseInt(document.getElementById('bookmark-count-' + postId).textContent);
            bookmarkCount++;
            document.getElementById('bookmark-count-' + postId).textContent = bookmarkCount;
       } else {
            document.getElementById('btn-bookmark-' + postId).setAttribute('onclick', 'handleBookmark(' + postId + ', \'bookmark\')');
            document.getElementById('btn-bookmark-' + postId).classList.remove('bi-bookmark-fill');
            document.getElementById('btn-bookmark-' + postId).classList.add('bi-bookmark');
            let bookmarkCount = parseInt(document.getElementById('bookmark-count-' + postId).textContent);
            bookmarkCount--;
            document.getElementById('bookmark-count-' + postId).textContent = bookmarkCount;
       }
   })
}

function toggleContent(postId, action) {
    var shortContent = document.getElementById('short-content-' + postId);
    var fullContent = document.getElementById('full-content-' + postId);

    if (shortContent && fullContent) {
        if (action === 'more') {
            shortContent.classList.add('d-none');
            fullContent.classList.remove('d-none');
        } else if (action === 'less') {
            fullContent.classList.add('d-none');
            shortContent.classList.remove('d-none');
        }
    }
}