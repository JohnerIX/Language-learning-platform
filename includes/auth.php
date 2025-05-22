<?php
require 'config.php';
if ($_POST['login']) {
  $email = $_POST['email'];
  $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
  $stmt->execute([$email]);
  $user = $stmt->fetch();
  if ($user && password_verify($_POST['password'], $user['password_hash'])) {
    $_SESSION['user_id'] = $user['user_id'];
    header("Location: profile.php");
  }
}
?>