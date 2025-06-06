<?php
function getCourses($language = null) {
  global $conn;
  $sql = "SELECT * FROM courses" . ($language ? " WHERE language = ?" : "");
  $stmt = $conn->prepare($sql);
  $stmt->execute($language ? [$language] : []);
  return $stmt->fetchAll();
}

// Helper function to format file sizes
function formatFileSize($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' bytes';
    }
}
?>