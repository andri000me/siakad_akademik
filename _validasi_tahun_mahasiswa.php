<?php
// Author : Irvandy Goutama
// Email  : irvandygoutama@gmail.com
// Start  : 20 Januari 2008
// Purp   : Validate the years of the students

session_start();
include_once "sisfokampus.php";
HeaderSisfoKampus("Perbaiki Max SKS di KHS");

// *** Main ***
$gos = (empty($_REQUEST['gos']))? 'KonfirmasiDulu' : $_REQUEST['gos'];
$gos();

// *** Functions ***
function KonfirmasiDulu() { 
  TampilkanJudul("Menvalidasi tahun ajaran mahasiswa");
  echo Konfirmasi("Konfirmasi Proses",
    "Mengecek tahun pada tabel mahasiswa
    <hr size=1 color=silver />
    <input type=button name='btnProses' value='Mulai Proses'
    onClick=\"location='?gos=fnProses&page=1'\" />"); 
  }
function fnProses() {
  TampilkanJudul("Menvalidasi Tahun Mahasiswa");
  
  $s = "select * from `krs` where left(MhswID, 4) > '2002' and (TahunID='0' or TahunID='' or TahunID IS NULL)";
  $r = _query($s);
  $x = 0;
  
  echo "<table class=box align=center>";
  echo "<tr align=center colspan=10><th>Tahun yang Kosong</tr>
		<tr><th class=ttl>No.</th>
			<th class=ttl>No. ID</th>
			<th class=ttl>Tahun</th>
			<th class=ttl>MkKode</th>
			<th class=ttl>KrsID</th>
		</tr>";
  while($w=_fetch_array($r))
  {		$x++;
		echo "<tr>
				<td col=ul1>$x</td>
				<td col=ul1>$w[MhswID]</td> 
				<td col=ul1>$w[TahunID]</td>	
				<td col=ul1>$w[MKKode]</td>
				<td col=ul1>$w[KRSID]</td>
				</tr>";
  }
  
  echo "</table>";
  }
?>
