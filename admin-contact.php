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
    c.phone,
    c.email,
    c.subject,
    c.message
  FROM contact AS c
  $where_sql
  GROUP BY c.id, c.name, c.phone, c.email, c.subject, c.message
";

$stmt = $connection->prepare($sql);

if (!empty($params)) {
  $types = str_repeat('s', count($params));
  $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
$contacts = [];

while ($row = $result->fetch_assoc()) {
  $contacts[] = $row;
}

// Delete Contact
if (isset($_GET['delete'])) {
  $delete_id = intval($_GET['delete']);
  $connection->query("DELETE FROM contact WHERE id = $delete_id");
  header("Location: admin-contact.php");
  exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/admin.css">
  <title>Contact Management</title>
</head>

<body>

  <?php include "admin-sidebar.php"; ?>

  <section class="main-content">
    <div class="header">
      <h1>Contact Management</h1>
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
          <h2 class="section-title">Contact Management</h2>
        </div>
        <div class="section-content">
          <form class="controls">
            <input type="text" name="search" class="search-box" placeholder="Search user..."
              value="<?= htmlspecialchars($search) ?>">

            <button type="submit" class="btn-primary">Filter</button>
            <a href="admin-contact.php" class="btn-secondary">
              Clear
            </a>
          </form>
          <table class="data-table">
            <thead>
              <tr>
                <th>Name</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Subject</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($contacts)): ?>
                <tr>
                  <td colspan="6">No contacts found.</td>
                </tr>
              <?php else: ?>
                <?php foreach ($contacts as $contact): ?>
                  <tr>
                    <td style="text-transform: capitalize;">
                      <?= htmlspecialchars($contact['name']) ?>
                    </td>
                    <td>
                      <?= htmlspecialchars($contact['phone']) ?>
                    </td>
                    <td>
                      <?= htmlspecialchars($contact['email']) ?>
                    </td>
                    <td>
                      <?= htmlspecialchars($contact['subject']) ?>
                    </td>
                    <td>
                      <button class="action-btn btn-edit"
                        onclick="detailContactModal(<?= $contact['id'] ?>)">Detail</button>
                      <button class="action-btn btn-delete"
                        onclick="deleteDifficulty(<?= $contact['id'] ?>)">Delete</button>
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

  <div id="contactModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title" id="contactModalTitle">Contact Detail</h3>
        <button class="close" onclick="closeModal('contactModal')">&times;</button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label class="form-label">Name</label>
          <p id="modalContactName"></p>
        </div>
        <div class="form-group">
          <label class="form-label">Phone</label>
          <p id="modalContactPhone"></p>
        </div>
        <div class="form-group">
          <label class="form-label">Email</label>
          <p id="modalContactEmail"></p>
        </div>
        <div class="form-group">
          <label class="form-label">Subject</label>
          <p id="modalContactSubject"></p>
        </div>
        <div class="form-group">
          <label class="form-label">Message</label>
          <p id="modalContactMessage"></p>
        </div>
        <div class="form-actions">
          <button type="button" class="btn-secondary" onclick="closeModal()">Close</button>
        </div>
      </div>
    </div>
  </div>
</body>

</html>

<script>
  let contacts = <?= json_encode($contacts ?? []) ?>;

  function detailContactModal(id) {
    const modal = document.getElementById('contactModal');
    const contact = contacts.find(c => c.id == id);
    if (!contact) return;

    document.getElementById('modalContactName').innerText = contact.name;
    document.getElementById('modalContactPhone').innerText = contact.phone;
    document.getElementById('modalContactEmail').innerText = contact.email;
    document.getElementById('modalContactSubject').innerText = contact.subject;
    document.getElementById('modalContactMessage').innerText = contact.message;

    modal.style.display = 'block';
    document.body.classList.add('modal-open');
  }

  function closeModal() {
    document.getElementById("contactModal").style.display = 'none';
    document.body.classList.remove('modal-open');
  }

  function deleteDifficulty(id) {
    if (confirm("Are you sure you want to delete this contact?")) {
      window.location.href = `admin-contact.php?delete=${id}`;
    }
  }
</script>