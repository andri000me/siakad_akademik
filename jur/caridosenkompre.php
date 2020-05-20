<?php
// Author : Emanuel Setio Dewo
// Email  : setio.dewo@gmail.com
// Start  : 21 Agustus 2008

session_start();
include_once "../sisfokampus1.php";

HeaderSisfoKampus("Cari Dosen");

// *** Parameters ***
$ProdiID = GetSetVar('ProdiID');
$frm = GetSetVar('frm');
$div = GetSetVar('div');
$Nama = GetSetVar('Nama');
$indexKompre = GetSetVar('indexKompre')+0;
// cek Nama Dosen dulu
if (empty($Nama))
  die(ErrorMsg('Error', 
    "Masukkan terlebih dahulu Nama Dosen sebagai kata kunci pencarian.<br />
    Hubungi Sysadmin untuk informasi lebih lanjut.
    <hr size=1 color=silver />
    Opsi: <a href='#' onClick=\"javascript:toggleBox('$div', 0)\">Tutup</a>"));

$prd = GetaField('prodi', "KodeID='".KodeID."' and ProdiID", $ProdiID, 'Nama');

// *** Main ***
TampilkanJudul("Cari Dosen - $prd <sup>($ProdiID)</sup><br /><font size=-1><a href='#' onClick=\"toggleBox('$div', 0)\">(&times; Close &times;)</a></font>");
TampilkanDaftar();

// *** Functions ***
function TampilkanDaftar() {
//echo $ProdiID;
  $s = "select d.Login, d.Nama, d.Gelar, d.NA
    from dosen d
    where d.KodeID = '".KodeID."'
      and d.Nama like '%$_SESSION[Nama]%'
      and INSTR(d.ProdiID, '$_SESSION[ProdiID]') > 0
      and d.NA='N'
    order by d.Nama";
  $r = _query($s); $i = 0;
  
  echo "<table class=bsc cellspacing=1 width=100%>";
  echo "<tr>
    <th class=ttl>#</th>
    <th class=ttl>Kode/NIP</th>
    <th class=ttl>Nama Dosen</th>
    <th class=ttl>NA</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    $i++;
    if ($w['NA'] == 'Y') {
      $c = "class=nac";
      $d = "$w[Nama] <sup>$w[Gelar]</sup>";
    }
    else {
      $c = "class=ul";
      $d = "<a href=\"javascript:$_SESSION[frm].DosenID$_SESSION[indexKompre].value='$w[Login]';$_SESSION[frm].NamaDosen$_SESSION[indexKompre].value='$w[Nama]';toggleBox('$_SESSION[div]', 0)\">
        &raquo;
        $w[Nama]</a>
        <sup>$w[Gelar]</sup>";
    }
    echo <<<SCR
      <tr>
      <td class=inp width=20>$i</td>
      <td $c width=100 align=center>$w[Login]</td>
      <td $c>$d</td>
      <td class=ul width=20 align=center><img src='../img/book$w[NA].gif' /></td>
      </tr>
SCR;
  }
  echo "</table>";
}

?>

</BODY>
</HTML>
