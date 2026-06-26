// toggle between table resident ngo
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

// switching dari view to edit mode
function editAccount(rowId) {
  const row = document.getElementById(rowId);
  if (!row) return;

  row.querySelectorAll('.view-mode').forEach(el => el.style.display = 'none');
  row.querySelectorAll('.edit-mode').forEach(el => el.style.display = '');
}

// confirming before truly delete
function confirmDelete(label) {
  return confirm("Permanently delete " + label + "? This cannot be undone.");
}
