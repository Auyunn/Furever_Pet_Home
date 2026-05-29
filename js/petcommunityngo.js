function toggleComment(commentId) {
  var panel = document.getElementById('panel-' + commentId);
  panel.classList.toggle('open');
  if (panel.classList.contains('open')) {
    document.getElementById('input-' + commentId).focus();
  }
}

function submitComment(commentId) {
  var input = document.getElementById('input-' + commentId);
  var commentText = input.value.trim();
  if (!commentText) return;

  var commentList = document.getElementById('list-' + commentId);
  var count = document.getElementById('count-' + commentId);

  var newComment = document.createElement('div');
  newComment.className = 'comment-item';
  newComment.innerHTML =
    '<div class="author">User</div>' +
    '<div class="comment-text">' + escapeHtml(commentText) + '</div>' +
    '<div class="comment-time">' + new Date().toLocaleString() + '</div>';

  commentList.appendChild(newComment);
  count.textContent = parseInt(count.textContent) + 1;
  input.value = '';
}

function escapeHtml(str) {
  return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

function viewPost(postId) {
  alert("Editing post " + postId);
}

function deletePost(postId) {
  var box = document.getElementById('post-' + postId);
  if (box) {
    box.remove();
  }
}

function goToAddPage() {
  window.location.href = 'addboard.html';        
}