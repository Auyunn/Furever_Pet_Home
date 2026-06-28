let deleteTargetCommentID = null;

window.addEventListener('scroll', () => {
    const navbar = document.getElementById('navbar');
    if (navbar) {
        if (window.scrollY > 10) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    }
});

function viewPost(boardID) {
    const overlay   = document.getElementById('modal-overlay');
    const modalBody = document.getElementById('modal-body');
    const modalTitle = document.getElementById('modal-title');

    modalTitle.textContent = 'Detail';
    modalBody.innerHTML = '<div class="modal-loading">Loading…</div>';
    overlay.classList.add('open');

    const formData = new FormData();
    formData.append('action', 'detail');
    formData.append('boardID', boardID);

    fetch(window.location.href, { method: 'POST', body: formData })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                modalBody.innerHTML = `<p class="modal-error">${data.error}</p>`;
                return;
            }

            const post     = data.post;
            const comments = data.comments || [];
            modalTitle.textContent = post.Title;

            let html = '';

            if (post.Photo) {
                html += `<img src="../image/pet_community/${post.Photo}" class="detail-img">`;
            }

            html += `
                <div class="detail-meta">
                    <p><strong>Organisation:</strong> ${post.OrgName || post.OrgID}</p>
                    <p><strong>Date:</strong> ${post.Date}</p>
                    <p><strong>Post ID:</strong> ${post.BoardID}</p>
                </div>
                <div class="detail-content-text">${post.Content}</div>
                <hr style="border:0; border-top:1px solid #eee; margin:20px 0;">
            `;

            html += `<div class="comment-section"><h5>💬 Comments (${comments.length})</h5>`;

            if (comments.length === 0) {
                html += '<p class="no-comments">No comments yet.</p>';
            } else {
                comments.forEach(c => {
                    const isReply = c.ReplyID !== null && c.ReplyID !== '';
                   const name = c.CommenterName || c.ResidentID || c.OrgID || 'Unknown';

                    html += `
                        <div class="comment-item ${isReply ? 'is-reply' : ''}" id="comment-${c.CommentID}">
                           ${isReply ? `<div class="reply-label">&#8629; Replying to ${c.ReplyToName || c.ReplyID}</div>` : ''}
                            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px;">
                                <span style="font-size:0.78rem;font-weight:600;color:#825540;">&#128100; ${name}</span>
                                <div style="display:flex;align-items:center;gap:8px;flex-shrink:0;">
                                    <span style="font-size:0.75rem;color:#bbb;">${c.Date}</span>
                                    <button onclick="confirmDeleteComment('${c.CommentID}')" title="Delete comment"
                                        style="cursor:pointer;background:#fdeaea;border:1px solid rgba(204,0,0,0.25);font-size:0.82rem;padding:3px 8px;border-radius:5px;color:#cc0000;line-height:1;">
                                        &#128465;
                                    </button>
                                </div>
                            </div>
                            <p style="margin:0;font-size:0.85rem;color:#555;line-height:1.5;">${c.Content}</p>
                        </div>`;

                });
            }

            html += '</div>';
            modalBody.innerHTML = html;
        })
        .catch(() => {
            modalBody.innerHTML = '<p class="modal-error">Failed to load. Please try again.</p>';
        });
}

function closeModal() {
    document.getElementById('modal-overlay').classList.remove('open');
}

//comment delete

function confirmDeleteComment(commentID) {
    deleteTargetCommentID = commentID;
    document.getElementById('confirm-overlay').classList.add('open');
}

function cancelDelete() {
    deleteTargetCommentID = null;
    document.getElementById('confirm-overlay').classList.remove('open');
}

function doDeleteComment() {
    if (!deleteTargetCommentID) return;

    const formData = new FormData();
    formData.append('action', 'delete_comment');
    formData.append('commentID', deleteTargetCommentID);

    fetch(window.location.href, { method: 'POST', body: formData })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // remove deleted comment
                const card = document.getElementById(`comment-${deleteTargetCommentID}`);
                if (card) {
                    card.style.transition = 'opacity 0.3s, transform 0.3s';
                    card.style.opacity    = '0';
                    card.style.transform  = 'scale(0.95)';
                    setTimeout(() => {
                        card.remove();
                        // udpaet comment count kat heading
                        const section = document.querySelector('.comment-section h5');
                        if (section) {
                            const remaining = document.querySelectorAll('.comment-item').length;
                            section.textContent = `💬 Comments (${remaining})`;
                        }
                    }, 300);
                }
            } else {
                alert('Failed to delete comment. Please try again.');
            }
            cancelDelete();
        })
        .catch(() => {
            alert('Connection error. Please try again.');
            cancelDelete();
        });
}
