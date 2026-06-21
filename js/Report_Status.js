// Filter Logic
function filterReports(status, btn) {
    // Update active button state
    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
    if (btn) btn.classList.add('active');

    // Store the active status globally or on the container for reference when deleting
    const container = document.getElementById('reportsContainer'); // Assuming you have a wrapper
    if (container) container.dataset.activeFilter = status;

    const cards = document.querySelectorAll('.report-card');
    let visible = 0;

    cards.forEach(card => {
        const show = status === 'all' || card.dataset.status === status;
        card.style.display = show ? 'flex' : 'none';
        if (show) visible++;
    });

    // Toggle empty state
    document.getElementById('emptyState').style.display = visible === 0 ? 'flex' : 'none';
}

// Optimized Card Removal via Event Delegation
document.addEventListener('click', (e) => {
    const removeBtn = e.target.closest('.btn-remove');
    if (!removeBtn) return;

    const card = removeBtn.closest('.report-card');
    if (!card) return;

    // Trigger animations
    card.style.opacity = '0';
    card.style.transform = 'translateX(30px)';

    // Wait for animation, remove from DOM, and re-evaluate layout
    setTimeout(() => {
        card.remove();

        // Dynamically recalculate empty state using your existing filter function
        const activeBtn = document.querySelector('.filter-btn.active');
        const currentStatus = activeBtn ? activeBtn.getAttribute('onclick').match(/'([^']+)'/)[1] : 'all';
        // Tip: Alternatively, read from container.dataset.activeFilter if saved

        // Re-run filter logic to update item counts and empty state seamlessly
        filterReports(currentStatus || 'all', activeBtn);
    }, 300);
});

// Modal Configuration
const timelines = {
    resolved: [
        { label: 'Report Filed', done: true },
        { label: 'Under Review', done: true },
        { label: 'Rescue Dispatched', done: true },
        { label: 'Resolved', done: true },
    ],
    pending: [
        { label: 'Report Filed', done: true },
        { label: 'Under Review', done: false },
        { label: 'Rescue Dispatched', done: false },
        { label: 'Resolved', done: false },
    ],
    reviewing: [
        { label: 'Report Filed', done: true },
        { label: 'Under Review', done: true },
        { label: 'Rescue Dispatched', done: false },
        { label: 'Resolved', done: false },
    ],
};

const statusLabels = {
    resolved: '✓ Resolved',
    pending: '⏳ Pending',
    reviewing: '🔍 Reviewing',
};

// Open Modal Window
function openModal(status, title, desc, location, date, id) {
    document.getElementById('modalTitle').textContent = title;
    document.getElementById('modalDesc').textContent = desc;
    document.getElementById('modalLocation').textContent = location;
    document.getElementById('modalDate').textContent = date;
    document.getElementById('modalId').textContent = id;

    const statusBar = document.getElementById('modalStatusBar');
    statusBar.className = 'modal-status-bar ' + status;

    const statusText = document.getElementById('modalStatusText');
    statusText.textContent = statusLabels[status] || status;
    statusText.className = 'modal-status-text status-badge ' + status;

    // Timeline UI Construction
    const tl = document.getElementById('modalTimeline');
    const steps = timelines[status] || [];

    tl.innerHTML = steps.map((step, i) => `
    <div class="tl-step ${step.done ? 'done' : ''}">
      <div class="tl-dot">${step.done ? '✓' : i + 1}</div>
      <div class="tl-label">${step.label}</div>
      ${i < steps.length - 1 ? '<div class="tl-line"></div>' : ''}
    </div>
  `).join('');

    document.getElementById('modalBackdrop').classList.add('open');
}

// Close Modal Window
function closeModal() {
    document.getElementById('modalBackdrop').classList.remove('open');
}