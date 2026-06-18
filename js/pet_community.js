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
