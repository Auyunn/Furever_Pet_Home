// ════════════════════════════════════════
//  ADMIN REPORT — search & status filter via Report.php?ajax=1
// ════════════════════════════════════════

const ROWS_PER_PAGE = 10;
let currentPage = 1;
let reports  = Array.isArray(serverReportsData) ? serverReportsData : [];
let filtered = [...reports];
let searchTimer = null;

function escapeHtml(str) {
  if (str === null || str === undefined) return '';
  return String(str)
    .replace(/&/g, '&amp;').replace(/</g, '&lt;')
    .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

function badgeHtml(status) {
  const cssClass = { pending: 'badge-open', reviewing: 'badge-review', resolved: 'badge-resolved' };
  const label    = { pending: 'Pending', reviewing: 'Under Review', resolved: 'Resolved' };
  return `<span class="badge ${cssClass[status] || ''}">${label[status] || status}</span>`;
}

function renderTable() {
  const tbody = document.getElementById('report-tbody');
  const startIndex = (currentPage - 1) * ROWS_PER_PAGE;
  const pageRows   = filtered.slice(startIndex, startIndex + ROWS_PER_PAGE);

  tbody.innerHTML = pageRows.length === 0
    ? `<tr class="empty-row"><td colspan="6">No reports found.</td></tr>`
    : pageRows.map(r => `
        <tr>
          <td>${escapeHtml(r.ReportID)}</td>
          <td>${escapeHtml(r.Location)}</td>
          <td>${badgeHtml(r.calculatedstatus)}</td>
          <td>${escapeHtml(r.InboxDateTime) || '—'}</td>
          <td>${escapeHtml(r.ReportDescription)}</td>
          <td>${escapeHtml(r.InboxMessage) || '—'}</td>
        </tr>
      `).join('');

  const total    = filtered.length;
  const endIndex = Math.min(startIndex + ROWS_PER_PAGE, total);
  document.getElementById('pagination-info').textContent =
    total === 0 ? 'No results' : `Showing ${startIndex + 1}–${endIndex} of ${total}`;

  const totalPages = Math.ceil(total / ROWS_PER_PAGE);
  document.getElementById('page-btns').innerHTML = Array.from({ length: totalPages }, (_, i) =>
    `<button class="page-btn${i + 1 === currentPage ? ' active' : ''}" onclick="goPage(${i + 1})">${i + 1}</button>`
  ).join('');
}

function goPage(pageNumber) {
  currentPage = pageNumber;
  renderTable();
}

async function applyFilters() {
  const searchText   = document.getElementById('search-input').value.trim();
  const statusChoice = document.getElementById('status-filter').value;

  const params = new URLSearchParams();
  if (searchText)   params.set('search', searchText);
  if (statusChoice) params.set('status', statusChoice);

  try {
    const res  = await fetch(`Add_Report.php?ajax=1&${params.toString()}`);
    const data = await res.json();
    if (data.error) {
      console.error('Server error:', data.error);
      return;
    }

    reports  = data;
    filtered = data;
    currentPage = 1;
    renderTable();
  } catch (err) {
    console.error('Search failed:', err);
  }
}

document.getElementById('search-input').addEventListener('input', () => {
  clearTimeout(searchTimer);
  searchTimer = setTimeout(applyFilters, 350);
});
document.getElementById('status-filter').addEventListener('change', applyFilters);

// Close modal when clicking outside the white box
document.querySelectorAll('.modal-overlay').forEach(overlay => {
  overlay.addEventListener('click', (event) => {
    if (event.target === overlay) {
      overlay.classList.remove('open');
    }
  });
});

function closeModal(id) {
  document.getElementById(id).classList.remove('open');
}

// First render using data PHP already provided on page load
renderTable();
