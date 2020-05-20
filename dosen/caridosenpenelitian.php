<?php


session_start();
include_once "../sisfokampus1.php";

HeaderSisfoKampus("Cari Dosen");

// *** Parameters ***
$frm = GetSetVar('frm');
$div = GetSetVar('div');
$Nama = GetSetVar('Nama');
$doseni = GetSetVar('doseni');
$cari = GetSetVar('cari');

// cek Nama Dosen dulu
if (empty($Nama))
  die(ErrorMsg('Error', 
    "Masukkan terlebih dahulu Nama Dosen sebagai kata kunci pencarian.<br />
    Hubungi Sysadmin untuk informasi lebih lanjut.
    <hr size=1 color=silver />
    Opsi: <a href='#' onClick=\"javascript:toggleBox('$div', 0)\">Tutup</a>"));


// *** Main ***
TampilkanJudul("Cari Dosen<br /><font size=-1><a href='#' onClick=\"toggleBox('$div', 0)\">(&times; Close &times;)</a></font>");
TampilkanDaftar();

// *** Functions ***
function TampilkanDaftar() {
//echo $ProdiID;
  $s = "select d.NIDN, d.Nama, d.Gelar1, d.Gelar, concat(d.Gelar1,' ',d.Nama,', ',d.Gelar) as NM, d.NA
    from dosen d
    where d.KodeID = '".KodeID."'
      and d.Nama like '%$_SESSION[Nama]%'
	  and d.NIDN != ''
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
      $d = "$w[Gelar1] $w[Nama] <sup>$w[Gelar]</sup>";
    }
    else {
      $c = "class=ul";
	  if ($_SESSION['cari']=='') {
      $d = "$_GET[doseni]<a href=\"javascript:$_SESSION[frm].NIDN$_SESSION[doseni].value='$w[NIDN]';$_SESSION[frm].Namadosen$_SESSION[doseni].value='$w[NM]';toggleBox('$_SESSION[div]', 0)\">
        &raquo; $w[Gelar1] 
        $w[Nama]</a>
        <sup>$w[Gelar]</sup>";
	  }
	  else {
		  $d = "$_GET[doseni]<a href=\"javascript:$_SESSION[frm].NIDN_Anggota$_SESSION[doseni].value='$w[NIDN]';$_SESSION[frm].Namadosen_Anggota$_SESSION[doseni].value='$w[NM]';toggleBox('$_SESSION[div]', 0)\">
        &raquo; $w[Gelar1] 
        $w[Nama]</a>
        <sup>$w[Gelar]</sup>";
	  }
    }
    echo <<<SCR
      <tr>
      <td class=inp width=20>$i</td>
      <td $c width=100 align=center>$w[NIDN]</td>
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
