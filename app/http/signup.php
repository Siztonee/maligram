<?php

if(isset($_POST['name']) && isset($_POST['username']) && isset($_POST['password'])) {

  require '../db_conn.php';

  $name = $_POST['name'];
  $username = $_POST['username'];
  $password = $_POST['password'];

  $data = 'name='.$name.'&username='.$username;

  if(empty($name)) {
    $em = 'Поле Name не заполнено';
    header("Location: ../../signup.php?error=$em&$data");
    exit;
  } elseif (empty($username)) {
    $em = 'Поле Username не заполнено';
    header("Location: ../../signup.php?error=$em&$data");
    exit;
  } elseif (empty($password)) {
    $em = 'Поле Password не заполнено';
    header("Location: ../../signup.php?error=$em&$data");
    exit;
  } else {

      $sql = "SELECT username FROM users WHERE username=?";
      $stmt = $conn->prepare($sql);
      $stmt->execute([$username]);

      if ($stmt->rowCount() > 0) {

        $em = "Имя пользователя ($username) уже используется";
        header("Location: ../../signup.php?error=$em&$data");
        exit;

      } else {
        if(isset($_FILES['pp'])) {

          $img_name = $_FILES['pp']['name'];
          $tmp_name = $_FILES['pp']['tmp_name'];
          $error = $_FILES['pp']['error'];

          if($error === 0) {

            $img_ex = pathinfo($img_name, PATHINFO_EXTENSION);
            $img_ex_lc = strtolower($img_ex);

            $allowed_exs = ['jpg', 'jpeg', 'img', 'png'];

            if(in_array($img_ex_lc, $allowed_exs)) {

              $new_img_name = $username . '.' . $img_ex_lc;
              $img_upload_path = '../../uploads/' . $new_img_name;

              move_uploaded_file($tmp_name, $img_upload_path);

            } else {
              $em = 'Некорректный тип файла';
              header("Location: ../../signup.php?error=$em&$data");
              exit;
            }

          } 
        }


        $password = password_hash($password, PASSWORD_DEFAULT);

        if(isset($new_img_name)) {

          $sql = "INSERT INTO users (name, username, password, p_p) VALUES (?,?,?,?)";
          $stmt = $conn->prepare($sql);
          $stmt->execute([$name, $username, $password, $new_img_name]);

        } else {

          $sql = "INSERT INTO users (name, username, password) VALUES (?,?,?)";
          $stmt = $conn->prepare($sql);
          $stmt->execute([$name, $username, $password]);

        }

        $sm = 'Аккаунт успешно зарегистрирован';
        header("Location: ../../index.php?success=$sm");
        exit;
      }

  }

} else {
  header("Location: ../../signup.php");
  exit;
}


?>
