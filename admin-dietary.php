<?php

include "connection.php";

if (isset($_SESSION["name"]) && $_SESSION["type"] && isset($_SESSION["email"])) {
  $email = $_SESSION["email"];
  $type = $_SESSION["type"];
  $username = $_SESSION["name"];
  $loggedInUserId = $_SESSION["id"];
} else {
  echo "
    <script>
      window.alert('You must be logged in to access this page. Redirecting you back...')
      window.location = 'admin-sign-up.php';
    </script>
  ";
  exit();
}

$search = $_GET['search'] ?? '';

$where = [];
$params = [];

if (!empty($search)) {
  $where[] = "d.name LIKE ?";
  $params[] = "%" . $search . "%";
}

$where_sql = '';
if (!empty($where)) {
  $where_sql = "WHERE " . implode(" AND ", $where);
}

$sql = "
  SELECT 
    d.id,
    d.name,
    d.status
  FROM dietary_preferences AS d
  $where_sql
  GROUP BY d.id, d.name, d.status
";

$stmt = $connection->prepare($sql);

if (!empty($params)) {
  $types = str_repeat('s', count($params));
  $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
$dietaries = [];

while ($row = $result->fetch_assoc()) {
  $dietaries[] = $row;
}

// Create and Edit Dietary
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["save-btn"])) {
  $id = $_POST['dietary_id'] ?? null;
  $name = $_POST['dietaryName'];
  $status = $_POST['dietaryStatus'];

  if ($id) {
    $stmt = $connection->prepare("UPDATE dietary_preferences SET name=?, status=? WHERE id=?");
    $stmt->bind_param("ssi", $name, $status, $id);
    $stmt->execute();
  } else {
    $stmt = $connection->prepare("INSERT INTO dietary_preferences (name, status) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $status);
    $stmt->execute();
    $id = $stmt->insert_id;
  }

  header("Location: admin-dietary.php");
  exit;
}

// Delete Dietary
if (isset($_GET['delete'])) {
  $delete_id = intval($_GET['delete']);
  $connection->query("DELETE FROM dietary_preferences WHERE id = $delete_id");
  header("Location: admin-dietary.php");
  exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/admin.css">
  <title>Dietary Management</title>
</head>

<body>

  <?php include "admin-sidebar.php"; ?>

  <section class="main-content">
    <div class="header">
      <h1>Dietary Management</h1>
      <div class="user-info">
        <?php if (isset($_SESSION['email'])): ?>
          <form action="admin-logout.php" method="POST">
            <button type="submit" class="btn-secondary">Logout</button>
          </form>
        <?php endif; ?>
      </div>
    </div>

    <div class="section">
      <div class="content-section">
        <div class="section-header">
          <h2 class="section-title">Dietary Management</h2>
          <button class="add-btn" onclick="openDietaryModal()">+ Add Dietary</button>
        </div>
        <div class="section-content">
          <form class="controls">
            <input type="text" name="search" class="search-box" placeholder="Search dietary..."
              value="<?= htmlspecialchars($search) ?>">

            <button type="submit" class="btn-primary">Filter</button>
            <a href="admin-dietary.php" class="btn-secondary">
              Clear
            </a>
          </form>
          <table class="data-table">
            <thead>
              <tr>
                <th>Cuisine Name</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($dietaries)): ?>
                <tr>
                  <td colspan="6">No dietaries found.</td>
                </tr>
              <?php else: ?>
                <?php foreach ($dietaries as $dietary): ?>
                  <tr>
                    <td>
                      <?= htmlspecialchars($dietary['name']) ?>
                    </td>
                    <td>
                      <span
                        class="status-badge status-<?= htmlspecialchars($dietary['status']) ?>"><?= htmlspecialchars($dietary['status']) ?></span>
                    </td>
                    <td>
                      <button class="action-btn btn-edit" onclick="editDietary(<?= $dietary['id'] ?>)">Edit</button>
                      <button class="action-btn btn-delete" onclick="deleteDietary(<?= $dietary['id'] ?>)">Delete</button>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>

          </table>
        </div>
      </div>
    </div>
  </section>

  <div id="dietaryModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title" id="dietaryModalTitle">Add New Cuisine</h3>
        <button class="close" onclick="closeModal('dietaryModal')">&times;</button>
      </div>
      <div class="modal-body">
        <form id="dietaryForm" method="post" action="admin-dietary.php" enctype="multipart/form-data">
          <input type="hidden" name="dietary_id" id="dietary_id">

          <div class="form-group">
            <label class="form-label">Dietary Name</label>
            <input type="text" id="dietaryName" name="dietaryName" class="form-input" required
              placeholder="Enter dietary name...">
          </div>

          <div class="form-group">
            <label class="form-label">Status</label>
            <select class="form-select" id="dietaryStatus" name="dietaryStatus">
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>

          <div class="form-actions">
            <button type="button" class="btn-secondary" onclick="closeModal('dietaryModal')">Cancel</button>
            <input type="submit" class="btn-primary" name="save-btn" value="Save Dietary">
          </div>
        </form>
      </div>
    </div>
  </div>
</body>

</html>

<script>
  let dietaries = <?= json_encode($dietaries ?? []) ?>;

  function openDietaryModal(id = null) {
    const modal = document.getElementById('dietaryModal');
    const title = document.getElementById('dietaryModalTitle');
    const form = document.getElementById('dietaryForm');

    if (id) {
      const dietary = dietaries.find(r => r.id == id);
      if (!dietary) return;

      title.textContent = 'Edit Dietary';
      form.dietary_id.value = dietary.id;
      form.dietaryName.value = dietary.name || '';
      form.dietaryStatus.value = dietary.status || '';

    } else {
      title.textContent = 'Add New Dietary';
      form.reset();
      form.dietary_id.value = '';
    }

    modal.style.display = 'block';
    document.body.classList.add('modal-open');
  }

  function closeModal() {
    document.getElementById("dietaryModal").style.display = 'none';
    document.body.classList.remove('modal-open');
    currentEditId = null;
  }

  function editDietary(id) {
    openDietaryModal(id);
  }

  function deleteDietary(id) {
    if (confirm("Are you sure you want to delete this dietary?")) {
      window.location.href = `admin-dietary.php?delete=${id}`;
    }
  }
</script>