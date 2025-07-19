<?php

include "connection.php";

$search = $_GET['search'] ?? '';
$type = $_GET['type'] ?? '';
$maxSizeBytes = 50 * 1024 * 1024; // 50 MB
$where = [];
$params = [];

if (!empty($search)) {
  $where[] = "m.title LIKE ?";
  $params[] = "%" . $search . "%";
}

if (!empty($type)) {
  $where[] = "m.type = ?";
  $params[] = $type;
}

$where_sql = '';
if (!empty($where)) {
  $where_sql = "WHERE " . implode(" AND ", $where);
}

$sql = "
  SELECT 
    m.id,
    m.title,
    m.description,
    m.status,
    m.type,
    m.size,
    m.url,
    m.uploaded_date
  FROM media AS m
  $where_sql
  GROUP BY m.id, m.title, m.description, m.status, m.type, m.size, m.url
";

$stmt = $connection->prepare($sql);

if (!empty($params)) {
  $types = str_repeat('s', count($params));
  $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
$medias = [];

while ($row = $result->fetch_assoc()) {
  $medias[] = $row;
}

// Create and Edit Media
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["save-media-btn"])) {
  $title = $_POST['title'];
  $description = $_POST['description'];
  $status = $_POST['status'];
  $type = $_POST['type'];

  if (isset($_FILES['media_file']) && $_FILES['media_file']['error'] !== UPLOAD_ERR_NO_FILE) {
    $file = $_FILES['media_file'];
    $fileName = basename($file['name']);
    $fileType = mime_content_type($file['tmp_name']);  // safe now because file exists

    if ($file['size'] > $maxSizeBytes) {
      echo "
        <script>
          alert('File too large. Maximum allowed size is 50 MB!');
          window.location = 'admin-media.php';
        </script>
      ";
      exit;
    }

    switch ($type) {
      case 'pdf':
        $targetDir = "./pdf/";
        break;
      case 'video':
        $targetDir = "./videos/";
        break;
      default:
        $targetDir = "./uploads/media/";
        break;
    }

    if (!is_dir($targetDir)) {
      mkdir($targetDir, 0777, true);
    }

    $targetPath = $targetDir . uniqid() . "_" . $fileName;

    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
      $size = $file['size'];

      // If editing, update record with new file info
      if (!empty($_POST['media_id'])) {
        $media_id = intval($_POST['media_id']);
        $stmt = $connection->prepare("UPDATE media SET title=?, description=?, type=?, size=?, url=?, status=? WHERE id=?");
        $stmt->bind_param("sssissi", $title, $description, $type, $size, $targetPath, $status, $media_id);
        $stmt->execute();
      } else {
        // Adding new media
        $stmt = $connection->prepare("INSERT INTO media (title, description, type, size, url, status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssiss", $title, $description, $type, $size, $targetPath, $status);
        $stmt->execute();
      }

      header("Location: admin-media.php");
      exit;
    } else {
      echo "Error uploading file.";
      exit;
    }

  } else {
    // No new file uploaded - editing existing media, update all except file fields
    if (!empty($_POST['media_id'])) {
      $media_id = intval($_POST['media_id']);
      $stmt = $connection->prepare("UPDATE media SET title=?, description=?, type=?, status=? WHERE id=?");
      $stmt->bind_param("ssssi", $title, $description, $type, $status, $media_id);
      $stmt->execute();

      header("Location: admin-media.php");
      exit;
    } else {
      // Reject or handle as error for no file and no media_id
      echo "
        <script>
          alert('Please upload a media file.');
          window.location = 'admin-media.php';
        </script>
      ";
      exit;
    }
  }
}


// Delete Media
if (isset($_GET['delete'])) {
  $delete_id = intval($_GET['delete']);
  $connection->query("DELETE FROM media WHERE id = $delete_id");
  header("Location: admin-media.php");
  exit;
}

