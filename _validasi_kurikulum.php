<?php
// Author : Irvandy Goutama
// Email  : irvandygoutama@gmail.com
// Start  : 20 Januari 2008
// Purp   : Validate the curriculum of the college eye

session_start();
include_once "sisfokampus.php";
HeaderSisfoKampus("Validasi Kurikulum");

// *** Main ***
$gos = (empty($_REQUEST['gos']))? 'KonfirmasiDulu' : $_REQUEST['gos'];
$gos();

// *** Functions ***
function KonfirmasiDulu() { 
  TampilkanJudul("Menvalidasi kurikulum");
  echo Konfirmasi("Konfirmasi Proses",
    "Mengecek kurikulum di tabel mk
    <hr size=1 color=silver />
    <input type=button name='btnProses' value='Mulai Proses'
    onClick=\"location='?gos=fnProses&page=1'\" />"); 
  }
function fnProses() {
  TampilkanJudul("Menvalidasi Kurikulum");
  
  $s = "select * from `mk` where KurikulumID='0' or KurikulumID='' or KurikulumID IS NULL";
  $r = _query($s);
  $x = 0;
  
  echo "<table class=box align=center>";
  echo "<tr align=center colspan=10><th>Tahun yang Kosong</tr>
		<tr><th class=ttl>No.</th>
			<th class=ttl>No. KurikulumID</th>
			<th class=ttl>No. MKID</th>
			<th class=ttl>No. ProdiID</th>
			<th class=ttl>MkKode</th>
			<th class=ttl>Nama</th>
			<th class=ttl>Singkatan</th>
		</tr>";
  while($w=_fetch_array($r))
  {		$x++;
		echo "<tr>
				<td col=ul1>$x</td>
				<td col=ul1>$w[KurikulumID]</td>
				<td col=ul1>$w[MKID]</td> 
				<td col=ul1>$w[ProdiID]</td>	
				<td col=ul1>$w[MKKode]</td>
				<td col=ul1>$w[Nama]</td>
				<td col=ul1>$w[Singkatan]</td>
				</tr>";
  }
  
  echo "</table>";
  }
?>
