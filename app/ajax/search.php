<?php
session_start();

if (isset($_SESSION['username'])) {
  if (isset($_POST['key'])) {
    require '../db_conn.php';

    $key = "%{$_POST['key']}%";
    $sql = "SELECT * FROM users WHERE username LIKE ? OR name LIKE ?";

    $stmt = $conn->prepare($sql);
    $stmt-> execute([$key, $key]);

    if ($stmt->rowCount() > 0) {
      $users = $stmt->fetchAll();

      foreach ($users as $user) {
        if ($user['user_id'] == $_SESSION['user_id']) continue;
      ?>

      <li class="list-group-item">
        <a class="d-flex justify-content-between align-items-center p-2" href="chat.php?user=<?=$user['username']?>">
          <div class="d-flex align-items-center">
            <img class="w-10 rounded-circle" src="uploads/<?=$user['p_p']?>" alt="profile">
            <h3 class="fs-xs m-2"><?=$user['name']?></h3>
          </div>
        </a>
      </li>

  <?php }  } else { ?>

      <div class="alert alert-info text-center">
        <i class="fa fa-user-times d-block fs-big"></i>
        Пользователь "<?=htmlspecialchars($_POST['key'])?>" не найден.
      </div>

      <?php
    }
  }


} else {
  header('Location: ../../index.php');
  exit;
}
 ?>
