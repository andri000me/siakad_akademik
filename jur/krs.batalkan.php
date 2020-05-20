<?php
session_start();
if ($_SESSION['_LevelID']==60) {
  include_once "../dwo.lib.php";
  include_once "../db.mysql.php";
  include_once "../connectdb.php";
  include_once "../parameter.php";
  include_once "../cekparam.php";
  include_once "../header_pdf.php";

// *** Parameters ***
  
    $MhswID = sqling($_POST['MhswID']);
    $TahunID = sqling($_POST['TahunID']);
    $s = "DELETE from khs where MhswID='$MhswID' and TahunID='$TahunID'";
    $r = _query($s);

    //die($s.$_POST['MhswID']);

  header('location:../../?mnux='.$_SESSION['mnux']);
}
else  header('location:../../?mnux='.$_SESSION['mnux']);
?>