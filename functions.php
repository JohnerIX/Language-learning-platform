<?php
function getCourses($language = null) {
  global $conn;
  $sql = "SELECT * FROM courses" . ($language ? " WHERE language = ?" : "");
  $stmt = $conn->prepare($sql);
  $stmt->execute($language ? [$language] : []);
  return $stmt->fetchAll();
}
?>