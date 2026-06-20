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

// Delete confirmation before the form actually submits to PHP.
// Call this from the delete button's onclick, e.g.:
// <button type="submit" onclick="return confirmDelete('this resident');">🗑️</button>
function confirmDelete(label) {
  return confirm("Delete " + label + "? This cannot be undone.");
}