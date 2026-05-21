function toggleComment(commentId) {
    var panel = document.getElementById('panel-'+commentId);
    panel.classList.toggle('open');
    if(panel.classList.contains('open')) {
        document.getElementById('input-' + commentId).focus();
    }
}

function submitComment(commentId) {
    var input = document.getElementById('input-' + commentId);
    var commentText = input.value.trim();
    if(!commentText) return;
    var commentList = document.getElementById('list-' + commentId);
    var count = document.getElementById('count-' + commentId);
    var newComment = document.createElement('div');
    newComment.className = 'comment-item';
    newComment.innerHTML=
        '<div class="author">Name</div>' +
        '<div class="comment-text">' + escapeHtml(commentText) + '</div>' +
        '<div class = "comment-time">Date</div>';

    commentList.appendChild(newComment);
    count.textContent = parseInt(count.textContent) + 1;
    input.value = '';
}

document.addEventListener('keydown', function(event) {
    if(event.key === 'Enter' && event.target.classList.contains('comment-input')) {
        var commentId = event.target.id.split('-')[1];
        submitComment(commentId);
    }
});

function escapeHtml(str) {
    return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

//-------------------------------- ADMIN COMMUNITY -----------------------------------
var posts = {
    1: { title: 'ABC', image: 'dog.jpg', details: ['Event venue : Central Park'] },
    2: { title: 'ABC', image: 'cat.jpg', details: ['Date : 2026-05-05'] }
};

var deleteTargetId= null;

// View post
function viewPost(postId) {
    var post = posts[postId];
    if(!post) return;

    document.getElementById('modal-title').textContent = post.title;
    document.getElementById('modal-img').src = post.image;
    document.getElementById('modal-img').alt = post.title;

    var contentDiv = document.getElementById('modal-content');
    contentDiv.innerHTML = '';
    post.details.forEach(function(detail) {
        var p = document.createElement('p');
        p.textContent = detail;
        contentDiv.appendChild(p);
    });

    document.getElementById('modal-overlay').classList.add('open');
}

function closeModal() {
    document.getElementById('modal-overlay').classList.remove('open');
}

// Delete post
function deletePost(postId) {
    deleteTargetId = postId;
    document.getElementById('confirm-overlay').classList.add('open');
}

function cancelDelete() {
    deleteTargetId = null;
    document.getElementById('confirm-overlay').classList.remove('open');
}

function confirmDelete() {
    if(!deleteTargetId) return;

    var box = document.getElementById('post-' + deleteTargetId);
    if(box) {
        box.style.transition = 'opacity 0.5s';
        box.style.opacity = '0';
        setTimeout(function() {
            box.remove();
        }, 300);
    }

    delete posts[deleteTargetId];
    cancelDelete();
}

document.addEventListener('keydown', function(e) {
    if(e.key === 'Escape') {
        closeModal();
        cancelDelete();
    }
});
