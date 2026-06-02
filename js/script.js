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
//====INBOX RESIDENT====
//==== INBOX RESIDENT ====
function toggleGroup(id) {

    const panel = document.getElementById(id);
    const arrow = document.getElementById('arrow-' + id);

    const isHidden = panel.style.display === 'none';

    panel.style.display = isHidden ? 'flex' : 'none';

    arrow.textContent = isHidden ? '▾' : '▸';
}

function getStatusInfo(status) {

    if (!status) {
        return {
            label: '',
            cls: ''
        };
    }

    const map = {

        'Approve': {
            label: 'Diluluskan',
            cls: 'approve',
            dot: '●'
        },

        'Reject': {
            label: 'Ditolak',
            cls: 'reject',
            dot: '●'
        },

        'Pending': {
            label: 'Dalam Semakan',
            cls: 'pending',
            dot: '●'
        },

        'In Progress': {
            label: 'Sedang Diurus',
            cls: 'in-progress',
            dot: '●'
        },

        'Resolve': {
            label: 'Selesai',
            cls: 'resolve',
            dot: '●'
        }
    };

    return map[status] || {
        label: status,
        cls: 'pending',
        dot: '●'
    };
}

function formatDateTime(dtStr) {

    if (!dtStr) return '';

    const d = new Date(dtStr);

    return d.toLocaleString('ms-MY', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function openNotif(event, index, group) {

    document.querySelectorAll('.notif-item')
        .forEach(el => el.classList.remove('active'));

    const clickedItem =
        document.getElementById('item-' + group + '-' + index);

    if (clickedItem) {
        clickedItem.classList.add('active');
    }

    const notif = window.notifData[group][index];

    if (!notif) return;

    const status = getStatusInfo(notif.Status);

    const panel =
        document.getElementById('notif-content');

    panel.innerHTML = `
        <div class="content-wrapper">

            <div class="content-type-badge">
                ${notif.Type || ''}
            </div>

            <h2 class="content-title">
                ${notif.Title || ''}
            </h2>

            <div class="content-time">
                ${formatDateTime(notif.DateTime)}
            </div>

            <div class="content-status ${status.cls}">
                ${status.dot} ${status.label}
            </div>

            <hr class="content-divider">

            <div class="content-body">
                ${notif.Message || ''}
            </div>

        </div>
    `;
}

//====== PET COMMUNITY ===============
function toggleComment(boardId) {
    var panel = document.getElementById('panel-'+ boardId);
   if(panel.style.display == 'none' || panel.style.display ==''){
    panel.style.display='block';
   }else{
    panel.style.display='none';
   }
}

//====RESIDENT HELP CENTER====
//==== RESIDENT HELP CENTER ALERT ====
if (typeof triggerAlert !== 'undefined' && triggerAlert === true) {
    alert("Sorry, no results found for your search. Please try again with different keywords or try contacting our customer service.");
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
