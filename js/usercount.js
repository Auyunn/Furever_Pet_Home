// Toggle between Users and NGOs tables
function showTable(tableId) {
  document.getElementById('residents').style.display = 'none';
  document.getElementById('ngos').style.display = 'none';

  document.getElementById(tableId).style.display = 'block';

  document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
  if (tableId === 'residents') {
    document.querySelector('.tab-btn:nth-child(1)').classList.add('active');
  } else {
    document.querySelector('.tab-btn:nth-child(2)').classList.add('active');
  }
}

// Switch a row from view mode to edit mode (reveals inputs + Save button)
function editAccount(rowId) {
  const row = document.getElementById(rowId);
  if (!row) return;

  row.querySelectorAll('.view-mode').forEach(el => el.style.display = 'none');
  row.querySelectorAll('.edit-mode').forEach(el => el.style.display = '');
}

// Confirmation before permanently deleting an account. This is a real,
// irreversible delete - it also removes all of that account's related
// records (applications, comments, reports, posts, etc. depending on
// account type). Wired via onsubmit="return confirmDelete('...')".
function confirmDelete(label) {
  return confirm("Permanently delete " + label + "? This cannot be undone.");
}