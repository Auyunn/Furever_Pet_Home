let deleteTargetID =null;

window.addEventListener('scroll', ()=>{
    const navbar= document.getElementById('navbar');
    if(navbar){
        if(window.scrollY>10){
            if (window.scrollY > 10) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
        }
    }
});

function viewPost(boardID){
    const overlay= document.getElementById('modal-overlay');
    const modalBody = document.getElementById('modal-body');
    const modalTitle = document.getElementById('modal-title');

    modalTitle.textContent='Detail';
    modalBody.innerHTML ='<div class="modal-laoding">Loading..</div>';
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

            const post = data.post;
            const comments = data.comments || [];
            modalTitle.textContent = post.Title;

            let html = '';
            
            if (post.Photo) {
                html += `<img src="../image/pet_community/${post.Photo}" class="detail-img" style="width:100%; max-height:200px; object-fit:cover; border-radius:8px; margin-bottom:15px;">`;
            }

            html += `
                <div class="detail-meta" style="font-size:0.9rem; color:#666; margin-bottom:15px;">
                    <p><strong>Organisation:</strong> ${post.OrgName || post.OrgID}</p>
                    <p><strong>Date:</strong> ${post.Date}</p>
                    <p><strong>Post ID:</strong> ${post.BoardID}</p>
                </div>
                <div class="detail-content-text" style="font-size:1rem; line-height:1.5; margin-bottom:20px; color:#333;">
                    ${post.Content}
                </div>
                <hr style="border:0; border-top:1px solid #eee; margin:20px 0;">
            `;

            html += `<div class="comments-section"><h5 style="margin-bottom:15px;">💬 Comments (${comments.length})</h5>`;

            if (comments.length === 0) {
                html += '<p class="no-comments" style="color:#999; font-style:italic;">No comments yet.</p>';
            } else {
                comments.forEach(c => {
                    const isReply = c.ReplyID !== null && c.ReplyID !== '';
                    html += `<div class="comment-item" style="background:#f9f9f9; padding:10px; border-radius:6px; margin-bottom:10px; ${isReply ? 'margin-left:20px; border-left:3px solid #825540;' : ''}">`;
                    
                    if (isReply) {
                        html += `<div class="reply-label" style="font-size:0.8rem; color:#825540; margin-bottom:5px;">↩ Replying to comment ${c.ReplyID}</div>`;
                    }

                    const name = c.FirstName ? `${c.ResidentID} — ${c.FirstName} ${c.LastName || ''}` : c.ResidentID;
                    html += `
                        <div class="comment-meta" style="font-size:0.8rem; color:#666; display:flex; justify-content:between; margin-bottom:5px;">
                            <span class="comment-user" style="font-weight:bold;">👤 ${name}</span>
                            <span class="comment-date" style="margin-left:auto;">${c.Date}</span>
                        </div>
                        <p class="comment-text" style="margin:0; font-size:0.9rem; color:#444;">${c.Content}</p>
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

function closeModal(){
    document.getElementById('modal-overlay').classList.remove('open');
}

function confirmDelete(boardID){
    deleteTargetID= boardID;
    document.getElementById('confirm-overlay').classList.add('open');
}

function cancelDelete(){
    deleteTargetID = null;
    document.getElementById('confirm-overlay').classList.remove('open');
}

function doDelete(){
    if(!deleteTargetID)return;
    const formData = new FormData();
    formData.append('action','delete');
    formData.append('boardID',deleteTargetID);

    fetch(window.location.href,{method:'POST', body: formData})
     .then(response => response.json())
        .then(data => {
            if (data.success) {
                const card = document.getElementById(`post-${deleteTargetID}`);
                if (card) {
                    card.style.transition = 'opacity 0.3s, transform 0.3s';
                    card.style.opacity = '0';
                    card.style.transform = 'scale(0.9)';
                    setTimeout(() => { card.remove(); }, 300);
                }
            } else {
                alert('Failed to delete post. Please try again.');
            }
            cancelDelete(); 
        })
        .catch(() => {
            alert('Database connection error. Please try again.');
            cancelDelete();
        });  
}
