<?php
session_start();


$con = new mysqli("localhost", "root", "", "furever_pet_home");
if ($con->connect_error) {
  die("Connection failed: " . $con->connect_error);
}

if (isset($_SESSION['orgID'])) {
  $org_id = $_SESSION['orgID'];
} else {
  header("Location: ../User_Login.php");
  exit();
}


$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

  // ACTION: ADD PET
  if (isset($_POST['action_add'])) {
    $pet_name = trim($_POST['PetName'] ?? '');
    $pet_type = $_POST['PetType'] ?? '';
    $breed = trim($_POST['Breed'] ?? '') ?: 'Unknown';
    $age = intval($_POST['Age'] ?? 0);
    $location = trim($_POST['Location'] ?? '');
    $gender = $_POST['Gender'] ?? 'Male';
    $allergies = trim($_POST['Allergies'] ?? '') ?: 'None';

    // string to tinyint
    $neutered = (isset($_POST['Neutered']) && $_POST['Neutered'] === 'Yes') ? 1 : 0;
    $is_available = (isset($_POST['IsAvailable']) && $_POST['IsAvailable'] === 'Available') ? 1 : 0;

    $photo_name = "default.png";
    if (isset($_FILES['Photo']) && $_FILES['Photo']['error'] === UPLOAD_ERR_OK) {
      $filename = time() . '_' . basename($_FILES['Photo']['name']);
      $target_path = "../image/pets/" . $filename;
      if (move_uploaded_file($_FILES['Photo']['tmp_name'], $target_path)) {
        $photo_name = $filename;
      }
    }

    // generate pet
    $result = $con->query("SELECT MAX(CAST(SUBSTRING(PetID, 4) AS UNSIGNED)) as max_num FROM pet");
    $row = $result->fetch_assoc();
    $next_num = ($row['max_num'] ?? 0) + 1;
    $pet_id = "PET" . sprintf("%02d", $next_num);

    $sql = "INSERT INTO pet (PetID, OrgID, PetType, Breed, Age, Location, Neutered, Allergies, Photo, Gender, PetName, IsAvailable) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("ssssissssssi", $pet_id, $org_id, $pet_type, $breed, $age, $location, $neutered, $allergies, $photo_name, $gender, $pet_name, $is_available);

    if ($stmt->execute()) {
      echo "<script>alert('New pet added successfully!'); window.location.href=window.location.href;</script>";
      exit();
    } else {
      $error = "Failed to add pet: " . $con->error;
    }
  }

  // ACTION: EDIT PET
  if (isset($_POST['action_edit'])) {
    $pet_id = $_POST['PetID'] ?? '';
    $pet_name = trim($_POST['PetName'] ?? '');
    $pet_type = $_POST['PetType'] ?? '';
    $breed = trim($_POST['Breed'] ?? '') ?: 'Unknown';
    $age = intval($_POST['Age'] ?? 0);
    $location = trim($_POST['Location'] ?? '');
    $gender = $_POST['Gender'] ?? 'Male';
    $allergies = trim($_POST['Allergies'] ?? '') ?: 'None';

    // change form to tinyint
    $neutered = (isset($_POST['Neutered']) && $_POST['Neutered'] === 'Yes') ? 1 : 0;
    $is_available = (isset($_POST['IsAvailable']) && $_POST['IsAvailable'] === 'Available') ? 1 : 0;

    if (isset($_FILES['Photo']) && $_FILES['Photo']['error'] === UPLOAD_ERR_OK) {
      $filename = time() . '_' . basename($_FILES['Photo']['name']);
      $target_path = "../image/pets/" . $filename;
      if (move_uploaded_file($_FILES['Photo']['tmp_name'], $target_path)) {
        $sql = "UPDATE pet SET PetName=?, PetType=?, Breed=?, Age=?, Location=?, Neutered=?, Allergies=?, Gender=?, IsAvailable=?, Photo=? WHERE PetID=? AND OrgID=?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("sssissssssss", $pet_name, $pet_type, $breed, $age, $location, $neutered, $allergies, $gender, $is_available, $filename, $pet_id, $org_id);
      }
    } else {
      $sql = "UPDATE pet SET PetName=?, PetType=?, Breed=?, Age=?, Location=?, Neutered=?, Allergies=?, Gender=?, IsAvailable=? WHERE PetID=? AND OrgID=?";
      $stmt = $con->prepare($sql);
      $stmt->bind_param("sssisssssss", $pet_name, $pet_type, $breed, $age, $location, $neutered, $allergies, $gender, $is_available, $pet_id, $org_id);
    }

    if ($stmt->execute()) {
      echo "<script>alert('Pet updated successfully!'); window.location.href=window.location.href;</script>";
      exit();
    } else {
      $error = "Failed to update pet: " . $con->error;
    }
  }

  // delete pet
  if (isset($_POST['action_delete'])) {
    $pet_id = $_POST['PetID'] ?? '';
    $sql = "DELETE FROM pet WHERE PetID = ? AND OrgID = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("ss", $pet_id, $org_id);

    if ($stmt->execute()) {
      echo "<script>alert('Pet removed successfully!'); window.location.href=window.location.href;</script>";
      exit();
    } else {
      $error = "Failed to delete pet: " . $con->error;
    }
  }
}

