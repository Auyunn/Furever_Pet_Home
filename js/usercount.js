// Toggle between Users and NGOs tables
function showTable(tableId) {
  document.getElementById('residents').style.display = (tableId === 'residents') ? 'block' : 'none';
  document.getElementById('ngos').style.display = (tableId === 'ngos') ? 'block' : 'none';

  // update active button styling
  document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
  if (tableId === 'residents') {
    document.querySelector('.tab-btn:nth-child(1)').classList.add('active');
  } else {
    document.querySelector('.tab-btn:nth-child(2)').classList.add('active');
  }
}

// Edit account (placeholder for modal or inline editing)
function editAccount(accountId) {
  alert("Editing account: " + accountId);
  // Later: open a modal or inline form for editing
}

// Delete account row
function deleteAccount(accountId) {
  var row = document.getElementById(accountId);
  if (row) {
    row.remove();
    alert("Account " + accountId + " deleted.");
  }
}

// Save account changes
function saveAccount(accountId) {
  alert("Saving changes for: " + accountId);
  // Later: send AJAX request to backend (PHP/MySQL or C# API)
}

// Redirect to Add Account page
function goToAddPage() {
  window.location.href = 'add_account.html';
}
