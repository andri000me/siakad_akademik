<?php
include "../sisfokampus.php";
include "db.mysql.php";
include "connectdb.php";
include "dwo.lib.php";
include "parameter.php";

$kelid = $_REQUEST['MhswID'];
if (!empty($kelid)) TampilkanDaftar();

function TampilkanDaftar() {
  global $kelid;
  $s = "select KDPIN
    from mhsw
    where MhswID = $kelid
      and NA='N'";
    
  $r = _query($s);
  $w = _fetch_array($r);
    if (!empty($w['KDPIN'])) echo "$w[KDPIN]";
    else echo "Belum di Set";
}
?>
