<?php
session_start();

if (isset($_SESSION['username'])) {

  require 'app/db_conn.php';

  require 'app/helpers/user.php';
  require 'app/helpers/conversations.php';
  include 'app/helpers/timeAgo.php';
  require 'app/helpers/last_chat.php';

  $user = getUser($_SESSION['username'], $conn);

  $conversations = getConversation($user['user_id'], $conn);


 ?>


<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Maligram - Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" href="img/logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  </head>
  <body class="d-flex justify-content-center align-items-center vh-100">

    <div class="p-2 w-400 rounded shadow">
      <div class="">
        <div class="d-flex justify-content-between mb-3 p-3 bg-light align-items-center">
          <div class="d-flex align-items-center">
            <img src="uploads/<?=$user['p_p']?>" alt="profile" class="w-25 rounded-circle">
            <h3 class="fs-xs m-2"><?=$user['name']?></h3>
          </div>
          <a class="btn btn-dark" href="logout.php">Logout</a>
        </div>

        <div class="input-group mb-3">
          <input id="searchText" type="text" placeholder="Поиск..." class="form-control">
          <button id="searchBtn" class="btn btn-primary">
            <i class="fa fa-search"></i>
          </button>
        </div>

        <ul id="chatList" class="list-group mvh-50 overflow-auto">
          <?php
          if(!empty($conversations)) { ?>
            <?php
            foreach($conversations as $conversation) { ?>
            <li class="list-group-item">
              <a class="d-flex justify-content-between align-items-center p-2" href="chat.php?user=<?=$conversation['username']?>">
                <div class="d-flex align-items-center">
                  <img class="w-10 rounded-circle" src="uploads/<?=$conversation['p_p']?>" alt="profile">
                  <h3 class="fs-xs m-2">
                    <?=$conversation['name']?><br>
                    <small>
                      <?php
                        echo lastChat($_SESSION['user_id'], $conversation['user_id'], $conn);
                      ?>
                    </small>
                  </h3>
                </div>


                <?php if(last_seen($conversation['last_seen']) == 'Active') { ?>
                  <div title="online">
                    <div class="online">
                    </div>
                  </div>
                <?php } ?>
              </a>
            </li>
            <?php } ?>
        <?php } else { ?>

          <div class="alert alert-info text-center">
            <i class="fa fa-comments d-block fs-big"></i>
            Сообщений пока нет, начните разговор
          </div>

        <?php } ?>


        </ul>

      </div>
    </div>

    <style media="screen">
      .fs-xs {font-size: 1rem;}
      .w-10 {width: 10%;}
      a {text-decoration: none;}
      .fs-big {font-size: 5rem;}
      .online {
        width: 8px;
        height: 8px;
        background: green;
        border-radius: 50%;
       }
    </style>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <script>
    $(document).ready(function(){

      $("#searchText").on("input", function() {
        var searchText = $(this).val();
        if (searchText == '') return;

        $.post('app/ajax/search.php', {
          key: searchText
        },
        function(data, status) {
          $("#chatList").html(data);
        });
      });


      $("#searchBtn").on("click", function() {
        var searchText = $("#searchText").val();
        if (searchText == '') return;

        $.post('app/ajax/search.php', {
          key: searchText
        },
        function(data, status) {
          $("#chatList").html(data);
        });
      });


      let lastSeenUpdate = function() {
        $.get("app/ajax/update_last_seen.php");
      };

      lastSeenUpdate();
      setInterval(lastSeenUpdate, 10000);
    });



    </script>

  </body>
</html>


<?php } else {
  header("Location: index.php");
  exit;
}
?>
