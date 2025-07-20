<?php

include "connection.php";

$recipe = null;

if (isset($_GET['id'])) {
  $id = intval($_GET['id']); // sanitize input
  $select_query = "
    SELECT 
      r.id,
      r.name,
      r.description,
      r.ingredients,
      r.recipe_image,
      r.total_time_minutes,
      r.prep_time_minutes,
      r.cooking_time_minutes,
      dl.name AS difficulty_name,
      ct.name AS cuisine_type,
      GROUP_CONCAT(DISTINCT dp.name ORDER BY dp.name SEPARATOR ', ') AS recipe_dietary_preferences
    FROM recipes AS r
    JOIN difficulty_levels AS dl ON r.difficulty_level_id = dl.id
    JOIN cuisine_types AS ct ON r.cuisine_type_id = ct.id
    LEFT JOIN recipe_dietary_preferences AS rdp ON r.id = rdp.recipe_id
    LEFT JOIN dietary_preferences AS dp ON rdp.dietary_preference_id = dp.id
    WHERE r.id = $id
    GROUP BY r.id, r.name, r.description, r.recipe_image, r.total_time_minutes, dl.name, ct.name;
  ";

  $result = mysqli_query($connection, $select_query);

  if ($result && mysqli_num_rows($result) > 0) {
    $recipe = mysqli_fetch_assoc($result);
  } else {
    echo "Recipe not found.";
    exit;
  }
} else {
  echo "Invalid recipe ID.";
  exit;
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/style.css">
  <title>Document</title>
</head>

<body>
  <?php include "auth.php" ?>

  <?php include "header.php" ?>

  <main class="main-content">
    <div class="container">
      <div class="breadcrumb">
        <a href="index.php">Home</a> >
        <a href="recipe.php">Recipes</a> >
        <p><?= htmlspecialchars($recipe['name']) ?></p>
      </div>

      <div class="recipe-header">
        <div class="recipe-main-info">
          <h1 class="recipe-title">
            <?= htmlspecialchars($recipe['name']) ?>
          </h1>
          <p class="recipe-description">
            <?= nl2br(htmlspecialchars($recipe['description'])) ?>
          </p>

          <div class="recipe-meta-grid">
            <div class="meta-item">
              <span class="meta-icon">🌍</span>
              <div class="meta-label">Cuisine Type</div>
              <div class="meta-value">
                <?= htmlspecialchars($recipe['cuisine_type']) ?>
              </div>
            </div>
            <div class="meta-item">
              <span class="meta-icon">🍽️</span>
              <div class="meta-label">Prep Time</div>
              <div class="meta-value">
                <?= htmlspecialchars($recipe['prep_time_minutes']) ?> min
              </div>
            </div>
            <div class="meta-item">
              <span class="meta-icon">🔥</span>
              <div class="meta-label">Cook Time</div>
              <div class="meta-value">
                <?= htmlspecialchars($recipe['cooking_time_minutes']) ?> min
              </div>
            </div>
            <div class="meta-item">
              <span class="meta-icon">⏱️</span>
              <div class="meta-label">Total Time</div>
              <div class="meta-value">
                <?= htmlspecialchars($recipe['total_time_minutes']) ?> min
              </div>
            </div>
            <div class="meta-item difficulty-medium">
              <span class="meta-icon">📊</span>
              <div class="meta-label">Difficulty</div>
              <div class="meta-value" style="text-transform: capitalize;">
                <?= htmlspecialchars($recipe['difficulty_name']) ?>
              </div>
            </div>
          </div>

          <div class="dietary-preferences">
            <?php
            $dietary_tags = explode(", ", $recipe['recipe_dietary_preferences']);
            foreach ($dietary_tags as $tag) {
              echo "<span class=\"dietary-tag\">" . htmlspecialchars($tag) . "</span>";
            }
            ?>
          </div>
        </div>

        <div class="recipe-images">
          <div class="main-image" id="mainImage"
            style="background-image: url('<?= $recipe['recipe_image'] ?? '/placeholder.svg?height=300&width=400' ?>')"
            onclick="openImageModal(this)">
            <div class="image-overlay">
              <div class="image-caption"><?= htmlspecialchars($recipe['name']) ?></div>
            </div>
          </div>
        </div>
      </div>

      <div class="ingredients-section">
        <h2 class="ingredients-title">🛒 Ingredients</h2>

        <p class="ingredients-text">
          <?= htmlspecialchars($recipe['ingredients']) ?>
        </p>
      </div>
    </div>
  </main>

  <?php include "footer.php" ?>
</body>

</html>

<script src="javascript/index.js"></script>