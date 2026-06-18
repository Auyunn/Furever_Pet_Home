// ════════════════════════════════════════
//  ADMIN REPORT — JavaScript
//  This file handles all the logic:
//  - Rendering the table rows
//  - Search & filter
//  - Delete confirm
//  - View detail popup
//  - Toast notification
//  - Pagination
// ════════════════════════════════════════


// ── STEP 1: Sample data ──
// In a real project this would come from a database.
// Each object = one report row.
let reports = [
  { id:1,  name:"Ahmad Rizal",    title:"Injured stray dog near market",    status:"open",     date:"2026-06-01", location:"Jalan Besar, Klang",        desc:"Dog was limping badly near the wet market. Needs immediate attention." },
  { id:2,  name:"Siti Norzahra",  title:"Cat colony near school",           status:"review",   date:"2026-06-02", location:"SMK Klang Perdana",          desc:"Large colony of cats found near school entrance. Possible health risk." },
  { id:3,  name:"Lim Wei Sheng",  title:"Abandoned litter of kittens",      status:"resolved", date:"2026-06-03", location:"Taman Bahagia, Klang",       desc:"Four kittens found in a box by the roadside. Taken to shelter." },
  { id:4,  name:"Priya Nair",     title:"Dog stuck in drain",               status:"open",     date:"2026-06-03", location:"Lorong Merbah 3",            desc:"Medium-sized dog trapped in a drain canal after heavy rain." },
  { id:5,  name:"Farah Hanum",    title:"Sick rabbit near playground",      status:"open",     date:"2026-06-04", location:"Taman Sri Andalas",          desc:"Rabbit found unresponsive near children playground." },
  { id:6,  name:"Tan Ah Kow",     title:"Aggressive stray dog pack",        status:"review",   date:"2026-06-04", location:"Kawasan Perindustrian Meru",  desc:"Pack of 5–6 dogs acting aggressively toward pedestrians." },
  { id:7,  name:"Nurul Asyikin",  title:"Injured bird found",               status:"resolved", date:"2026-06-05", location:"Persiaran Raja Muda Musa",   desc:"Bird with broken wing found on the roadside." },
  { id:8,  name:"Mohd Aizat",     title:"Stray cat in drain",               status:"open",     date:"2026-06-05", location:"Jalan Goh Hock Huat",        desc:"Cat found trapped inside a closed drain. Meowing loudly." },
  { id:9,  name:"Lee Mei Xin",    title:"Dog with collar, no owner found",  status:"review",   date:"2026-06-06", location:"Bandar Botanik",             desc:"Dog with collar wandering alone for 2 days. No owner responding." },
  { id:10, name:"Zulaikha Rahim", title:"Cat hit by vehicle",               status:"resolved", date:"2026-06-07", location:"Lebuhraya Klang",            desc:"Cat found injured on the highway shoulder. Rushed to vet." },
];


// ── STEP 2: Settings ──
const ROWS_PER_PAGE = 10;   // how many rows to show per page
let currentPage = 1;         // which page we are on right now
let filtered = [...reports]; // copy of reports — changes when user searches/filters
let pendingDeleteId = null;  // stores the id of the report about to be deleted


// ── STEP 3: Helper — create the coloured status badge HTML ──
// Takes a status string ("open", "review", "resolved")
// Returns an HTML string like: <span class="badge badge-open">Open</span>
function badgeHtml(status) {
  const cssClass = { open: 'badge-open', review: 'badge-review', resolved: 'badge-resolved' };
  const label    = { open: 'Open',       review: 'Under Review', resolved: 'Resolved' };
  return `<span class="badge ${cssClass[status]}">${label[status]}</span>`;
}


// ── STEP 4: Render the table ──
// This function draws all the rows into <tbody id="report-tbody">
function renderTable() {

  const tbody = document.getElementById('report-tbody');

  // Work out which rows belong to the current page
  const startIndex = (currentPage - 1) * ROWS_PER_PAGE;
  const pageRows   = filtered.slice(startIndex, startIndex + ROWS_PER_PAGE);

  // If no results, show a "no reports found" message
  if (pageRows.length === 0) {
    tbody.innerHTML = `<tr class="empty-row"><td colspan="6">No reports found.</td></tr>`;

  } else {
    // Build one <tr> string per report, then join them all together
    tbody.innerHTML = pageRows.map(function(r) {
      return `
        <tr>
          <td class="td-name">${r.name}</td>
          <td class="td-title">${r.title}</td>
          <td>${badgeHtml(r.status)}</td>
          <td class="td-date">${r.date}</td>
          <td style="text-align:center;">
            <button class="btn-view" onclick="openView(${r.id})">Open</button>
          </td>
          <td>
            <button class="btn-delete" title="Delete" onclick="openDelete(${r.id})">🗑</button>
          </td>
        </tr>
      `;
    }).join('');
  }

  // Update "Showing X–Y of Z" text
  const total    = filtered.length;
  const endIndex = Math.min(startIndex + ROWS_PER_PAGE, total);
  document.getElementById('pagination-info').textContent =
    total === 0 ? 'No results' : `Showing ${startIndex + 1}–${endIndex} of ${total}`;

  // Draw page number buttons (1, 2, 3 …)
  const totalPages = Math.ceil(total / ROWS_PER_PAGE);
  const pageBtns   = document.getElementById('page-btns');
  pageBtns.innerHTML = Array.from({ length: totalPages }, function(_, i) {
    const isActive = (i + 1 === currentPage) ? ' active' : '';
    return `<button class="page-btn${isActive}" onclick="goPage(${i + 1})">${i + 1}</button>`;
  }).join('');

  // Refresh the 4 summary cards at the top
  updateCounts();
}


