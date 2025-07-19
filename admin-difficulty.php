<?php

include "connection.php";

$search = $_GET['search'] ?? '';

$where = [];
$params = [];

if (!empty($search)) {
  $where[] = "dl.name LIKE ?";
  $params[] = "%" . $search . "%";
}

$where_sql = '';
if (!empty($where)) {
  $where_sql = "WHERE " . implode(" AND ", $where);
}

$sql = "
  SELECT 
    dl.id,
    dl.name,
    dl.status,
  COUNT(r.id) AS recipe_count
  FROM difficulty_levels AS dl
  LEFT JOIN recipes AS r ON r.difficulty_level_id = dl.id
  $where_sql
  GROUP BY dl.id, dl.name, dl.status
";

$stmt = $connection->prepare($sql);

if (!empty($params)) {
  $types = str_repeat('s', count($params));
  $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
$difficulties = [];

while ($row = $result->fetch_assoc()) {
  $difficulties[] = $row;
}

// Create and Edit Dietary
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["save-btn"])) {
  $id = $_POST['difficulty_id'] ?? null;
  $name = $_POST['difficultyLevel'];
  $status = $_POST['difficultyStatus'];

  if ($id) {
    $stmt = $connection->prepare("UPDATE difficulty_levels SET name=?, status=? WHERE id=?");
    $stmt->bind_param("ssi", $name, $status, $id);
    $stmt->execute();
  } else {
    $stmt = $connection->prepare("INSERT INTO difficulty_levels (name, status) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $status);
    $stmt->execute();
    $id = $stmt->insert_id;
  }

  header("Location: admin-difficulty.php");
  exit;
}

// Delete Dietary
if (isset($_GET['delete'])) {
  $delete_id = intval($_GET['delete']);
  $connection->query("DELETE FROM difficulty_levels WHERE id = $delete_id");
  header("Location: admin-difficulty.php");
  exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/admin.css">
  <title>Difficulty Level Management</title>
</head>

<body>

  <?php include "admin-sidebar.php"; ?>

  <section class="main-content">
    <div class="header">
      <h1>Difficulty Level Management</h1>
      <div class="user-info">
        <span>Welcome, Admin</span>
        <div class="user-avatar">A</div>
      </div>
    </div>

    <div class="section">
      <div class="content-section">
        <div class="section-header">
          <h2 class="section-title">Difficulty Level Management</h2>
          <button class="add-btn" onclick="opendifficultyModal()">+ Add Difficulty Level</button>
        </div>
        <div class="section-content">
          <form class="controls">
            <input type="text" name="search" class="search-box" placeholder="Search dietary..."
              value="<?= htmlspecialchars($search) ?>">

            <button type="submit" class="btn-primary">Filter</button>
            <a href="admin-difficulty.php" class="btn-secondary">
              Clear
            </a>
          </form>
          <table class="data-table">
            <thead>
              <tr>
                <th>Difficulty Level</th>
                <th>Recipe Count</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($difficulties)): ?>
                <tr>
                  <td colspan="6">No difficulties level found.</td>
                </tr>
              <?php else: ?>
                <?php foreach ($difficulties as $difficulty): ?>
                  <tr>
                    <td style="text-transform: capitalize;">
                      <?= htmlspecialchars($difficulty['name']) ?>
                    </td>
                    <td>
                      <?= htmlspecialchars($difficulty['recipe_count']) ?>
                    </td>
                    <td>
                      <span
                        class="status-badge status-<?= htmlspecialchars($difficulty['status']) ?>"><?= htmlspecialchars(string: $difficulty['status']) ?></span>
                    </td>
                    <td>
                      <button class="action-btn btn-edit" onclick="editDifficulty(<?= $difficulty['id'] ?>)">Edit</button>
                      <button class="action-btn btn-delete"
                        onclick="deleteDifficulty(<?= $difficulty['id'] ?>)">Delete</button>
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

  <div id="difficultyModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title" id="difficultyModalTitle">Add New Cuisine</h3>
        <button class="close" onclick="closeModal('difficultyModal')">&times;</button>
      </div>
      <div class="modal-body">
        <form id="difficultyForm" method="post" action="admin-difficulty.php" enctype="multipart/form-data">
          <input type="hidden" name="difficulty_id" id="difficulty_id">

          <div class="form-group">
            <label class="form-label">Difficulty Level</label>
            <input type="text" id="difficultyLevel" name="difficultyLevel" class="form-input" required
              placeholder="Enter difficulty level...">
          </div>

          <div class="form-group">
            <label class="form-label">Status</label>
            <select class="form-select" id="difficultyStatus" name="difficultyStatus">
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>

          <div class="form-actions">
            <button type="button" class="btn-secondary" onclick="closeModal('difficultyModal')">Cancel</button>
            <input type="submit" class="btn-primary" name="save-btn" value="Save Difficulty Level">
          </div>
        </form>
      </div>
    </div>
  </div>
</body>

</html>

<script>
  let difficulties = <?= json_encode($difficulties ?? []) ?>;

  function opendifficultyModal(id = null) {
    const modal = document.getElementById('difficultyModal');
    const title = document.getElementById('difficultyModalTitle');
    const form = document.getElementById('difficultyForm');

    if (id) {
      const difficulty = difficulties.find(r => r.id == id);
      if (!difficulty) return;

      title.textContent = 'Edit Difficulty';
      form.difficulty_id.value = difficulty.id;
      form.difficultyLevel.value = difficulty.name || '';
      form.difficultyStatus.value = difficulty.status || '';

    } else {
      title.textContent = 'Add New Difficulty';
      form.reset();
      form.difficulty_id.value = '';
    }

    modal.style.display = 'block';
    document.body.classList.add('modal-open');
  }

  function closeModal() {
    document.getElementById("difficultyModal").style.display = 'none';
    document.body.classList.remove('modal-open');
    currentEditId = null;
  }

  function editDifficulty(id) {
    opendifficultyModal(id);
  }

  function deleteDifficulty(id) {
    if (confirm("Are you sure you want to delete this difficulty level?")) {
      window.location.href = `admin-difficulty.php?delete=${id}`;
    }
  }
</script>