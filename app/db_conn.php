<?php

  $sName = 'localhost';
  $uName = 'root';
  $pass = 'root';
  $dbname = 'fourth_data';


  try {
    $conn = new PDO("mysql:host=$sName;dbname=$dbname", $uName, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->exec("SET CHARACTER SET utf8");
  } catch (PDOException $e) {
    echo "Connection failed : ". $e->getMessage();
  }




 ?>
