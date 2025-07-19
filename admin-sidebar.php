<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sidebar</title>
</head>

<body>

  <div class="sidebar" id="sidebar">
    <div class="sidebar-header">
      <h2>🍽️ FoodFusion</h2>
      <p>Admin Dashboard</p>
    </div>
    <ul class="sidebar-nav">
      <li>
        <a href="admin-dashboard.php" class="nav-link">
          📊 Dashboard
        </a>
      </li>
      <li>
        <a href="admin-recipe.php" class="nav-link">
          🍽️ Recipes
        </a>
      </li>
      <li>
        <a href="admin-cuisine.php" class="nav-link">
          🌍 Cuisines
        </a>
      </li>
      <li>
        <a href="admin-dietary.php" class="nav-link">
          🥗 Dietary Preferences
        </a>
      </li>
      <li>
        <a href="admin-difficulty.php" class="nav-link">
          📈 Difficulty Levels
        </a>
      </li>
      <li>
        <a href="admin-contact.php" class="nav-link">
          📧 Contacts
        </a>
      </li>
      <li>
        <a href="admin-media.php" class="nav-link">
          🎬 Media
        </a>
      </li>
    </ul>
  </div>

</body>

</html>

<script>
  const currentPage = window.location.pathname.split("/").pop();

  document.querySelectorAll('.nav-link').forEach(link => {
    const linkPage = link.getAttribute('href');
    if (linkPage === currentPage) {
      link.classList.add('active');
    } else {
      link.classList.remove('active');
    }
  });
</script>