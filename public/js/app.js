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
            // Update button to reflect bookmarked status
            updateBookmarkButton(postId, true);
        } else {
            // Update button to reflect unbookmarked status
            updateBookmarkButton(postId, false);
            
            // Remove post from DOM if on bookmarks page
            if (window.location.href.includes('/bookmarks')) {
                document.getElementById('post-' + postId).remove();
            }
        }
    });
}

function updateBookmarkButton(postId, isBookmarked) {
    let button = document.getElementById('btn-bookmark-' + postId);
    let bookmarkCountElement = document.getElementById('bookmark-count-' + postId);
    let bookmarkCount = parseInt(bookmarkCountElement.textContent);

    if(isBookmarked) {
        button.setAttribute('onclick', 'handleBookmark(' + postId + ', \'unbookmark\')');
        button.classList.replace('bi-bookmark', 'bi-bookmark-fill');
        bookmarkCountElement.textContent = bookmarkCount + 1;
    } else {
        button.setAttribute('onclick', 'handleBookmark(' + postId + ', \'bookmark\')');
        button.classList.replace('bi-bookmark-fill', 'bi-bookmark');
        bookmarkCountElement.textContent = bookmarkCount - 1;
    }
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

let nextPageUrl = '{{ $posts->nextPageUrl() }}';

function loadMorePosts() {
    console.log(nextPageUrl);
    if (!nextPageUrl) return; 

    fetch(nextPageUrl)
        .then(response => response.json())
        .then(data => {
            document.getElementById('post-list').insertAdjacentHTML('beforeend', data.posts);
            nextPageUrl = data.next_page_url;

            if (!nextPageUrl) {
                document.getElementById('load-more').style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Error loading more posts:', error);
        });
}