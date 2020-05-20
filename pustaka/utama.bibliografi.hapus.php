<?php
session_start(); error_reporting(0);
include_once "../sisfokampus2.php";
// *** Parameters ***
$md = $_REQUEST['md']+0;
$id = $_REQUEST['id']+0; // Jika edit, maka gunakan id ini
$bck = $_REQUEST['bck'];

HeaderSisfoKampus("Hapus Bibliografi");

$delete = "Delete from app_pustaka1.biblio where biblio_id='$id'";
$q = _query($delete);
echo $delete;
TutupScript($bck);
function TutupScript($BCK) {
echo <<<SCR
<SCRIPT>
  function ttutup() {
    opener.location='../?mnux=$BCK';
    self.close();
    return false;
  }
  ttutup();
</SCRIPT>
SCR;
}
?>
