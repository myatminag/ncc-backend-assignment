<?php

include "connection.php";

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/style.css">
  <title>Recipes</title>
</head>

<body>
  <?php include "cookie-consent.php" ?>

  <?php include "auth.php" ?>

  <?php include "header.php" ?>

  <section class="hero">
    <div class="hero-content">
      <h1>Recipe Collection</h1>
      <p>Discover fusion recipes from around the world. From easy weeknight dinners to challenging gourmet creations,
        find your next culinary adventure.</p>
    </div>
  </section>

  <section class="recipes-section">
    <div class="container">
      <form method="GET" class="search-filter-container">
        <div class="search-box">
          <input type="text" name="search" placeholder="Search recipes..." class="search-input"
            value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
          <button class="search-btn">🔍</button>
        </div>

        <div class="filter-dropdown">
          <select name="category" id="categoryFilter" class="category-select" onchange="this.form.submit()">
            <option value="">All Categories</option>
            <?php
            $category_result = mysqli_query($connection, "SELECT id, name FROM cuisine_types ORDER BY name ASC");
            $selected_category = $_GET['category'] ?? '';

            if ($category_result && mysqli_num_rows($category_result) > 0) {
              while ($row = mysqli_fetch_assoc($category_result)) {
                $category_name = htmlspecialchars($row['name']);
                $selected = ($category_name === $selected_category) ? 'selected' : '';

                echo "
                  <option value=\"$category_name\" $selected>" . ucfirst($category_name) . "</option>
                ";
              }
            }
            ?>
          </select>
        </div>

        <div class="results-count">
          <span id="resultsCount">
            <?php
            if (!empty($_GET['search']) || !empty($_GET['category'])) {
              echo "Filtered results";
            } else {
              echo "Showing all recipes";
            }
            ?>
          </span>
        </div>
      </form>
      <div class="recipes-grid">
        <?php
        $search = mysqli_real_escape_string($connection, $_GET['search'] ?? '');
        $category = mysqli_real_escape_string($connection, $_GET['category'] ?? '');

        $where_clauses = [];

        if (!empty($search)) {
          $where_clauses[] = "r.name LIKE '%$search%'";
        }

        if (!empty($category)) {
          $where_clauses[] = "ct.name LIKE '%$category%'";
        }

        $where_sql = "";
        if (!empty($where_clauses)) {
          $where_sql = "WHERE " . implode(" AND ", $where_clauses);
        }

        $select_query = "
          SELECT 
            r.id,
            r.name,
            r.description,
            r.recipe_image,
            r.total_time_minutes,
            dl.name AS difficulty_name,
            ct.name AS cuisine_type,
            GROUP_CONCAT(DISTINCT dp.name ORDER BY dp.name SEPARATOR ', ') AS recipe_dietary_preferences
          FROM recipes AS r
          JOIN difficulty_levels AS dl ON r.difficulty_level_id = dl.id
          JOIN cuisine_types AS ct ON r.cuisine_type_id = ct.id
          LEFT JOIN recipe_dietary_preferences AS rdp ON r.id = rdp.recipe_id
          LEFT JOIN dietary_preferences AS dp ON rdp.dietary_preference_id = dp.id
          $where_sql
          GROUP BY r.id, r.name, r.description, r.recipe_image, r.total_time_minutes, dl.name;
        ";

        $select_result = mysqli_query($connection, $select_query);

        if ($select_result && mysqli_num_rows($select_result) > 0) {
          while ($recipe = mysqli_fetch_assoc($select_result)) {
            $id = htmlspecialchars($recipe['id']);
            $name = htmlspecialchars($recipe['name']);
            $description = htmlspecialchars($recipe['description']);
            $recipe_image = htmlspecialchars($recipe['recipe_image']);
            $difficulty_level = htmlspecialchars($recipe['difficulty_name']);
            $cuisine_type = htmlspecialchars($recipe['cuisine_type']);
            $total_time_minutes = htmlspecialchars($recipe['total_time_minutes']);
            $dietary_preferences = htmlspecialchars($recipe['recipe_dietary_preferences'] ?? 'None');
            $dietary_tags = array_filter(array_map('trim', explode(',', $dietary_preferences)));

            echo "
              <div class='recipe-card'>
                <div class='recipe-image' style='background-image: url(\"{$recipe_image}\");'>
                  <div class='recipe-difficulty difficulty-$difficulty_level'>" . ucfirst($difficulty_level) . "</div>
                </div>
                <div class='recipe-content'>
                  <h3 class='recipe-title'>$name</h3>
                  <p class='recipe-description'>$description</p>

                  <div class='recipe-meta'>
                    <div class='recipe-time'> 
                      <svg xmlns='http://www.w3.org/2000/svg' height='20px' viewBox='0 -960 960 960' width='24px' fill='#6c757d'><path d='M610-760q-21 0-35.5-14.5T560-810q0-21 14.5-35.5T610-860q21 0 35.5 14.5T660-810q0 21-14.5 35.5T610-760Zm0 660q-21 0-35.5-14.5T560-150q0-21 14.5-35.5T610-200q21 0 35.5 14.5T660-150q0 21-14.5 35.5T610-100Zm160-520q-21 0-35.5-14.5T720-670q0-21 14.5-35.5T770-720q21 0 35.5 14.5T820-670q0 21-14.5 35.5T770-620Zm0 380q-21 0-35.5-14.5T720-290q0-21 14.5-35.5T770-340q21 0 35.5 14.5T820-290q0 21-14.5 35.5T770-240Zm60-190q-21 0-35.5-14.5T780-480q0-21 14.5-35.5T830-530q21 0 35.5 14.5T880-480q0 21-14.5 35.5T830-430ZM480-80q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880v80q-134 0-227 93t-93 227q0 134 93 227t227 93v80Zm0-320q-33 0-56.5-23.5T400-480q0-5 .5-10.5T403-501l-83-83 56-56 83 83q4-1 21-3 33 0 56.5 23.5T560-480q0 33-23.5 56.5T480-400Z'/></svg>
                      $total_time_minutes mins
                    </div>
                    <div class='recipe-servings'>
                      <svg xmlns='http://www.w3.org/2000/svg' height='20px' viewBox='0 -960 960 960' width='24px' fill='#6c757d'><path d='M40-440v-80h81q14-127 103-216t216-103v-41h80v41q127 14 216 103t103 216h81v80H40Zm163-80h554q-14-103-92.5-171.5T480-760q-106 0-184.5 68.5T203-520Zm372 80H320v240h222q21 0 40.5-7t35.5-21l166-137q-8-8-18-12t-21-6q-17-3-33 1t-30 15l-108 87H400v-80h146l44-36q5-3 7.5-8t2.5-11q0-10-7.5-17.5T575-440Zm-335 0h-80v280h80v-280Zm0 360h-80q-33 0-56.5-23.5T80-160v-280q0-33 23.5-56.5T160-520h415q85 0 164 29t127 98l27 41-223 186q-27 23-60 34.5T542-120H309q-11 18-29 29t-40 11Zm240-440Z'/></svg>
                      $cuisine_type
                    </div>
                  </div>

                  <div class='recipe-tags'>
            ";

            foreach ($dietary_tags as $tag) {
              echo "<span class='recipe-tag cuisine-tag'>" . htmlspecialchars($tag) . "</span>";
            }

            echo "
                  </div>

                  <div class='recipe-actions'>
                    <a class='action-btn view-recipe' href='recipe-detail.php?id=$id'>View Recipe</a>
                  </div>
                </div>
              </div>
            ";
          }
        } else {
          echo "
            <div class='no-results'>
              <h3>No recipes found</h3>
              <p>Try adjusting your filters or search terms to find more recipes.</p>
            </div>
          ";
        }
        ?>
      </div>
    </div>
  </section>

  <?php include "footer.php" ?>
</body>

</html>

<script src="javascript/index.js"></script>