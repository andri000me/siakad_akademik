<?php
// Author: Emanuel Setio Dewo
// 2006-01-05

function DownloadDWOPRN($f) {
  header("Content-Author: Emanuel Setio Dewo");
  header("Content-type: application/dwoprn");
  header("Content-Length: ".filesize($f));
  header("Content-Disposition: attachment; filename=\"print.dwoprn\"");
  header("Content-Description: Download Data");
  header("Content-EQUIV: refresh; URL=\"http://localhost/?\" ");
  readfile($f);
  /*$hnd = fopen($f, "r");
  $isi = fread($hnd, filesize($f));
  fclose($hnd);
  echo $isi;
  */
  //header("location: http://localhost/semarang/?", false);
}
DownloadDWOPRN($_REQUEST['f']);
//echo "<META HTTP-EQUIV=\"refresh\" content=\"5; URL=http://localhost/semarang/$_REQUEST[GODONLOT]\">";
?>
