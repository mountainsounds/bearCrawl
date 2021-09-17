<?php
  ob_start();

  try {
    $con = new PDO("mysql:dbname=google-clone;host=localhost", "root", "");
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERMODE_WARNING);
  }
  catch(PDOExeption $e) {
    echo "Connection failed: " . $e->getMessage();
  }
?>