// Format file size
function formatSize($bytes)
{
  if ($bytes >= 1048576) {
    return number_format($bytes / 1048576, 2) . ' MB';
  } elseif ($bytes >= 1024) {
    return number_format($bytes / 1024, 2) . ' KB';
  } else {
    return $bytes . ' B';
  }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/admin.css">
  <title>Media Management</title>
</head>

<body>

  <?php include "admin-sidebar.php"; ?>

  <section class="main-content">
    <div class="header">
      <h1>Media Management</h1>
      <div class="user-info">
        <span>Welcome, Admin</span>
        <div class="user-avatar">A</div>
      </div>
    </div>

    <div class="section">
      <div class="content-section">
        <div class="section-header">
          <h2 class="section-title">Media Management</h2>
          <button class="add-btn" onclick="openMediaModal()">+ Add Media</button>
        </div>
        <div class="section-content">
          <form class="controls">
            <input type="text" name="search" class="search-box" placeholder="Search dietary..."
              value="<?= htmlspecialchars($search) ?>">

            <select name="type" class="filter-select">
              <option value="" <?= $type === '' ? 'selected' : '' ?>>All Type</option>
              <option value="pdf" <?= $type === 'pdf' ? 'selected' : '' ?>>Pdf</option>
              <option value="video" <?= $type === 'video' ? 'selected' : '' ?>>Video</option>
            </select>
            <button type="submit" class="btn-primary">Filter</button>
            <a href="admin-media.php" class="btn-secondary">
              Clear
            </a>
          </form>
          <table class="data-table">
            <thead>
              <tr>
                <th>Title</th>
                <th>Type</th>
                <th>Size</th>
                <th>Uploaded Date</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($medias)): ?>
                <tr>
                  <td colspan="6">No media found.</td>
                </tr>
              <?php else: ?>
                <?php foreach ($medias as $media): ?>
                  <tr>
                    <td style="text-transform: capitalize;">
                      <?= htmlspecialchars($media['title']) ?>
                    </td>
                    <td style="text-transform: capitalize;">
                      <?= htmlspecialchars($media['type']) ?>
                    </td>
                    <td style="text-transform: capitalize;">
                      <?= formatSize($media['size']) ?>
                    </td>
                    <td style="text-transform: capitalize;">
                      <?= htmlspecialchars($media['uploaded_date']) ?>
                    </td>
                    <td>
                      <span
                        class="status-badge status-<?= htmlspecialchars($media['status']) ?>"><?= htmlspecialchars(string: $media['status']) ?></span>
                    </td>
                    <td>
                      <button class="action-btn btn-edit" onclick="editMediaModal(<?= $media['id'] ?>)">Edit</button>
                      <button class="action-btn btn-delete" onclick="deleteMediaModal(<?= $media['id'] ?>)">Delete</button>
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

  <div id="mediaModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title" id="mediaModalTitle">Add New Cuisine</h3>
        <button class="close" onclick="closeModal('mediaModal')">&times;</button>
      </div>
      <div class="modal-body">
        <form id="mediaForm" method="post" action="admin-media.php" enctype="multipart/form-data">
          <input type="hidden" name="media_id" id="media_id">

          <div class="form-group">
            <label class="form-label">Title</label>
            <input type="text" name="title" class="form-input" required>
          </div>

          <div class="form-group">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-input" rows="3"></textarea>
          </div>

          <div class="form-group">
            <label class="form-label">Type</label>
            <select name="type" class="form-select" required>
              <option value="video">Video</option>
              <option value="pdf">PDF</option>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label">Upload File</label>
            <div id="mediaPreview" style="margin-top: 10px;"></div>
            <input type="file" name="media_file" class="form-input" accept="video/*,application/pdf" required
              onchange="handleFileChange(this)">
          </div>

          <div class="form-group">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>

          <div class="form-actions">
            <button type="button" class="btn-secondary" onclick="closeModal('mediaModal')">Cancel</button>
            <input type="submit" class="btn-primary" name="save-media-btn" value="Save Media">
          </div>
        </form>
      </div>
    </div>
  </div>
</body>

</html>

<script>
  let media = <?= json_encode($medias ?? []) ?>;

  function openMediaModal(id = null) {
    const modal = document.getElementById('mediaModal');
    const title = document.getElementById('mediaModalTitle');
    const form = document.getElementById('mediaForm');
    const preview = document.getElementById('mediaPreview');

    if (id) {
      const mediaItem = media.find(m => m.id == id); // assuming `media` is a JS array of existing items
      if (!mediaItem) return;

      title.textContent = 'Edit Media';
      form.media_id.value = mediaItem.id;
      form.title.value = mediaItem.title;
      form.description.value = mediaItem.description;
      form.type.value = mediaItem.type;
      form.status.value = mediaItem.status;

      preview.innerHTML = '';
      if (mediaItem.type === 'video') {
        preview.innerHTML = `<video src="${mediaItem.url}" controls width="300"></video>`;
      } else if (mediaItem.type === 'pdf') {
        preview.innerHTML = `<embed src="${mediaItem.url}" width="300" height="400" type="application/pdf" />`;
      } else if (mediaItem.type === 'image') {
        preview.innerHTML = `<img src="${mediaItem.url}" width="200" />`;
      }

      form.media_file.required = false;
    } else {
      title.textContent = 'Add New Media';
      form.reset();
      form.media_id.value = '';
      document.getElementById('mediaPreview').innerHTML = '';
    }

    modal.style.display = 'block';
    document.body.classList.add('modal-open');
  }

  function closeModal() {
    const modal = document.getElementById("mediaModal");
    const form = document.getElementById("mediaForm");
    const preview = document.getElementById("mediaPreview");

    form.reset();
    preview.innerHTML = '';
    form.media_id.value = '';
    modal.style.display = 'none';
    document.body.classList.remove('modal-open');
  }

  function editMediaModal(id) {
    openMediaModal(id);
  }

  function deleteMediaModal(id) {
    if (confirm("Are you sure you want to delete this media?")) {
      window.location.href = `admin-media.php?delete=${id}`;
    }
  }

  function handleFileChange(input) {
    validateFileSize(input);
    previewMedia(input);
  }

  function validateFileSize(input) {
    const maxSize = 50 * 1024 * 1024; // 50 MB
    const file = input.files[0];

    if (file && file.size > maxSize) {
      alert("File too large. Maximum allowed size is 50 MB.");
      input.value = '';
    }
  }

  function previewMedia(input) {
    const file = input.files[0];
    const preview = document.getElementById('mediaPreview');
    preview.innerHTML = '';

    if (!file) return;

    const type = file.type;

    if (type.startsWith('video/')) {
      const video = document.createElement('video');
      video.src = URL.createObjectURL(file);
      video.controls = true;
      video.width = 300;
      preview.appendChild(video);
    } else if (type === 'application/pdf') {
      const embed = document.createElement('embed');
      embed.src = URL.createObjectURL(file);
      embed.type = "application/pdf";
      embed.width = "300";
      embed.height = "400";
      preview.appendChild(embed);
    } else if (type.startsWith('image/')) {
      const img = document.createElement('img');
      img.src = URL.createObjectURL(file);
      img.width = 200;
      preview.appendChild(img);
    } else {
      preview.innerText = "Preview not available for this file type.";
    }
  }
</script>