// 3. take data from ngo
$pet_list = [];
$get_pets = $con->prepare("SELECT * FROM pet WHERE OrgID = ?");
$get_pets->bind_param("s", $org_id);
$get_pets->execute();
$result_pets = $get_pets->get_result();
while ($row = $result_pets->fetch_assoc()) {
  $pet_list[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>NGO Dashboard - Manage Pets</title>
  <link rel="stylesheet" href="../css/base.css">
  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="../css/Pet_Listing.css">
</head>
<script>
  const senaraiPet = <?= json_encode($pet_list, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
  console.log(senaraiPet);
</script>
<script src="../js/PetListing.js"></script>

<body>

  <header class="top-header">
    <nav class="navbar" id="navbar">
      <div class="navbar-top">
        <a href="#" class="nav-logo">
          <img src="../image/icons/logo.png" alt="Furever Pet Home">
          <span>Furever Pet Home </span>
        </a>
        <div class="nav-right">
          <button class="notif-btn" title="Notifications" onclick="window.location.href='inbox.php';">🔔<span
              class="notif-dot"></span></button>
          <div class="profile-dropdown">
            <div class="avatar" title="My Profile" onclick="toggleProfileDropdown()" style="cursor:pointer;">
              <?php echo htmlspecialchars(strtoupper(substr($org_id, 0, 2))); ?>
            </div>
            <div class="dropdown-menu" id="profileDropdown">
              <div class="dropdown-user-info">
                <strong><?php echo htmlspecialchars($org_id); ?></strong>
                <span>NGO Account</span>
              </div>
              <form method="post" action="../logout.php" style="margin:0;">
                <button type="submit" class="logout-btn">&#128274; Log Out</button>
              </form>
            </div>
          </div>
        </div>
      </div>

      <div class="nav-links">
        <a href="Pet_listing.php" class="nav-tab"> Home</a>
        <a href="inbox.php" class="nav-tab"> Inbox</a>
        <a href="findapet.php" class="nav-tab"> Find A Pet</a>
        <a href="petcommunity.php" class="nav-tab"> Pet Community</a>
        <a href="helpcenter_ngo.php" class="nav-tab"> Help Center</a>
        <a href="Analytics.php" class="nav-tab"> Analytics</a>
        <a href="report.php" class="nav-tab"> Report</a>
      </div>
    </nav>
  </header>



  <main>
    <hgroup>
      <h2>Manage Pets</h2>
      <p>List of pets registered by your organization (<?php echo htmlspecialchars($org_id); ?>)</p>
    </hgroup>

    <?php if ($error): ?>
      <p style="color: red; font-weight: bold; margin-bottom: 1rem;"><?php echo $error; ?></p>
    <?php endif; ?>

    <div class="tab-row" role="group" aria-label="Filter type of pet">
      <button class="active" onclick="tapis('all', this)">All</button>
      <button onclick="tapis('Cat', this)">Cat</button>
      <button onclick="tapis('Dog', this)">Dog</button>
    </div>

    <section id="senarai-pet" aria-label="List Of Pet">
    </section>
  </main>

  <button id="btn-tambah" onclick="bukaModalTambah()" aria-label="Add New Pet" title="Add Pet">
    <svg viewBox="0 0 24 24">
      <line x1="12" y1="5" x2="12" y2="19" />
      <line x1="5" y1="12" x2="19" y2="12" />
    </svg>
  </button>

  <dialog id="modal-tambah">
    <header>
      <div class="modal-icon">🐾</div>
      <div class="modal-heading">
        <h2>Add New Pet</h2>
        <span class="modal-subtitle">Fill in the details below to list a new pet</span>
      </div>
      <button type="button" onclick="tutupModalTambah()" aria-label="Close">
        <svg viewBox="0 0 24 24">
          <line x1="18" y1="6" x2="6" y2="18" />
          <line x1="6" y1="6" x2="18" y2="18" />
        </svg>
      </button>
    </header>
    <form id="form-tambah" method="post" action="" enctype="multipart/form-data">
      <input type="hidden" name="action_add" value="1">

      <fieldset>
        <legend>Identity</legend>
        <div class="signup-field">
          <label>Pet Name *</label>
          <input type="text" name="PetName" required placeholder="e.g. Mochi">
        </div>
        <div class="modal-row">
          <div class="signup-field">
            <label>Pet Type *</label>
            <select name="PetType" required>
              <option value="Cat">Cat</option>
              <option value="Dog">Dog</option>
            </select>
          </div>
          <div class="signup-field">
            <label>Gender *</label>
            <select name="Gender" required>
              <option value="Male">Male</option>
              <option value="Female">Female</option>
            </select>
          </div>
        </div>
      </fieldset>

      <fieldset>
        <legend>Physical Details</legend>
        <div class="modal-row">
          <div class="signup-field">
            <label>Breed</label>
            <input type="text" name="Breed" placeholder="e.g. Persian">
          </div>
          <div class="signup-field">
            <label>Age (months)</label>
            <input type="number" name="Age" placeholder="e.g. 6" min="0">
          </div>
        </div>
        <div class="signup-field">
          <label>Location (Area/City) *</label>
          <input type="text" name="Location" required placeholder="e.g. Bandar Klang, Selangor">
        </div>
      </fieldset>

      <fieldset>
        <legend>Care &amp; Status</legend>
        <div class="modal-row">
          <div class="signup-field">
            <label>Neutered</label>
            <select name="Neutered">
              <option value="No">No</option>
              <option value="Yes">Yes</option>
            </select>
          </div>
          <div class="signup-field">
            <label>Status</label>
            <select name="IsAvailable">
              <option value="Available">Available</option>
              <option value="In Progress">In Progress</option>
              <option value="Adopted">Adopted</option>
            </select>
          </div>
        </div>
        <div class="signup-field">
          <label>Allergies / Medical Notes</label>
          <input type="text" name="Allergies" placeholder="None">
        </div>
      </fieldset>

      <fieldset>
        <legend>Photo</legend>
        <div class="signup-field">
          <label>Pet Photo</label>
          <input type="file" name="Photo" accept="image/*">
        </div>
      </fieldset>

      <div class="modal-footer">
        <button type="button" class="btn-batal" onclick="tutupModalTambah()">Cancel</button>
        <button type="submit" class="btn-simpan">Save Pet</button>
      </div>
    </form>
  </dialog>


  <dialog id="modal-edit">
    <header>
      <div class="modal-icon">🐾</div>
      <div class="modal-heading">
        <h2>Edit Pet Information</h2>
        <span class="modal-subtitle">Update the details for this pet record</span>
      </div>
      <button type="button" onclick="tutupModalEdit()" aria-label="Close">
        <svg viewBox="0 0 24 24">
          <line x1="18" y1="6" x2="6" y2="18" />
          <line x1="6" y1="6" x2="18" y2="18" />
        </svg>
      </button>
    </header>
    <form id="form-edit" method="post" action="" enctype="multipart/form-data">
      <input type="hidden" name="action_edit" value="1">
      <input type="hidden" id="edit-id" name="PetID">

      <fieldset>
        <legend>Identity</legend>
        <div class="signup-field">
          <label>Pet Name *</label>
          <input type="text" id="edit-nama" name="PetName" required>
        </div>
        <div class="modal-row">
          <div class="signup-field">
            <label>Pet Type *</label>
            <select id="edit-jenis" name="PetType" required>
              <option value="Cat">Cat</option>
              <option value="Dog">Dog</option>
            </select>
          </div>
          <div class="signup-field">
            <label>Gender *</label>
            <select id="edit-gender" name="Gender" required>
              <option value="Male">Male</option>
              <option value="Female">Female</option>
            </select>
          </div>
        </div>
      </fieldset>

      <fieldset>
        <legend>Physical Details</legend>
        <div class="modal-row">
          <div class="signup-field">
            <label>Breed</label>
            <input type="text" id="edit-baka" name="Breed">
          </div>
          <div class="signup-field">
            <label>Age (months)</label>
            <input type="number" id="edit-umur" name="Age" min="0">
          </div>
        </div>
        <div class="signup-field">
          <label>Location *</label>
          <input type="text" id="edit-lokasi" name="Location" required>
        </div>
      </fieldset>

      <fieldset>
        <legend>Care &amp; Status</legend>
        <div class="modal-row">
          <div class="signup-field">
            <label>Neutered</label>
            <select id="edit-neutered" name="Neutered">
              <option value="No">No</option>
              <option value="Yes">Yes</option>
            </select>
          </div>
          <div class="signup-field">
            <label>Status</label>
            <select id="edit-status" name="IsAvailable">
              <option value="Available">Available</option>
              <option value="In Progress">In Progress</option>
              <option value="Adopted">Adopted</option>
            </select>
          </div>
        </div>
        <div class="signup-field">
          <label>Allergies / Medical Notes</label>
          <input type="text" id="edit-allergies" name="Allergies">
        </div>
      </fieldset>

      <fieldset>
        <legend>Photo</legend>
        <div class="signup-field">
          <label>Update Photo (Leave empty to maintain current image)</label>
          <input type="file" name="Photo" accept="image/*">
        </div>
      </fieldset>

      <div class="modal-footer">
        <button type="button" class="btn-batal" onclick="tutupModalEdit()">Cancel</button>
        <button type="submit" class="btn-simpan">Update Info</button>
      </div>
    </form>
  </dialog>

  <form id="form-delete" method="post" action="" style="display:none;">
    <input type="hidden" name="action_delete" value="1">
    <input type="hidden" id="delete-id" name="PetID">
  </form>

  <footer>
    <div class="footer-grid">
      <div>
        <div style="font-size:2rem;">🐾</div>
        <div class="footer-brand-name">Furever Pet Home</div>
        <p class="footer-tagline">A compassionate digital hub for stray pet adoption and community care in Bandar Klang,
          Selangor.</p>
      </div>
      <div>
        <p class="footer-col-title">Platform</p>
        <ul class="footer-links-list">
          <li><a href="inbox.php">Inbox</a></li>
          <li><a href="findapet.php">Find A Pet</a></li>
          <li><a href="petcommunity.php">Pet Comunity</a></li>
          <li><a href="helpcenter_ngo.php">Help Center</a></li>
          <li><a href="Analytics.php">Analytics</a></li>
          <li><a href="Report.php">Report Animal</a></li>
        </ul>
        </ul>
      </div>

      <div>
        <p class="footer-col-title">Contact</p>
        <ul class="footer-links-list">
          <li><a href="#">41700 Bandar Klang, Selangor</a></li>
          <li><a href="mailto:info@fureverpethome.com">info@fureverpethome.com</a></li>
          <li><a href="#">+60 123-456-7890</a></li>
          <li><a href="#">Facebook · Instagram · X</a></li>
        </ul>
      </div>
    </div>
    <div class="footer-bottom">
      <span>© 2026 Furever Pet Home — Urban Pet Adoption & Community Management</span>
      <span>Made with ❤️ for Bandar Klang</span>
    </div>
  </footer>


</body>

</html>
