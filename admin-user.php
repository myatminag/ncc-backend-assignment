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
  $where[] = "u.username LIKE ?";
  $params[] = "%" . $search . "%";
}

$where_sql = '';
if (!empty($where)) {
  $where_sql = "WHERE " . implode(" AND ", $where);
}

$sql = "
  SELECT 
    u.id,
    u.username,
    u.email,
    u.phone,
    ur.role
  FROM user AS u
  LEFT JOIN user_roles AS ur ON u.role_id = ur.id
  $where_sql
  GROUP BY u.id, u.username, u.email, u.phone
";

$stmt = $connection->prepare($sql);

if (!empty($params)) {
  $types = str_repeat('s', count($params));
  $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
$users = [];

while ($row = $result->fetch_assoc()) {
  $users[] = $row;
}

// Delete User
if (isset($_GET['delete'])) {
  $delete_id = intval($_GET['delete']);
  $connection->query("DELETE FROM user WHERE id = $delete_id");
  header("Location: admin-user.php");
  exit;
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/admin.css">
  <title>Users Management</title>
</head>

<body>

  <?php include "admin-sidebar.php"; ?>

  <section class="main-content">
    <div class="header">
      <h1>Users Management</h1>
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
          <h2 class="section-title">Users Management</h2>
        </div>
        <div class="section-content">
          <form class="controls">
            <input type="text" name="search" class="search-box" placeholder="Search dietary..."
              value="<?= htmlspecialchars($search) ?>">

            <button type="submit" class="btn-primary">Filter</button>
            <a href="admin-user.php" class="btn-secondary">
              Clear
            </a>
          </form>
          <table class="data-table">
            <thead>
              <tr>
                <th>Username</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Role</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($users)): ?>
                <tr>
                  <td colspan="6">No users found.</td>
                </tr>
              <?php else: ?>
                <?php foreach ($users as $user): ?>
                  <tr>
                    <td style="text-transform: capitalize;">
                      <?= htmlspecialchars($user['username']) ?>
                    </td>
                    <td>
                      <?= htmlspecialchars($user['email']) ?>
                    </td>
                    <td>
                      <?= htmlspecialchars($user['phone']) ?>
                    </td>
                    <td style="text-transform: capitalize;">
                      <?= htmlspecialchars($user['role']) ?>
                    </td>
                    <td>
                      <?php if (strtolower($user['role']) !== 'admin'): ?>
                        <button class="action-btn btn-delete" onclick="deleteUser(<?= $user['id'] ?>)">Delete</button>
                      <?php else: ?>
                        <em>Protected</em>
                      <?php endif; ?>
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
</body>

</html>

<script>
  function deleteUser(id) {
    if (confirm("Are you sure you want to delete this user?")) {
      window.location.href = `admin-user.php?delete=${id}`;
    }
  }
</script>