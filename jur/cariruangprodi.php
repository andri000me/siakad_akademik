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
$div = GetSetVar('div');
$RuangID = GetSetVar('RuangID');
$HariID = GetSetVar('HariID');
$TahunID = GetSetVar('TahunID');
$JamMulai = GetSetVar('JamMulai');
$JamSelesai = GetSetVar('JamSelesai');

// cek Ruangan dulu
if (empty($RuangID))
  die(ErrorMsg('Error', 
    "Masukkan terlebih dahulu Kode Ruang sebagai kata kunci pencarian.<br />
    Hubungi Sysadmin untuk informasi lebih lanjut.
    <hr size=1 color=silver />
    Opsi: <a href='#' onClick=\"javascript:toggleBox('$div', 0)\">Tutup</a>"));


$prd = GetaField('prodi', "KodeID='".KodeID."' and ProdiID", $ProdiID, 'Nama');

// *** Main ***
TampilkanJudul("Cari Ruang - $prd <sup>($ProdiID)</sup><br /><font size=-1><a href='#' onClick=\"toggleBox('cariruang', 0)\">(&times; Close &times;)</a></font>");
TampilkanDaftar();

// *** Functions ***
function TampilkanDaftar() {
$ProdiID = GetSetVar('ProdiID');
$frm = GetSetVar('frm');
$div = GetSetVar('div');
$RuangID = GetSetVar('RuangID');
$HariID = GetSetVar('HariID');
$TahunID = GetSetVar('TahunID');
$JamMulai = GetSetVar('JamMulai');
$JamSelesai = GetSetVar('JamSelesai');
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
    <th class=ttl>Kode</th>
    <th class=ttl>Nama Ruang</th>
	<th class=ttl>Dipakai? <sup>pada jam yg sama</sup></th>
    <th class=ttl width=60>Kapasitas</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    $i++;
	$induk = GetaField('ruang',"RuangID",$w[RuangID],'RuangInduk');
	$s9 = "select j.JadwalID, j.MKKode, j.Nama, j.JamMulai, j.JamSelesai, j.DosenID, j.SKS,
    j.ProdiID, j.ProgramID, j.RuangID,
    d.Nama as NamaDosen, j.JenisJadwalID,
    p.Nama as _PRG, pr.Nama as _PRD, jn.Nama as JJG
    from jadwal j
      left outer join dosen d on d.Login = j.DosenID and d.KodeID = '".KodeID."'
      left outer join program p on p.ProgramID = j.ProgramID and p.KodeID = '".KodeID."'
      left outer join prodi pr on pr.ProdiID = j.ProdiID and pr.KodeID = '".KodeID."'
	  left outer join jenjang jn on jn.JenjangID = pr.JenjangID 
	  left outer join ruang r on r.RuangID = j.RuangID
    where j.TahunID = '$TahunID'
      and r.RuangInduk = '$induk'
	  and j.RuangID != '$RuangID'
      and j.HariID = '$HariID'
      and (('$JamMulai:00' <= j.JamMulai and j.JamMulai <= '$JamSelesai:59')
      or  ('$JamMulai:00' <= j.JamSelesai and j.JamSelesai <= '$JamSelesai:59'))
      and j.KodeID='".KodeID."'
	  and j.NA = 'N'";
	$r9= _query($s9);
	$mk = "<ul>";
	while ($w9 = _fetch_array($r9)) {
	$mk .= "<li><b>$w9[_PRD] $w9[JJG]</b> $w9[Nama] ($w9[RuangID])</li>";
	}
	$mk .="</ul>";
    echo <<<SCR
      <tr>
      <td class=inp width=20>$i</td>
      <td class=ul1 width=100 align='center'>$w[RuangID]</td>
      <td class=ul1>
        <a href="javascript:$_SESSION[frm].RuangID.value='$w[RuangID]';$_SESSION[frm].Kapasitas.value='$w[Kapasitas]';$_SESSION[frm].Kapasitas2.value='$w[Kapasitas]';toggleBox('cariruang', 0)">$w[Nama]</a>
      </td>
	  <td class=ul1>$mk</td>
      <td class=ul1 align=right>$w[Kapasitas]</td>
      </tr>
SCR;
  }
  echo "</table>";
}
// Cari ruang yang berisi pada jadwal yang sama
$s9 = "select j.RuangID
       from jadwal j
      left outer join dosen d on d.Login = j.DosenID and d.KodeID = '".KodeID."'
      left outer join program p on p.ProgramID = j.ProgramID and p.KodeID = '".KodeID."'
      left outer join prodi pr on pr.ProdiID = j.ProdiID and pr.KodeID = '".KodeID."'
	  left outer join jenjang jn on jn.JenjangID = pr.JenjangID 
	  left outer join ruang r on r.RuangID = j.RuangID
    where j.TahunID = '$TahunID'
	  and j.RuangID != '$RuangID'
      and j.HariID = '$HariID'
      and (('$JamMulai:00' <= j.JamMulai and j.JamMulai <= '$JamSelesai:59')
      or  ('$JamMulai:00' <= j.JamSelesai and j.JamSelesai <= '$JamSelesai:59'))
      and j.KodeID='".KodeID."'
	  and j.NA = 'N'";
	$r9= _query($s9);
	$whr = array();
	while ($w9 = _fetch_array($r9)) {
	$whr[]= "RuangID != '$w9[RuangID]'";
	}
	$_whr = implode(' and ', $whr);
 	$_whr = (empty($_whr))? '' : ' and ' . $_whr;

// Cari ruang yang tidak sama dengan ruang tadi
	$induk = GetaField('ruang',"RuangID",$RuangID,'RuangInduk');
	$s8 = "Select RuangID,Kapasitas from ruang where RuangInduk=RuangID And RuangID != '$RuangID' $_whr order by RuangID";
	$r8 = _query($s8);
	echo "<br><sub>*/ Berikut adalah ruang kosong pada hari yang sama dari jam $JamMulai sampai $JamSelesai</sub><br><ul>";
	echo "<table class=box><tr bgcolor=lightgrey><td class=ul1><b>Nama Ruang</td><td class=ul1><b>Kapasitas</td></tr>";
	while ($w8 = _fetch_array($r8)) {
	echo "<tr><td class=ul1>$w8[RuangID]</td><td class=ul1 align=center>$w8[Kapasitas]</td></tr>";
	}
	echo "</table>";
?>

</BODY>
</HTML>
