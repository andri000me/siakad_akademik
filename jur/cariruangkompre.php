<?php
// Author : Emanuel Setio Dewo
// Email  : setio.dewo@gmail.com
// Start  : 21 Agustus 2008

session_start();
include_once "../sisfokampus1.php";

HeaderSisfoKampus("Cari Ruang");

// *** Parameters ***
$ProdiID = GetSetVar('ProdiID');
$frm = GetSetVar('frm');
$indexKompre = GetSetVar('indexKompre')+0;
$div = GetSetVar('div');
$RuangID = GetSetVar('RuangID');

// cek Ruangan dulu
if (empty($RuangID))
  die(ErrorMsg('Error', 
    "Masukkan terlebih dahulu Kode Ruang sebagai kata kunci pencarian.<br />
    Hubungi Sysadmin untuk informasi lebih lanjut.
    <hr size=1 color=silver />
    Opsi: <a href='#' onClick=\"javascript:toggleBox('$div', 0)\">Tutup</a>"));


$prd = GetaField('prodi', "KodeID='".KodeID."' and ProdiID", $ProdiID, 'Nama');

// *** Main ***
TampilkanJudul("Cari Ruang - $prd <sup>($ProdiID)</sup><br /><font size=-1><a href='#' onClick=\"toggleBox('$div', 0)\">(&times; Close &times;)</a></font>");
TampilkanDaftar();

// *** Functions ***
function TampilkanDaftar() {
  $s = "select r.RuangID, r.Nama, r.Kapasitas, r.KampusID
    from ruang r
    where r.KodeID = '".KodeID."'
      and r.RuangID like '%$_SESSION[RuangID]%'
      and r.NA = 'N'
      and INSTR(r.ProdiID, '.$_SESSION[ProdiID].') > 0
    order by r.KampusID, r.RuangID";
  $r = _query($s); $i = 0;
  
  echo "<table class=bsc cellspacing=1 width=100%>";
  echo "<tr>
    <th class=ttl>#</th>
    <th class=ttl>Kampus</th>
    <th class=ttl>Kode</th>
    <th class=ttl>Nama Ruang</th>
    <th class=ttl width=60>Kapasitas</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    $i++;
    echo <<<SCR
      <tr>
      <td class=inp width=20>$i</td>
      <td class=ul1 width=100>$w[KampusID]</td>
      <td class=ul1 width=100>$w[RuangID]</td>
      <td class=ul1>
        <a href="javascript:$_SESSION[frm].RuangID$_SESSION[indexKompre].value='$w[RuangID]';toggleBox('$_SESSION[div]', 0)">$w[Nama]</a>
      </td>
      <td class=ul1 align=right>$w[Kapasitas]</td>
      </tr>
SCR;
  }
  echo "</table>";
}

?>

</BODY>
</HTML>