// ── STEP 5: Update the summary count cards ──
function updateCounts() {
  document.getElementById('count-total').textContent    = reports.length;
  document.getElementById('count-open').textContent     = reports.filter(r => r.status === 'open').length;
  document.getElementById('count-review').textContent   = reports.filter(r => r.status === 'review').length;
  document.getElementById('count-resolved').textContent = reports.filter(r => r.status === 'resolved').length;
}


// ── STEP 6: Filter function ──
// Called every time the user types in the search box or changes the dropdown
function applyFilters() {
  const searchText   = document.getElementById('search-input').value.toLowerCase();
  const statusChoice = document.getElementById('status-filter').value;

  filtered = reports.filter(function(r) {
    const matchesSearch = !searchText ||
      r.name.toLowerCase().includes(searchText) ||
      r.title.toLowerCase().includes(searchText);

    const matchesStatus = !statusChoice || r.status === statusChoice;

    return matchesSearch && matchesStatus;
  });

  currentPage = 1;   // go back to page 1 after filtering
  renderTable();
}


// ── STEP 7: Pagination — jump to a page number ──
function goPage(pageNumber) {
  currentPage = pageNumber;
  renderTable();
}


// ── STEP 8: DELETE flow ──

// 8a. Open the delete confirmation modal
function openDelete(id) {
  pendingDeleteId = id;
  const report = reports.find(r => r.id === id);
  document.getElementById('delete-name').textContent = report.name;
  document.getElementById('delete-modal').classList.add('open');
}

// 8b. When admin clicks the red "Delete" button inside the modal
document.getElementById('confirm-delete-btn').addEventListener('click', function() {
  // Remove from both arrays
  reports  = reports.filter(r => r.id !== pendingDeleteId);
  filtered = filtered.filter(r => r.id !== pendingDeleteId);

  // If the current page is now empty, go back one page
  if ((currentPage - 1) * ROWS_PER_PAGE >= filtered.length && currentPage > 1) {
    currentPage--;
  }

  closeModal('delete-modal');
  renderTable();
  showToast('Report deleted successfully.');
});


// ── STEP 9: VIEW flow ──
// Open the view detail modal and fill in the fields
function openView(id) {
  const r = reports.find(r => r.id === id);
  document.getElementById('view-name').textContent     = r.name;
  document.getElementById('view-title').textContent    = r.title;
  document.getElementById('view-status').textContent   = r.status.charAt(0).toUpperCase() + r.status.slice(1);
  document.getElementById('view-location').textContent = r.location;
  document.getElementById('view-desc').textContent     = r.desc;
  document.getElementById('view-date').textContent     = r.date;
  document.getElementById('view-modal').classList.add('open');
}


// ── STEP 10: Close any modal ──
// The id parameter matches the modal's HTML id attribute
function closeModal(id) {
  document.getElementById(id).classList.remove('open');
}

// Also close modal when user clicks the dark overlay (outside the white box)
document.querySelectorAll('.modal-overlay').forEach(function(overlay) {
  overlay.addEventListener('click', function(event) {
    if (event.target === overlay) {   // only if clicked directly on overlay, not the modal box
      overlay.classList.remove('open');
    }
  });
});


// ── STEP 11: Toast notification ──
// Shows a small popup message at bottom-right for 2.8 seconds
function showToast(message) {
  const toast = document.getElementById('toast');
  toast.textContent = message;
  toast.classList.add('show');
  setTimeout(function() {
    toast.classList.remove('show');
  }, 2800);
}


// ── STEP 12: Attach events to the search & filter controls ──
document.getElementById('search-input').addEventListener('input',  applyFilters);
document.getElementById('status-filter').addEventListener('change', applyFilters);


// ── STEP 13: First render when the page loads ──
renderTable();