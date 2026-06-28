//====|NGO REPORT|====

window.applyFilter = function () {
    const filter = document.getElementById('filter').value;
    const url = new URL(window.location.href);
    url.searchParams.set('filter', filter);
    window.location.href = url.toString();
}

function getReportBadgeClass(status) {
    if (status === 'Resolved') return 'badge_resolved';
    if (status === 'Pending')  return 'badge_inprogress';
    if (status === 'Submit')   return 'badge_submit';
    return 'badge_submit';
}

function getReportDisplayLabel(status) {
    if (status === 'Pending')  return 'In Progress';
    if (status === 'Resolved') return 'Resolved';
    return 'Submit';
}

window.viewReport = function (reportID) {
    const row = document.getElementById('row-' + reportID);
    if (!row) return;

    const petName      = row.dataset.pet;
    const location     = row.dataset.loc;
    const description  = row.dataset.desc;
    const dateReported = row.dataset.date;
    const status       = row.dataset.status;
    const photo        = row.dataset.photo;

    let photoHTML = '';
    if (photo) {
        photoHTML = `
        <div>
            <p class="panel-label">Photo</p>
            <img src="../image/report/${photo}" alt="Report Photo"
                style="width:100%;border-radius:14px;margin-top:0.4rem;object-fit:cover;max-height:180px;">
        </div>`;
    }

    const panel = document.getElementById('panel-content');
    panel.innerHTML = `
        <div class="panel-card">
            <div style="display:flex; justify-content:flex-end;">
                <button onclick="closeReportPanel()" class="close-btn">✕</button>
            </div>
            <div class="panel-title">${petName}</div>
            <div style="font-size:0.85rem;color:var(--text-muted);">Report ID: ${reportID}</div>
            <hr class="panel-divider">
            <div>
                <p class="panel-label">Location</p>
                <p class="panel-value">📍 ${location}</p>
            </div>
            <div>
                <p class="panel-label">Date Reported</p>
                <p class="panel-value">${dateReported}</p>
            </div>
            <div>
                <p class="panel-label">Status</p>
                <p class="panel-value">
                    <span class="${getReportBadgeClass(status)}" id="panel-badge-${reportID}">
                        ${getReportDisplayLabel(status)}
                    </span>
                </p>
            </div>
            <hr class="panel-divider">
            <div>
                <p class="panel-label">Description</p>
                <p class="panel-value">${description}</p>
            </div>
            ${photoHTML}
        </div>`;

    panel.scrollTop = 0;
    panel.dataset.currentId = reportID;
}

window.closeReportPanel = function () {
    const panel = document.getElementById('panel-content');
    if (!panel) return;
    panel.innerHTML = '<div class="panel-empty">Click "View" on a request to see details here.</div>';
    panel.dataset.currentId = '';
}

window.updateReportStatus = function (reportID, newStatus) {
    fetch('report.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'update_status', reportID: reportID, status: newStatus })
    })
    .then(r => {
        if (!r.ok) throw new Error('Network response not ok');
        return r.json();
    })
    .then(data => {
        if (!data.success) {
            alert('Update failed: ' + (data.message || 'unknown'));
            return;
        }
        updateReportRowBadge(reportID, newStatus);

        // refresh side panel if open
        const panel = document.getElementById('panel-content');
        if (panel && panel.dataset.currentId === String(reportID)) {
            window.viewReport(reportID);
        }
    })
    .catch(e => {
        console.error(e);
        alert('Network error');
    });
}

function updateReportRowBadge(reportID, newStatus) {
    const row = document.getElementById('row-' + reportID);
    if (!row) return;

    // update data attribute
    row.dataset.status = newStatus;

    // update table badge — guna display label bukan raw DB value
    const badge = document.getElementById('badge-' + reportID);
    if (badge) {
        badge.textContent = getReportDisplayLabel(newStatus);
        badge.className   = getReportBadgeClass(newStatus);
    }

    // update buttons
    const actionTd    = row.querySelector('.action-btns');
    if (!actionTd) return;
    const solveBtn    = actionTd.querySelector('.btn-solve');
    const progressBtn = actionTd.querySelector('.btn-inprogress');

    if (newStatus === 'Resolved') {
        // both disabled
        if (solveBtn)    { solveBtn.disabled = true;    solveBtn.style.opacity = '0.4';    solveBtn.style.cursor = 'not-allowed'; }
        if (progressBtn) { progressBtn.disabled = true; progressBtn.style.opacity = '0.4'; progressBtn.style.cursor = 'not-allowed'; }

    } else if (newStatus === 'Pending') {
        // Pending = In Progress — solve enabled, in progress disabled
        if (solveBtn)    { solveBtn.disabled = false;    solveBtn.style.opacity = '1';      solveBtn.style.cursor = 'pointer'; }
        if (progressBtn) { progressBtn.disabled = true;  progressBtn.style.opacity = '0.4'; progressBtn.style.cursor = 'not-allowed'; }

    } else {
        // Submit — both enabled
        if (solveBtn)    { solveBtn.disabled = false;    solveBtn.style.opacity = '1';      solveBtn.style.cursor = 'pointer'; }
        if (progressBtn) { progressBtn.disabled = false; progressBtn.style.opacity = '1';   progressBtn.style.cursor = 'pointer'; }
    }
}