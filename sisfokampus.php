<?php
// Author: SIAKAD TEAM
// 25 May 2006
// Kenaikan Yesus Kristus ke Surga
// Damai dan sejahtera beserta kita semua

function HeaderSisfoKampus($title='') {
  include_once "db.mysql.php";
  include_once "connectdb.php";
  include_once "dwo.lib.php";
  include_once "parameter.php";
 
  echo "<HTML xmlns=\"http://www.w3.org/1999/xhtml\">
  <HEAD><TITLE>$title</TITLE>
  <META content=\"SIAKAD TEAM\" name=\"author\">
  <META content=\"Sisfo Kampus\" name=\"description\">
  <link href=\"themes/default/index.css\" rel=\"stylesheet\" type=\"text/css\">
  ";
}
?>
<script src='../putiframe.js' language='javascript' type='text/javascript'></script>
