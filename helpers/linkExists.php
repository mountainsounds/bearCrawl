<?php
  include("../config.php");

  function linkExists($url) {
    global $con;

    $query = $con->prepare("SELECT * FROM sites WHERE url = :url");

    $query->bindParam(":url", $url);
    $query->execute();
    return $query->rowCount();
  }
?>