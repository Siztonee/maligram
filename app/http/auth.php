<?php
session_start();


if(isset($_POST['username']) && isset($_POST['password'])) {

  require '../db_conn.php';

  $username = $_POST['username'];
  $password = $_POST['password'];


  if (empty($username)) {
    $em = 'Поле Username не заполнено';
    header("Location: ../../index.php?error=$em");
  } elseif (empty($password)) {
    $em = 'Поле Password не заполнено';
    header("Location: ../../index.php?error=$em");
  } else {

    $sql = "SELECT * FROM users WHERE username=?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$username]);

    if($stmt->rowCount() === 1) {
      $user = $stmt->fetch();

      if($user['username'] === $username) {

        if(password_verify($password, $user['password'])) {

          $_SESSION['username'] = $user['username'];
          $_SESSION['name'] = $user['name'];
          $_SESSION['user_id'] = $user['user_id'];

          header("Location: ../../home.php");

        } else {
          $em = 'Неверный логин или пароль';
          header("Location: ../../index.php?error=$em");
        }

      } else {
        $em = 'Неверный логин или пароль';
        header("Location: ../../index.php?error=$em");
      }

    }


  }

} else {
  header("Location: ../../index.php");
  exit;
}



 ?>
