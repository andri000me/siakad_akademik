<?php
// Author : Emanuel Setio Dewo
// Email  : setio.dewo@gmail.com
// Start  : 21 Agustus 2008

session_start();
include_once "../sisfokampus1.php";

HeaderSisfoKampus("Cari Mahasiswa");

// *** Parameters ***
$ProdiID = GetSetVar('ProdiID');
$frm = GetSetVar('frm');
$div = GetSetVar('div');
$NamaMhsw = GetSetVar('NamaMhsw');

// cek Nama Dosen dulu
if (empty($NamaMhsw))
  die(ErrorMsg('Error', 
    "Masukkan terlebih dahulu Nama Mahasiswa sebagai kata kunci pencarian.<br />
    Hubungi Sysadmin untuk informasi lebih lanjut.
    <hr size=1 color=silver />
    Opsi: <a href='#' onClick=\"javascript:toggleBox('$div', 0)\">Tutup</a>"));


$prd = GetaField('prodi', "KodeID='".KodeID."' and ProdiID", $ProdiID, 'Nama');

// *** Main ***
TampilkanJudul("Cari Mahasiswa - $prd <sup>($ProdiID)</sup><br /><font size=-1><a href='#' onClick=\"toggleBox('$div', 0)\">(&times; Close &times;)</a></font>");
TampilkanDaftar();

// *** Functions ***
function TampilkanDaftar() {
  $s = "select m.MhswID, m.Nama as NamaMhsw, m.TahunID, m.NA
    from mhsw m
    where m.KodeID = '".KodeID."'
      and m.NA = 'N'
      and m.Nama like '%$_SESSION[NamaMhsw]%'
      and m.ProdiID = '$_SESSION[ProdiID]'
    order by m.Nama";
  $r = _query($s); $i = 0;
  
  echo "<table class=bsc cellspacing=1 width=100%>";
  echo "<tr>
    <th class=ttl>#</th>
    <th class=ttl>NIM</th>
    <th class=ttl>Nama Mahasiswa</th>
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
      $d = "<a href=\"javascript:$_SESSION[frm].MhswID.value='$w[MhswID]';$_SESSION[frm].NamaMhsw.value='$w[NamaMhsw]';toggleBox('$_SESSION[div]', 0)\">
        &raquo;
        $w[NamaMhsw]</a>
        <sup>$w[Gelar]</sup>";
    }
    echo <<<SCR
      <tr>
      <td class=inp width=20>$i</td>
      <td $c width=100 align=center>$w[MhswID]</td>
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
