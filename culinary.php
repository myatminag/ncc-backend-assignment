<?php

include "connection.php";

$sql = "
  SELECT 
    id, 
    title, 
    description, 
    type, 
    size, 
    url 
    FROM media 
    WHERE type IN ('video', 'pdf') AND status = 'active'
    ORDER BY uploaded_date DESC
";
$result = $connection->query($sql);

$videos = [];
$pdfs = [];

while ($row = $result->fetch_assoc()) {
  if ($row['type'] === 'video') {
    $videos[] = $row;
  } elseif ($row['type'] === 'pdf') {
    $pdfs[] = $row;
  }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/style.css">
  <title>Culinary Resources</title>
</head>

<body>
  <?php include "cookie-consent.php" ?>

  <?php include "auth.php" ?>

  <?php include "header.php" ?>

  <section class="hero">
    <div class="hero-content">
      <h1>Culinary Resources</h1>
      <p>
        Master the art of fusion cooking with our comprehensive collection of downloadable recipe cards, step-by-step
        tutorials, and expert kitchen techniques.
      </p>
    </div>
  </section>

  <section id="video-tutorials">
    <div class="container">
      <div class="section-header">
        <h2 class="section-title">Video Tutorials</h2>
        <p class="section-subtitle">Learn essential cooking techniques and fusion methods from our expert chefs</p>
      </div>

      <div class="video-grid" id="videoGrid">
        <div class="video-card">
          <div class="video-thumbnail">
            <iframe width="100%" height="200" src="https://www.youtube.com/embed/YrHpeEwk_-U?si=i52CsOKFiJa9spgt"
              title="YouTube video player" frameborder="0"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
              referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
          </div>
          <div class="video-content">
            <h3 class="video-title">9 Essential Knife Skills To Master</h3>
            <p class="video-description">
              From chopping and cubing to slicing and dicing, Frank explains it all step-by-step so you can achieve
              results you never
              thought possible in your own kitchen.
            </p>
            <div style="margin-top: 0.5rem; color: #666; font-size: 0.9rem;">
              Instructor: Chef Frank Proto
            </div>
          </div>
        </div>
        <div class="video-card">
          <div class="video-thumbnail">
            <iframe width="100%" height="200" src="https://www.youtube.com/embed/xniS7kMpW4I?si=Bqit69OacxR7DCc1"
              title="YouTube video player" frameborder="0"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
              referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
          </div>
          <div class="video-content">
            <h3 class="video-title">Sauce Making Mastery</h3>
            <p class="video-description">
              Mastering these 5 mother sauces is the ultimate power move for any aspiring chef or home cook. From
              béchamel to hollandaise, these classic sauces
            </p>

            <div style="margin-top: 0.5rem; color: #666; font-size: 0.9rem;">
              Instructor: Chef Auguste Escoffier
            </div>
          </div>
        </div>
        <div class="video-card">
          <div class="video-thumbnail">
            <iframe width="100%" height="200" src="https://www.youtube.com/embed/T2leakA9Uo8?si=2SEU2rvc_XTN_BX-"
              title="YouTube video player" frameborder="0"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
              referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
          </div>
          <div class="video-content">
            <h3 class="video-title">Plating and Presentation</h3>
            <p class="video-description">
              How to expertly plate your food at home. From choosing the right plate to the importance of
              highlighting the key ingredients, follow Ann’s steps to plate like a pro.
            </p>

            <div style="margin-top: 0.5rem; color: #666; font-size: 0.9rem;">
              Instructor: Chef Ann Ziata
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section>
    <div class="container">
      <div class="section-header">
        <h2 class="section-title">Free Downloads</h2>
        <p class="section-subtitle">Essential cooking guides, measurement charts, and reference materials for your
          kitchen</p>
      </div>

      <div class="recipes-grid" id="downloadGrid">
        <?php foreach ($pdfs as $pdf): ?>
          <div class="download-item">
            <div class="download-icon">📄</div>
            <h3 class="download-title"><?= htmlspecialchars($pdf['title']) ?></h3>
            <p class="download-description"><?= htmlspecialchars($pdf['description']) ?></p>
            <div class="download-stats">
              <span>📄 PDF</span>
              <span>💾 <?= number_format($pdf['size'] / 1024, 2) ?> KB</span>
            </div>
            <button download class="download-action" data-filename="<?= htmlspecialchars($pdf['url']) ?>">
              📥 Download Now
            </button>
          </div>
        <?php endforeach; ?>
      </div>

      <div class="recipes-grid" id="downloadGrid">
        <?php foreach ($videos as $video): ?>
          <div class="video-card">
            <div class="video-thumbnail">
              <video width="100%" height="auto" controls>
                <source src="<?= htmlspecialchars($video['url']) ?>" type="video/mp4">
                Your browser does not support the video tag.
              </video>
            </div>
            <div class="video-content">
              <h3 class="download-title"><?= htmlspecialchars($video['title']) ?></h3>
              <p class="video-description"><?= htmlspecialchars($video['description']) ?></p>
              <button download class="download-action" data-filename="<?= htmlspecialchars($video['url']) ?>">
                📥 Download Now
              </button>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section id="kitchen-hacks">
    <div class="container">
      <div class="section-header">
        <h2 class="section-title">Kitchen Hacks & Tips</h2>
        <p class="section-subtitle">Discover time-saving techniques and clever tricks to elevate your cooking game</p>
      </div>

      <div class="recipes-grid" id="hacksGrid"></div>
    </div>
  </section>

  <?php include "footer.php" ?>
</body>

</html>
<script src="javascript/index.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    displayKitchenHacks();
  });

  document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('downloadGrid').addEventListener('click', function (e) {
      if (e.target.classList.contains('download-action')) {
        const fileUrl = e.target.getAttribute('data-filename');

        if (fileUrl) {
          const a = document.createElement('a');
          a.href = fileUrl;
          a.download = fileUrl.split('/').pop(); // extracts the filename
          document.body.appendChild(a);
          a.click();
          document.body.removeChild(a);
        }
      }
    });
  });

  function displayKitchenHacks() {
    const grid = document.getElementById('hacksGrid');
    grid.innerHTML = kitchenHacks.map(hack => `
        <div class="hack-card">
            <div class="hack-icon">${hack.icon}</div>
            <h3 class="hack-title">${hack.title}</h3>
            <p class="hack-description">${hack.description}</p>
            <div class="hack-category">${hack.category}</div>
        </div>
    `).join('');
  }


  const kitchenHacks = [
    {
      icon: "🧄",
      title: "Quick Garlic Peeling",
      description: "Shake garlic cloves in a jar for 30 seconds to remove skins effortlessly. Perfect for preparing large quantities for fusion marinades.",
      category: "Prep Hacks"
    },
    {
      icon: "🌶️",
      title: "Spice Level Control",
      description: "Add dairy or coconut milk to tone down spicy dishes. Use sugar or honey to balance heat in fusion sauces.",
      category: "Flavor Balance"
    },
    {
      icon: "🥘",
      title: "One-Pan Fusion Cooking",
      description: "Layer ingredients by cooking time - start with proteins, add vegetables, finish with delicate herbs and spices.",
      category: "Cooking Technique"
    },
    {
      icon: "❄️",
      title: "Herb Preservation",
      description: "Freeze fresh herbs in olive oil using ice cube trays. Perfect for adding instant flavor to fusion dishes.",
      category: "Storage Tips"
    },
    {
      icon: "🔥",
      title: "Perfect Rice Every Time",
      description: "Use the absorption method: 1:1.5 rice to water ratio, bring to boil, then simmer covered for 18 minutes.",
      category: "Cooking Basics"
    },
    {
      icon: "🥄",
      title: "Flavor Layering",
      description: "Build flavors in stages - aromatics first, then spices, proteins, and finish with fresh elements.",
      category: "Technique"
    },
    {
      icon: "🧊",
      title: "Quick Marinade Trick",
      description: "Use a fork to pierce meat before marinating for faster flavor absorption in fusion dishes.",
      category: "Prep Hacks"
    },
    {
      icon: "🍋",
      title: "Citrus Maximization",
      description: "Roll citrus fruits before juicing and microwave for 10 seconds to extract maximum juice.",
      category: "Prep Hacks"
    },
    {
      icon: "🥒",
      title: "Vegetable Prep Shortcut",
      description: "Cut vegetables uniformly for even cooking. Use a mandoline for consistent thickness in fusion salads.",
      category: "Prep Hacks"
    }
  ];
</script>