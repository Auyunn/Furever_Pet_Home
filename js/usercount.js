function showTable(tableId) {
  document.getElementById('residents').style.display = (tableId === 'residents') ? 'block' : 'none';
  document.getElementById('ngos').style.display = (tableId === 'ngos') ? 'block' : 'none';
}

function editAccount(accountId) {
  alert("Editing account: " + accountId);
  // Later: open a modal or inline form for editing
}

function deleteAccount(accountId) {
  var card = document.getElementById(accountId);
  if (card) {
    card.remove();
    alert("Account " + accountId + " deleted.");
  }
}

function saveAccount(accountId) {
  alert("Saving changes for: " + accountId);
  // Later: send AJAX request to backend (PHP/MySQL or C# API)
}

function goToAddPage() {
  window.location.href = 'add_account.html';
}
