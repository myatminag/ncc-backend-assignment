<?php

include "connection.php";

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/style.css">
  <title>Food Fusion</title>
</head>

<body>
  <?php include "cookie-consent.php" ?>

  <?php include "auth.php" ?>

  <?php include "header.php" ?>


  <section class="hero" id="home">
    <div class="hero-content">
      <h1>Welcome to FoodFusion</h1>
      <p>Where culinary traditions meet modern innovation. Join our community of passionate food lovers and
        discover the art of fusion cooking that brings cultures together, one dish at a time.
      </p>
    </div>
  </section>

  <section>
    <div class="container">
      <h2 class="section-title">Featured Recipes & Culinary Trends</h2>
      <div class="recipes-grid">
        <?php
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
          GROUP BY r.id, r.name, r.description, r.recipe_image, r.total_time_minutes, dl.name
          ORDER BY r.id DESC
          LIMIT 6;
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
          echo "<p>No recipes found.</p>";
        }
        ?>
      </div>
    </div>
  </section>

  <section>
    <div class="container">
      <h2 class="section-title">Upcoming Cooking Events</h2>
      <div class="carousel-container">
        <div class="carousel" id="carousel">
          <div class="carousel-slide">
            <h3>🍜 Asian Fusion Workshop</h3>
            <p>Learn to create stunning Asian fusion dishes with Chef Maria. Explore the art of combining
              traditional Asian flavors with modern cooking techniques.</p>
            <div class="event-date">March 15, 2024 | 6:00 PM</div>
          </div>
          <div class="carousel-slide">
            <h3>🥘 Mediterranean Meets Middle East</h3>
            <p>Join Chef Ahmed for an exciting culinary journey through Mediterranean and Middle Eastern
              fusion cuisine. Discover new flavor combinations.</p>
            <div class="event-date">March 22, 2024 | 7:00 PM</div>
          </div>
          <div class="carousel-slide">
            <h3>🌮 Latin-American Fusion Night</h3>
            <p>Experience the vibrant world of Latin-American fusion with Chef Carlos. Learn to create
              innovative dishes that celebrate cultural diversity.</p>
            <div class="event-date">March 29, 2024 | 6:30 PM</div>
          </div>
        </div>
        <button class="carousel-nav prev" onclick="prevSlide()">‹</button>
        <button class="carousel-nav next" onclick="nextSlide()">›</button>
      </div>
      <div class="carousel-dots">
        <span class="dot active" onclick="currentSlide(1)"></span>
        <span class="dot" onclick="currentSlide(2)"></span>
        <span class="dot" onclick="currentSlide(3)"></span>
      </div>
    </div>
  </section>

  <?php include "footer.php" ?>
</body>

</html>

<script src="javascript/index.js"></script>