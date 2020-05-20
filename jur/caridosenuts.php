<?php

session_start();
include_once "../sisfokampus1.php";

HeaderSisfoKampus("Cari Dosen");

// *** Parameters ***
$ProdiID = GetSetVar('ProdiID');
$frm = GetSetVar('frm');
$div = GetSetVar('div');
$Ruangan = GetSetVar('UR');
$Nama = GetSetVar('Nama');
	$Tanggal = $_GET[Tanggal];
  	$Mulai = $_GET[Mulai];
  	$Selesai = $_GET[Selesai];
  	$Tahun = $_GET[Tahun];

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
  $s = "select Login, d.Nama, d.Gelar, d.NA
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
	<th class=ttl>Pilih</th>
    <th class=ttl>Ruang</th>
	<th class=ttl>MK</th>
    </tr>";
    while ($w = _fetch_array($r)) {
	$i++;
	$s8="select count(j.DosenID) as JmlDosen,
		left(j.RuangID,6) as RuangID, 
		left(ja.Nama,8) as Nama
    from jadwaluts j,jadwal ja
	  where 
      j.DosenID = '$w[Login]'
	  and ja.JadwalID=j.JadwalID
      and j.Tanggal = '$_GET[Tanggal]'
	  and j.TahunID = '$_GET[Tahun]'
	  and (('$_GET[Mulai]:00' <= j.JamMulai and j.JamMulai <= '$_GET[Selesai]:59')
      or  ('$_GET[Mulai]:00' <= j.JamSelesai and j.JamSelesai <= '$_GET[Selesai]:59'))
      ";
	$r8 = _query($s8);
	$w8= mysql_num_rows($r8);
	while ($w9 = _fetch_array($r8)) {
		$Ruang = $w9['RuangID'];
		$MK = $w9['Nama'];
		if (($w9['JmlDosen']>0) and ($w9['JmlDosen']<3))
		{

			if ($w['NA'] == 'Y') {
			  $c = "class=nac";
			  $d = "dosen tidak aktif";
			}
    		else {
			  $c = "class=ul bgcolor=yellow";
			  $d = "<font color=yellow>
			  <a href=\"javascript:$_SESSION[frm].UTSDosenID$_SESSION[UR].value='$w[Login]'; $_SESSION[frm].UTSDosen$_SESSION[UR].value='$w[Nama]';toggleBox('$_SESSION[div]', 0)\">
				Pilih</a></font>
				";
				
			}
	}
	elseif ($w9['JmlDosen']>2)
		{

			if ($w['NA'] == 'Y') {
			  $c = "class=nac";
			  $d = "dosen tidak aktif";
			}
			else {
			  $c = "class=ul bgcolor=pink";
			  $d = "<font color=white>sdh ada jadwal mengawas $w9[JmlDosen]</font>";
			}
	}
	    		elseif ($w9['JmlDosen']==0) {
			  $c = "class=ul";
			  $d = "
			  <a href=\"javascript:$_SESSION[frm].UTSDosenID$_SESSION[UR].value='$w[Login]'; $_SESSION[frm].UTSDosen$_SESSION[UR].value='$w[Nama]';toggleBox('$_SESSION[div]', 0)\">
				Pilih</a></font>
				";
			}
	} // while $w9

    echo <<<SCR
      <tr>
      <td class=inp width=20>$i</td>
      <td $c width=100 align=center>$w[Login]</td>
	  <td $c>$w[Nama]<sup>$w[Gelar]</sup>
      <td $c width=200 align=center>$d</td>
      <td class=ul width=20 align=center>$Ruang</td>
	  <td class=ul align=center>$MK</td>
      </tr>
SCR;
  }
  echo "</table>";
}

?>

</BODY>
</HTML>
