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

function handleLikeDislikeComment(commentId, action) {
    let url = '/comment/' + commentId + '/' + action;
    let method = action === 'like' ? 'POST' : 'DELETE';

    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ comment_id: commentId })
    })
    .then(response => response.json())
    .then(data => {
        var button = document.getElementById('btn-comment' + commentId)
        if(action === 'like') {
            button.setAttribute('onclick', 'handleLikeDislikeComment(' + commentId + ', \'dislike\')');
            button.classList.remove('bi-heart');
            button.classList.add('bi-heart-fill');
            let likeCount = parseInt(document.getElementById('comment-like-count-' + commentId).textContent);
            likeCount++;
            document.getElementById('comment-like-count-' + commentId).textContent = likeCount + " likes";
        } else {
            button.setAttribute('onclick', 'handleLikeDislikeComment(' + commentId + ', \'like\')');
            button.classList.remove('bi-heart-fill');
            button.classList.add('bi-heart');
            let likeCount = parseInt(document.getElementById('comment-like-count-' + commentId).textContent);
            likeCount--;
            document.getElementById('comment-like-count-' + commentId).textContent = likeCount + " likes";
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



function loadMorePosts() {
    let postList = document.getElementById('post-list');
    let pageContext = postList.dataset.pageContext;
    let currentPage = parseInt(postList.dataset.currentPage) + 1;

    let nextPageUrl;
    if (pageContext === 'home') {
        nextPageUrl = '/api/loadFeed?page=' + currentPage;
    } else if (pageContext === 'forYou') {
        nextPageUrl = '/api/loadForYou?page=' + currentPage;
    } else {
        console.error('Unknown page context');
        return;
    }

    console.log('Next page URL:', nextPageUrl);
    if (!nextPageUrl) return;

    fetch(nextPageUrl)
        .then(response => response.json())
        .then(data => {
            const postsHtml = data.posts.join('');
            postList.insertAdjacentHTML('beforeend', postsHtml);
            postList.dataset.currentPage = currentPage;

            if (!data.next_page_url) {
                document.getElementById('load-more').style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Error loading more posts:', error);
        });
}

document.querySelector('.open-btn').addEventListener('click', function() {
    document.querySelector('.sidebar').classList.add('active');
});

document.querySelector('.close-btn').addEventListener('click', function() {
    document.querySelector('.sidebar').classList.remove('active');
});