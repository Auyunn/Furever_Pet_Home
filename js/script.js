//====HomePage Unregistered====
let currentSlide = 0;

    function showSlide(n) {
        let slides = document.querySelectorAll(".slides");
        if (slides.length === 0) return; 
        slides.forEach(s => s.style .display = "none");
        slides[n].style.display = "block";
    }

    window.prevSlide = function()
    {
        let slides = document.querySelectorAll(".slides");
        currentSlide = (currentSlide - 1 + slides.length) % slides.length;
        showSlide(currentSlide);
    }

    window.nextSlide = function()
    {
    let slides = document.querySelectorAll(".slides");
    currentSlide = (currentSlide + 1) % slides.length;
    showSlide(currentSlide);
    }

    document.addEventListener("DOMContentLoaded", function(){
        showSlide(currentSlide);
    })


//====|Resident|====
//====== PET COMMUNITY ===============
function toggleComment(boardId) {
    var panel = document.getElementById('panel-'+ boardId);
   if(panel.style.display == 'none' || panel.style.display ==''){
    panel.style.display='block';
   }else{
    panel.style.display='none';
   }
}

//====INBOX RESIDENT====
    //Expanse Arrow
    function toggleGroup(id) {
    const items = document.getElementById(id);
    const arrow = document.getElementById('arrow-' + id);

    if (items.style.display === "none") {
        items.style.display = 'flex';
        arrow.textContent = '▾';
    } else {
        items.style.display = 'none';
        arrow.textContent = '▸';
    }
    }

    const statusColor = {
    "Approved": "green",
    "Rejected": "red",
    "Pending": "orange",
    "In Progress": "blue"
    };

    function openNotif(event, index, group) {
    document.querySelectorAll('.notif-item')
        .forEach(item => item.classList.remove('active'));

    event.currentTarget.classList.add('active');

    const data = window.notifData[group][index];

    document.getElementById('notif-content').innerHTML = `
        <div class="content-title">${data.Title}</div>
        <div class="content-time">${data.CreatedAt}</div>
        <div class="content-status" style="color:${statusColor[data.Status]}; font-weight:700; margin-bottom:16px;">
        ● ${data.Status.toUpperCase()}
        </div>
        <div class="content-body">${data.Message}</div>
    `;
    }

//====|NGO|====
//====INBOX NGOS====
   // apply filter reload page
    //====|NGO|====
    window.applyFilter = function() 
    {
        const filter = document.getElementById('filter').value;
        const url = new URL(window.location.href);
        url.searchParams.set('filter', filter);
        window.location.href = url.toString();
    }

    window.viewApp = function(adoptionID) 
    {
        fetch('getApplicant.php?id=' + encodeURIComponent(adoptionID))
            .then(r => r.text())
            .then(html => {
                const panel = document.getElementById('panel-content');
                panel.innerHTML = html;
                panel.scrollTop = 0;
                panel.dataset.currentId = adoptionID;
            })
            .catch(err => {
                document.getElementById('panel-content').innerHTML =
                    '<div style="color:#c00">Error loading details</div>';
            });
    }

    window.closePanel = function()
    {
        const panel = document.getElementById('panel-content');
        if (!panel) return;
        panel.innerHTML = '<div class="panel-empty">Click "View" on a request to see details here.</div>';
        panel.dataset.currentId = '';
    }

    window.updateStatus = function(adoptionID, newStatus)
    {
        fetch('updateStatus.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: adoptionID, status: newStatus })
        })
        .then(r => {
            if (!r.ok) throw new Error('Network response not ok');
            return r.json();
        })
        .then(data => {
            if (!data.ok) {
                alert('Update failed: ' + (data.error || 'unknown'));
                return;
            }

            // JANGAN row.remove() -- NGO still nampak row untuk Undo
            window.updateRowBadge(adoptionID, newStatus);

            const panel = document.getElementById('panel-content');
            if (panel && panel.dataset.currentId === String(adoptionID)) {
                window.viewApp(adoptionID);
            }
        })
        .catch(e => {
            console.error(e);
            alert('Network error');
        });
    }

    window.updateRowBadge = function(adoptionID, newStatus)
    {
        const row = document.getElementById('row-' + adoptionID);
        if (!row) return;

        const badge = row.querySelector('td span');
        if (badge) {
            badge.textContent = newStatus;
            badge.className = newStatus === 'Approved' ? 'badge_approved'
                            : newStatus === 'Rejected'  ? 'badge_rejected'
                            : 'badge_pending';
        }

        const approveBtn = row.querySelector('.btn-approve, .btn-undo');
        const rejectBtn  = row.querySelector('.btn-reject');

        if (newStatus === 'Approved') {
            if (approveBtn) {
                approveBtn.textContent = 'Undo';
                approveBtn.className = 'btn-undo';
                approveBtn.disabled = false;
                approveBtn.onclick = () => {
                    const petID = row.dataset.petid;
                    fetch('unhide.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ petID })
                    })
                    .then(r => r.json())
                    .then(res => {
                        if (res.ok) {
                            window.updateRowBadge(adoptionID, 'Pending');
                        } else {
                            alert('Undo failed: ' + (res.error || 'unknown'));
                        }
                    })
                    .catch(err => alert('Network error during undo'));
                };
            }
            if (rejectBtn) {
                rejectBtn.disabled = true;
                rejectBtn.classList.add('btn-disabled');
            }

        } else if (newStatus === 'Rejected') {
            if (rejectBtn) {
                rejectBtn.textContent = 'Rejected';
                rejectBtn.disabled = true;
                rejectBtn.classList.add('btn-disabled');
            }
            if (approveBtn) {
                approveBtn.textContent = 'Approve';
                approveBtn.className = 'btn-approve';
                approveBtn.disabled = false;
                approveBtn.onclick = () => window.updateStatus(adoptionID, 'Approved');
            }

        } else {
            if (approveBtn) {
                approveBtn.textContent = 'Approve';
                approveBtn.className = 'btn-approve';
                approveBtn.disabled = false;
                approveBtn.onclick = () => window.updateStatus(adoptionID, 'Approved');
            }
            if (rejectBtn) {
                rejectBtn.textContent = 'Reject';
                rejectBtn.disabled = false;
                rejectBtn.classList.remove('btn-disabled');
                rejectBtn.onclick = () => window.updateStatus(adoptionID, 'Rejected');
            }
        }
    }

    


//====|ADMIN|====
//====ADMIN DASHBOARD====
