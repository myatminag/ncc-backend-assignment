<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/style.css">
  <title>Educational Resources</title>
</head>

<body>
  <?php include "cookie-consent.php" ?>

  <?php include "auth.php" ?>

  <?php include 'header.php' ?>

  <section class="hero">
    <div class="hero-content">
      <h1>Renewable Energy Educational Resources</h1>
      <p>
        Explore our comprehensive collection of educational materials on renewable energy. Download guides, view
        interactive infographics, and watch expert videos to deepen your understanding of sustainable energy solutions.
      </p>
    </div>
  </section>

  <section class="section" id="video-tutorials">
    <div class="container">
      <div class="section-header">
        <h2 class="section-title">Educational Video Library</h2>
        <p class="section-subtitle">
          Learn from renewable energy experts through comprehensive video tutorials and documentaries
        </p>
      </div>

      <div class="video-grid">
        <div class="video-card">
          <div class="video-thumbnail">
            <iframe width="100%" height="200" src="https://www.youtube.com/embed/jvYO_peP_No?si=ZfrDHI3aDI-9lzKN"
              title="YouTube video player" frameborder="0"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
              referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
          </div>
          <div class="video-content">
            <h3 class="video-title">Introduction to Solar Energy</h3>
            <p class="video-description">
              A general introduction into the variety of technologies which harvest the energy in solar light and
              convert it into electricity, heat or fuels
            </p>
          </div>
        </div>
        <div class="video-card">
          <div class="video-thumbnail">
            <iframe width="100%" height="200" src="https://www.youtube.com/embed/H_cm_Lvml70?si=EjGba1T_WKK5YTCq"
              title="YouTube video player" frameborder="0"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
              referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
          </div>
          <div class="video-content">
            <h3 class="video-title">Hydroelectric Power Systems</h3>
            <p class="video-description">
              How is hydropower actually generated? Exploration of hydroelectric power generation, from small-scale
              micro-hydro to massive dam projects.
            </p>
          </div>
        </div>
        <div class="video-card">
          <div class="video-thumbnail">
            <iframe width="100%" height="200" src="https://www.youtube.com/embed/j7q653ffQO4?si=K4Q8zDlQ9InNvY80"
              title="YouTube video player" frameborder="0"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
              referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
          </div>
          <div class="video-content">
            <h3 class="video-title">Geothermal Energy: The Basic</h3>
            <p class="video-description">
              Check out this video to learn more about geothermal energy
              systems, heat and cool buildings and ground-source energy applications.
            </p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="section">
    <div class="container">
      <div class="section-header">
        <h2 class="section-title">Interactive Infographics</h2>
        <p class="section-subtitle">Visual guides and data visualizations to help you understand renewable energy
          concepts and statistics.
        </p>
      </div>

      <div class="hacks-grid">
        <div class=" video-card">
          <div class="video-thumbnail" style='background-image: url(./images/renewable-energy.jpg);'>
          </div>
          <div class="video-content">
            <h3 class="video-title">Global Renewable Energy Capacity 2024</h3>
            <p class="video-description">
              Visual breakdown of worldwide renewable energy capacity by technology type and geographic region.
            </p>
          </div>
        </div>
        <div class=" video-card">
          <div class="video-thumbnail" style='background-image: url(./images/solar-cell-efficiency.jpg);'>
          </div>
          <div class="video-content">
            <h3 class="video-title">Solar Panel Efficiency Comparison</h3>
            <p class="video-description">
              Comparative analysis of different solar panel technologies and their efficiency ratings over time.
            </p>
          </div>
        </div>
        <div class=" video-card">
          <div class="video-thumbnail" style='background-image: url(./images/carbon-footprint-reduction.jpg);'>
          </div>
          <div class="video-content">
            <h3 class="video-title">Carbon Footprint Reduction</h3>
            <p class="video-description">
              A carbon footprint infographic visually represents ways to reduce greenhouse gas emissions.
            </p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="section" id="downloads">
    <div class="container">
      <div class="section-header">
        <h2 class="section-title">Downloadable Resources</h2>
        <p class="section-subtitle">Comprehensive guides, reports, and educational materials on renewable energy
          technologies and sustainability practices.
        </p>
      </div>

      <div class="hacks-grid" id="pdfsGrid">
        <div class="download-item">
          <div class="download-icon">☀️</div>
          <h3 class="download-title">Solar Energy Complete Guide</h3>
          <p class="download-description">
            Comprehensive guide covering solar panel technology, installation, maintenance, and cost analysis for
            residential and commercial applications.
          </p>

          <div class="download-stats">
            <span>📄 Pdf</span>
            <span>💾 3.1 MB</span>
          </div>

          <button class="download-action" data-filename="./pdf/solar-energy-guide.pdf">
            📥 Download Now
          </button>
        </div>
        <div class="download-item">
          <div class="download-icon">🏠</div>
          <h3 class="download-title">Home Energy Efficiency</h3>
          <p class="download-description">
            Step-by-step guide to making your home more energy efficient and integrating renewable energy systems
            including insulation, heating and cooling systems.
          </p>

          <div class="download-stats">
            <span>📄 Pdf</span>
            <span>💾 1,794 KB</span>
          </div>

          <button class="download-action" data-filename="./pdf/energy-efficiency-of-your-home.pdf">
            📥 Download Now
          </button>
        </div>
        <div class="download-item">
          <div class="download-icon">🔋</div>
          <h3 class="download-title">Energy Storage Solutions</h3>
          <p class="download-description">
            Comprehensive overview of battery technologies, grid storage, and energy management systems for renewable
            energy.
          </p>

          <div class="download-stats">
            <span>📄 Pdf</span>
            <span>💾 440 KB</span>
          </div>

          <button class="download-action" data-filename="./pdf/energy-storage-solutions.pdf">
            📥 Download Now
          </button>
        </div>
      </div>
    </div>
  </section>

  <?php include 'footer.php' ?>
</body>

</html>

<script src=" javascript/index.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('pdfsGrid').addEventListener('click', function (e) {
      if (e.target.classList.contains('download-action')) {
        const fileUrl = e.target.getAttribute('data-filename');

        const a = document.createElement('a');
        a.href = fileUrl;
        a.download = fileUrl.split('/').pop();
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
      }
    });
  });
</script>