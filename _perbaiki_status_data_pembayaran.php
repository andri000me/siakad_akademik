<?php
// Author : SIAKAD TEAM
// Email  : setio.dewo@gmail.com
// Start  : 26/12/2008

session_start();
include_once "sisfokampus.php";
HeaderSisfoKampus("Perbaiki Data Pembayaran Mhsw");

// *** Main ***
TampilkanJudul("Perbaiki Status Data Pembayaran Mahasiswa");
$gos = (empty($_REQUEST['gos']))? "fnKonfirmasi" : $_REQUEST['gos'];
$gos();

/// *** Functions ***
function fnKonfirmasi() {
  echo Konfirmasi("Konfirmasi",
    "Anda akan memperbaiki status detail pembayaran Mhsw.<br />
    Klik tombol di bawah ini untuk memulainya.
    <hr size=1 color=silver />
    <input type=button name='btnProses' value='Proses'
    onClick=\"location='?gos=fnProses'\" />");
}

function fnProses() {
  $s = "select *
    from bayarmhsw
    order by BayarMhswID";
  $r = _query($s);
  
  while ($w = _fetch_array($r)) {
    echo "Check <b>$w[BayarMhswID]</b>. Status: <b>$w[NA].<br />";
    echo "<blockquote>";
    if ($w['NA'] == 'N') CheckApakahDetailnyaDouble($w);
    else BunuhDetailnya($w);
    echo "</blockquote>";
  }
  echo "<p><font size=+1>Selesai</font></p>";
}

function CheckApakahDetailnyaDouble($b) {
  $s = "select * from bayarmhsw2 where BayarMhswID = '$b[BayarMhswID]' order by BIPOTNamaID";
  $r = _query($s);
  $sdh = array();
  while ($w = _fetch_array($r)) {
    // cek apakah sudah ada di array?
    if (array_search($w['BIPOTNamaID'], $sdh) === false) {
      $sdh[] = $w['BIPOTNamaID'];
      $st = ' ~ <b>Pass</b>';
    }
    else {
      $s1 = "update bayarmhsw2 set NA = 'Y' where BayarMhsw2ID = '$w[BayarMhsw2ID]' ";
      $r1 = _query($s1);
      $st = ' ~ <b>Deleted</b>';
    }
    echo "<sup>$w[BIPOTNamaID] - $w[Jumlah] $st</sup><br />";
  }
}

function BunuhDetailnya($w) {
  $s = "update bayarmhsw2 set NA = 'Y' where BayarMhswID='$w[BayarMhswID]' ";
  $r = _query($s);
  echo "<sup>$s</sup><br /><br />";
}
?>
