// Toggle antara Users dan NGOs tables
function showTable(tableId) {
  // sembunyikan kedua-dua section dulu
  document.getElementById('residents').style.display = 'none';
  document.getElementById('ngos').style.display = 'none';

  // tunjuk section yang dipilih
  document.getElementById(tableId).style.display = 'block';

  // update gaya active pada button
  document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
  if (tableId === 'residents') {
    document.querySelector('.tab-btn:nth-child(1)').classList.add('active');
  } else {
    document.querySelector('.tab-btn:nth-child(2)').classList.add('active');
  }
}

// Edit account
function editAccount(accountId) {
  alert("Editing account: " + accountId);
  // kemudian boleh tambah modal atau form untuk edit
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
  // kemudian boleh sambung ke backend (PHP/MySQL atau API)
}

// Redirect ke Add Account page
function goToAddPage() {
  window.location.href = 'add_account.html';
}
