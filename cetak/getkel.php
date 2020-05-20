<?php
include "../sisfokampus.php";
include "db.mysql.php";
include "connectdb.php";
include "dwo.lib.php";
include "parameter.php";

$kelid = $_REQUEST['KelompokID'];
if (!empty($kelid)) TampilkanDaftar();

function TampilkanDaftar() {
  global $kelid;
  $s = "select MasaKomersil, MasaFiskal, ProsentaseKomersil, ProsentaseFiskal
    from kelompokasset
    where KelompokID = $kelid
      and NA='N'";
    
  $r = _query($s);
  $w = _fetch_array($r);
    echo "$w[MasaKomersil]|$w[MasaFiskal]|$w[ProsentaseKomersil]|$w[ProsentaseFiskal]";
}
?>
