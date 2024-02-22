<?php
session_start();

if(isset($_SESSION['username'])) {
  require '../db_conn.php';

  $id = $_SESSION['user_id'];
  $sql = "UPDATE users SET last_seen = NOW() WHERE user_id = ?";

  $stmt = $conn->prepare($sql);
  $stmt->execute([$id]);

  
} else {
  header('Location: ../../index.php');
  exit;
}


 ?>
