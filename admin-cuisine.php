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
  $where[] = "c.name LIKE ?";
  $params[] = "%" . $search . "%";
}

$where_sql = '';
if (!empty($where)) {
  $where_sql = "WHERE " . implode(" AND ", $where);
}

$sql = "
  SELECT 
    c.id,
    c.name,
    c.status,
  COUNT(r.id) AS recipe_count
  FROM cuisine_types AS c
  LEFT JOIN recipes AS r ON r.cuisine_type_id = c.id
  $where_sql
  GROUP BY c.id, c.name, c.status
";

$stmt = $connection->prepare($sql);

if (!empty($params)) {
  $types = str_repeat('s', count($params));
  $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
$cuisines = [];

while ($row = $result->fetch_assoc()) {
  $cuisines[] = $row;
}

// Create and Edit Cuisine
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["save-btn"])) {
  $id = $_POST['cuisine_id'] ?? null;
  $name = $_POST['cuisineName'];
  $status = $_POST['cuisineStatus'];

  if ($id) {
    $stmt = $connection->prepare("UPDATE cuisine_types SET name=?, status=? WHERE id=?");
    $stmt->bind_param("ssi", $name, $status, $id);
    $stmt->execute();
  } else {
    $stmt = $connection->prepare("INSERT INTO cuisine_types (name, status) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $status);
    $stmt->execute();
    $id = $stmt->insert_id;
  }

  header("Location: admin-cuisine.php");
  exit;
}

// Delete Cuisine
if (isset($_GET['delete'])) {
  $delete_id = intval($_GET['delete']);
  $connection->query("DELETE FROM cuisine_types WHERE id = $delete_id");
  header("Location: admin-cuisine.php");
  exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/admin.css">
  <title>Cuisine Management</title>
</head>

<body>

  <?php include "admin-sidebar.php"; ?>

  <section class="main-content">
    <div class="header">
      <h1>Cuisine Management</h1>
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
          <h2 class="section-title">Cuisine Management</h2>
          <button class="add-btn" onclick="openCuisineModal()">+ Add Cuisine</button>
        </div>
        <div class="section-content">
          <form class="controls">
            <input type="text" name="search" class="search-box" placeholder="Search cuisine..."
              value="<?= htmlspecialchars($search) ?>">

            <button type="submit" class="btn-primary">Filter</button>
            <a href="admin-cuisine.php" class="btn-secondary">
              Clear
            </a>
          </form>
          <table class="data-table">
            <thead>
              <tr>
                <th>Cuisine Name</th>
                <th>Recipe Count</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($cuisines)): ?>
                <tr>
                  <td colspan="6">No cuisines found.</td>
                </tr>
              <?php else: ?>
                <?php foreach ($cuisines as $cuisine): ?>
                  <tr>
                    <td>
                      <?= htmlspecialchars($cuisine['name']) ?>
                    </td>
                    <td>
                      <?= htmlspecialchars($cuisine['recipe_count']) ?>
                    </td>
                    <td>
                      <span
                        class="status-badge status-<?= htmlspecialchars($cuisine['status']) ?>"><?= htmlspecialchars($cuisine['status']) ?></span>
                    </td>
                    <td>
                      <button class="action-btn btn-edit" onclick="editCuisine(<?= $cuisine['id'] ?>)">Edit</button>
                      <button class="action-btn btn-delete" onclick="deleteCuisine(<?= $cuisine['id'] ?>)">Delete</button>
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

  <div id="cuisineModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title" id="cuisineModalTitle">Add New Cuisine</h3>
        <button class="close" onclick="closeModal('cuisineModal')">&times;</button>
      </div>
      <div class="modal-body">
        <form id="cuisineForm" method="post" action="admin-cuisine.php" enctype="multipart/form-data">
          <input type="hidden" name="cuisine_id" id="cuisine_id">

          <div class="form-group">
            <label class="form-label">Cuisine Name</label>
            <input type="text" id="cuisineName" name="cuisineName" class="form-input" required
              placeholder="Enter cuisine name...">
          </div>

          <div class="form-group">
            <label class="form-label">Status</label>
            <select class="form-select" id="cuisineStatus" name="cuisineStatus">
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>

          <div class="form-actions">
            <button type="button" class="btn-secondary" onclick="closeModal('cuisineModal')">Cancel</button>
            <input type="submit" class="btn-primary" name="save-btn" value="Save Cuisine">
          </div>
        </form>
      </div>
    </div>
  </div>
</body>

</html>

<script>
  let cuisines = <?= json_encode($cuisines ?? []) ?>;

  function openCuisineModal(id = null) {
    const modal = document.getElementById('cuisineModal');
    const title = document.getElementById('cuisineModalTitle');
    const form = document.getElementById('cuisineForm');

    if (id) {
      const cuisine = cuisines.find(r => r.id == id);
      if (!cuisine) return;

      title.textContent = 'Edit Cuisine';
      form.cuisine_id.value = cuisine.id;
      form.cuisineName.value = cuisine.name || '';
      form.cuisineStatus.value = cuisine.status || '';

    } else {
      title.textContent = 'Add New Cuisine';
      form.reset();
      form.cuisine_id.value = '';
    }

    modal.style.display = 'block';
    document.body.classList.add('modal-open');
  }

  function closeModal() {
    document.getElementById("cuisineModal").style.display = 'none';
    document.body.classList.remove('modal-open');
    currentEditId = null;
  }

  function editCuisine(id) {
    openCuisineModal(id);
  }

  function deleteCuisine(id) {
    if (confirm("Are you sure you want to delete this cuisine?")) {
      window.location.href = `admin-cuisine.php?delete=${id}`;
    }
  }
</script>