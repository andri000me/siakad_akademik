<?php

session_start();

  include_once "../dwo.lib.php";
  include_once "../db.mysql.php";
  include_once "../connectdb.php";
  $ID = sqling($_GET['JadwalID']);
  if ($_SESSION['_LevelID']==40 || $_SESSION['_LevelID']==1 || $_SESSION['_Login']=='auth0rized'){
 $s = "UPDATE krs set Final='N' where JadwalID='$ID'";
 $r = _query($s);
  $s = "UPDATE jadwal set Final='N' where JadwalID='$ID'";
 $r = _query($s);
 echo "Jadwal #$ID sudah berhasil dibuka";
  }else{
    echo "Fitur Terkunci";
  }

