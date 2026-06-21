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

// Confirmation before deactivating an account (soft delete - sets Status to
// Inactive, does not remove any data, and can be reversed by editing the
// account and setting Status back to Active).
function confirmDeactivate(label) {
  return confirm("Set " + label + " to Inactive? They will be blocked from the platform, but their data will be kept. You can reactivate them later by editing their account.");
}