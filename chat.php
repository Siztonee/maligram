<?php
session_start();

if (isset($_SESSION['username'])) {

  require 'app/db_conn.php';

  require 'app/helpers/user.php';
  require 'app/helpers/timeAgo.php';
  require 'app/helpers/chat.php';
  require 'app/helpers/opened.php';




  if (!isset($_GET['user'])) {
    header('Location: home.php');
    exit;
  }

  $chatWith = getUser($_GET['user'], $conn);

  if (empty($chatWith)) {
    header('Location: home.php');
    exit;
  }

  $chats = getChats($_SESSION['user_id'], $chatWith['user_id'], $conn);

  opened($chatWith['user_id'], $conn, $chats);

?>


<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Maligram - Chat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" href="img/logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  </head>
  <body class="d-flex justify-content-center align-items-center vh-100">


    <div class="w-400 shadow p-4 rounded">
      <a class="fs-4 lik-dark" href="home.php">&#8592;</a>

      <div class="d-flex align-items-center">
        <img class="w-15 rounded-circle" src="uploads/<?=$chatWith['p_p']?>" alt="profile">
        <h3 class="dislpay-4 fs-sm m-2"><?=$chatWith['name']?> <br>
          <div class="d-flex align-items-center" title="online">

          <?php
            if (last_seen($chatWith['last_seen']) == 'Active') {
          ?>
            <div class="online"></div>
            <small class="d-block p-1">Online</small>
          <?php } else {?>
            <small class="d-block p-1">Был(-а) <?=last_seen($chatWith['last_seen'])?></small>
          <?php } ?>
          </div>
        </h3>
      </div>

      <div class="shadow p-4 rounded d-flex flex-column mt-2 chat-box" id="chatBox">
        <?php
        if (!empty($chats)) {
          foreach ($chats as $chat) {
            if ($chat['from_id'] == $_SESSION['user_id']) {
        ?>

        <p class="rtext align-self-end border rounded p-2 mb-1">
          <?=$chat['message']?>
          <small class="d-block"><?=$chat['created_at']?></small>
        </p>

      <?php } else { ?>

        <p class="ltext border rounded p-2 mb-1">
          <?=$chat['message']?>
          <small class="d-block"><?=$chat['created_at']?></small>
        </p>

      <?php
        }
      }
    } else { ?>

      <div class="alert alert-info text-center">
        <i class="fa fa-comments d-block fs-big"></i>
        Сообщений пока нет, начните разговор
      </div>

    <?php } ?>
      </div>
      <div class="input-group mb-3">
        <textarea id="message" class="form-control" cols="80"></textarea>
        <button id="sendBtn" class="btn btn-primary">
          <i class="fa fa-paper-plane"></i>
        </button>
      </div>

    </div>



    <style media="screen">
      <?require 'css/style.css';?>
    </style>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <script>
    var scrollDown = function () {
      let chatBox = document.getElementById('chatBox');
      chatBox.scrollTop = chatBox.scrollHeight;
    }

    scrollDown();

    $(document).ready(function(){

      $('#sendBtn').on('click', function() {
        message = $('#message').val();
        if (message == '') return;

        $.post("app/ajax/insert.php", {
          message: message,
          to_id: <?=$chatWith['user_id']?>
        },
        function(data, status) {
          $("#message").val("");
          $("#chatBox").append(data);
          scrollDown();
        });
      });

      let lastSeenUpdate = function() {
        $.get("app/ajax/update_last_seen.php");
      };

      lastSeenUpdate();
      setInterval(lastSeenUpdate, 10000);


      let fechData = function() {
        $.post("app/ajax/getMessage.php", {
          id_2: <?=$chatWith['user_id'];?>
        },

        function(data, status) {
          $("#chatBox").append(data);
          if (data != "") scrollDown();
          });

      }

      fechData();
      setInterval(fechData, 500);
    });


    </script>


  </body>
</html>


<?php
} else {
  header('Location: ../../index.php');
  exit;
}
 ?>
