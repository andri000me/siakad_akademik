<?php
  // file: connectdb.php
  // author: E. Setio Dewo, Maret 2003

  $db_username = "root";
  $db_hostname = "localhost";
  $db_password = "k4d4l";
  $db_name = "semarang-ukrida";

  $con = _connect($db_hostname, $db_username, $db_password);
  $db  = _select_db($db_name, $con);

?>
