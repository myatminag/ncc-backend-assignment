<?php

include "connection.php";

$recipesQuery = "SELECT COUNT(*) AS total FROM recipes";
$recipesResult = mysqli_query($connection, $recipesQuery);
$totalRecipes = mysqli_fetch_assoc($recipesResult)['total'];

$cuisineQuery = "SELECT COUNT(*) AS total FROM cuisine_types";
$cuisineResult = mysqli_query($connection, $cuisineQuery);
$totalCuisines = mysqli_fetch_assoc($cuisineResult)['total'];

$contactsQuery = "SELECT COUNT(*) AS total FROM contact";
$contactsResult = mysqli_query($connection, $contactsQuery);
$totalContacts = mysqli_fetch_assoc($contactsResult)['total'];

$usersQuery = "SELECT COUNT(*) AS total FROM user";
$usersResult = mysqli_query($connection, $usersQuery);
$totalUsers = mysqli_fetch_assoc($usersResult)['total'];

$mediasQuery = "SELECT COUNT(*) AS total FROM media";
$mediasResult = mysqli_query($connection, $mediasQuery);
$totalMedia = mysqli_fetch_assoc($mediasResult)['total'];

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/admin.css">
  <title>Admin Dashboard</title>
</head>

<body>

  <?php include "admin-sidebar.php"; ?>

  <section class="main-content">
    <div class="header">
      <h1>Dashboard Overview</h1>
      <div class="user-info">
        <span>Welcome, Admin</span>
        <div class="user-avatar">A</div>
      </div>
    </div>

    <div id="dashboard-section" class="section">
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon recipes">🍽️</div>
          <div class="stat-info">
            <h3>
              <?php echo $totalRecipes; ?>
            </h3>
            <p>Total Recipes</p>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon cuisines">🌍</div>
          <div class="stat-info">
            <h3 id="totalCuisines">
              <?php echo $totalCuisines; ?>
            </h3>
            <p>Cuisine Types</p>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon contacts">📧</div>
          <div class="stat-info">
            <h3 id="totalContacts">
              <?php echo $totalContacts; ?>
            </h3>
            <p>Contact Messages</p>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon users">👥</div>
          <div class="stat-info">
            <h3 id="totalUsers">
              <?php echo $totalUsers; ?>
            </h3>
            <p>Registered Users</p>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon users">🎬</div>
          <div class="stat-info">
            <h3 id="totalMedia">
              <?php echo $totalMedia; ?>
            </h3>
            <p>Total Media</p>
          </div>
        </div>
      </div>
    </div>
  </section>

</body>

</html>